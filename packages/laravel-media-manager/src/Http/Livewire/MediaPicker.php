<?php

namespace MediaManager\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

// NOTE: Livewire\Component must be available in the host Laravel app for this package to work.

class MediaPicker extends Component
{
    use WithFileUploads;

    public $show = true;
    public $images = [];
    public $folders = [];
    public $upload;
    public $currentPath = '/';
    public $selectedImage = [];
    public $multi = true; // Enable multi-select

    protected $listeners = ['openMediaPicker' => 'open'];

    public function mount($path = '/')
    {
        $this->currentPath = $path;
        $this->refreshImages();
    }

    public function open($path = '/')
    {
        $this->show = true;
        $this->currentPath = $path;
        $this->refreshImages();
    }

    public function close()
    {
        $this->show = false;
    }

    public function refreshImages()
    {
        $disk = config('media-manager.disk', 'public');
        $all = Storage::disk($disk)->files($this->currentPath);
        $this->images = array_filter($all, function($file) use ($disk) {
            $mime = Storage::disk($disk)->mimeType($file);
            return str_starts_with($mime, 'image/');
        });
        $this->folders = Storage::disk($disk)->directories($this->currentPath);
    }

    public function goToFolder($folder)
    {
        $this->currentPath = $folder;
        $this->refreshImages();
    }

    public function updatedUpload()
    {
        $disk = config('media-manager.disk', 'public');
        foreach ((array) $this->upload as $file) {
            $file->store($this->currentPath, $disk);
        }
        $this->refreshImages();
    }

    public function selectImage($file)
    {
        $disk = config('media-manager.disk', 'public');
        $url = Storage::disk($disk)->url($file);
        if ($this->multi) {
            if (!in_array($url, $this->selectedImage)) {
                $this->selectedImage[] = $url;
            } else {
                $this->selectedImage = array_filter($this->selectedImage, fn($u) => $u !== $url);
            }
        } else {
            $this->selectedImage = [$url];
            $this->emitUp('mediaPickerSelected', $url);
            $this->close();
        }
    }

    public function confirmSelection()
    {
        $this->emitUp('mediaPickerSelected', $this->selectedImage);
        $this->close();
    }

    public function render()
    {
        return view('media-manager::media-picker');
    }
} 