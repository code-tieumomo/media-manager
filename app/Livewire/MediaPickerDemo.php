<?php

namespace App\Livewire;

use Livewire\Component;

class MediaPickerDemo extends Component
{
    public $media = [];

    public function render()
    {
        return view('livewire.media-picker-demo');
    }

    public function submit()
    {
        dd($this->media);
    }
}
