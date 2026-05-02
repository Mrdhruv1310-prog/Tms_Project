<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FullscreenLoader extends Component
{
    public function load()
    {
        // Optional: You can perform any initialization logic here if needed
    }

    public function render()
    {
        return view('livewire.fullscreen-loader');
    }
}
