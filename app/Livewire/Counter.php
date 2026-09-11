<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    // Enforce strict integer typing to prevent payload tampering/type mismatch
    public int $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        // Optional: Prevent count from dropping below 0 (uncomment if required)
        // if ($this->count > 0) {
        //     $this->count--;
        // }
        $this->count--;
    }
    public function render()
    {
        // If this component is used as a full-page view via routes, uncomment the layout line below:
        // return view('livewire.counter')->layout('components.layouts.app', ['title' => 'Counter | TMS']);
        return view('livewire.counter');
    }
}
