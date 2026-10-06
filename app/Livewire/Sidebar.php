<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Sidebar extends Component
{
    public string $activeMenu = '';

    public function mount(): void
    {
        $currentRoute = Route::current();
        $routeName = $currentRoute ? (string) $currentRoute->getName() : '';

        $taskView = request()->query('task_view');
        $taskViewString = is_string($taskView) ? $taskView : '';

        // If 'task_view' exists, set it as activeMenu, otherwise use the route name
        $this->activeMenu = $taskViewString !== '' ? $taskViewString : $routeName;
    }

    public function render(): View
    {
        return view('livewire.sidebar');
    }
}
