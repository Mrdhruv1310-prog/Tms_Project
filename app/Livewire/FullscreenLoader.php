<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\View\View;

class FullscreenLoader extends Component
{
    /**
     * Optional initialization logic for the loader.
     */
    public function load(): void
    {
        // Add any background initialization logic here if required
    }

    /**
     * Render the fullscreen loader view.
     */
    public function render(): View
    {
        return view('livewire.fullscreen-loader');
    }
}
