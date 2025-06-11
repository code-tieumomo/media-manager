<?php

namespace MediaManager\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Spatie\LivewireFilepond\WithFilePond;

// NOTE: Livewire\Component must be available in the host Laravel app for this package to work.

class MediaManager extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $currentPath = '/';
    public $files = [];
    public $tmpFiles = [];
    public $folders = [];
    public $filter = 'all'; // all, images, docs
    public $upload;
    public $newFolderName = '';
    public $selected = [];
    public $moveTarget = null;
    public $directoryTree = [];
    public $expandedFolders = [];

    protected $rules = [
        'upload.*' => 'file',
        'newFolderName' => 'string|max:255',
    ];

    public function mount($path = '/')
    {
        $this->currentPath = $path;
        $this->refreshFiles();
        $this->refreshDirectoryTree();
        $this->autoExpandCurrentPath();
    }

    public function buildDirectoryTree($rootPath = '/')
    {
        $disk = config('media-manager.disk', 'public');
        return $this->buildDirectoryTreeRecursive($rootPath, $disk);
    }

    private function buildDirectoryTreeRecursive($path, $disk)
    {
        $directories = Storage::disk($disk)->directories($path);
        $tree = [];

        foreach ($directories as $directory) {
            $name = basename($directory);
            $tree[] = [
                'name' => $name,
                'path' => $directory,
                'children' => $this->buildDirectoryTreeRecursive($directory, $disk),
                'expanded' => in_array($directory, $this->expandedFolders),
            ];
        }

        return $tree;
    }

    public function refreshDirectoryTree()
    {
        $this->directoryTree = $this->buildDirectoryTree('/');
    }

    public function autoExpandCurrentPath()
    {
        if ($this->currentPath === '/') {
            return;
        }

        $pathParts = explode('/', trim($this->currentPath, '/'));
        $currentPath = '';
        
        foreach ($pathParts as $part) {
            $currentPath .= '/' . $part;
            $currentPath = ltrim($currentPath, '/');
            if ($currentPath && !in_array($currentPath, $this->expandedFolders)) {
                $this->expandedFolders[] = $currentPath;
            }
        }
        
        $this->refreshDirectoryTree();
    }

    public function toggleFolder($path)
    {
        if (in_array($path, $this->expandedFolders)) {
            $this->expandedFolders = array_filter($this->expandedFolders, fn($p) => $p !== $path);
        } else {
            $this->expandedFolders[] = $path;
        }
        $this->refreshDirectoryTree();
    }

    public function refreshFiles()
    {
        $disk = config('media-manager.disk', 'public');
        $all = Storage::disk($disk)->files($this->currentPath);
        $dirs = Storage::disk($disk)->directories($this->currentPath);
        $this->files = $this->filterFiles($all);
        $this->folders = $dirs;
    }

    public function filterFiles($files)
    {
        $files = array_filter($files, function($file) {
            return !str_starts_with($file, '.'); // Exclude hidden files
        });

        if ($this->filter === 'all') return $files;
        $allowed = config('media-manager.allowed_types', []);
        return array_filter($files, function($file) use ($allowed) {
            $mime = Storage::disk(config('media-manager.disk', 'public'))->mimeType($file);
            if ($this->filter === 'images') {
                return str_starts_with($mime, 'image/');
            } elseif ($this->filter === 'docs') {
                return !str_starts_with($mime, 'image/');
            }
            return true;
        });
    }

    public function updatedUpload()
    {
        $disk = config('media-manager.disk', 'public');
        foreach ((array) $this->upload as $file) {
            $fileName = $file->getClientOriginalName();
            $file->storeAs($this->currentPath, $fileName, $disk);
        }
        $this->refreshFiles();
    }

    public function createFolder()
    {
        $disk = config('media-manager.disk', 'public');
        $folder = trim($this->newFolderName);
        if ($folder) {
            Storage::disk($disk)->makeDirectory($this->currentPath . '/' . $folder);
            $this->newFolderName = '';
            $this->refreshFiles();
            $this->refreshDirectoryTree();
        }
    }

    public function delete($path)
    {
        $disk = config('media-manager.disk', 'public');
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        } elseif (Storage::disk($disk)->exists($path . '/')) {
            Storage::disk($disk)->deleteDirectory($path);
        }
        $this->refreshFiles();
        $this->refreshDirectoryTree();
    }

    public function move($from, $to)
    {
        $disk = config('media-manager.disk', 'public');
        if (Storage::disk($disk)->exists($from)) {
            Storage::disk($disk)->move($from, $to);
        }
        $this->refreshFiles();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->refreshFiles();
    }

    public function goToFolder($folder)
    {
        if ($folder === '.') {
            $folder = '/';
        }
        $this->currentPath = $folder;
        $this->refreshFiles();
        $this->autoExpandCurrentPath();
    }

    public function saveTmpFiles()
    {
        foreach ($this->tmpFiles as $file) {
            $disk = config('media-manager.disk', 'public');
            $fileName = $file->getClientOriginalName();
            $file->storeAs($this->currentPath, $fileName, $disk);
        }

        $this->tmpFiles = [];
        $this->dispatch('filepond-reset-tmpFiles');
        $this->refreshFiles();
        $this->refreshDirectoryTree();
    }

    public function render()
    {
        return view('media-manager::media-manager');
    }
} 