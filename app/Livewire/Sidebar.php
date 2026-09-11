<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Sidebar extends Component
{
    public string $activeMenu = '';

    public function mount(): void
    {
        $routeName = request()->route()?->getName() ?? '';
        $taskView = request()->query('task_view');

        // If 'task_view' exists, set it as activeMenu, otherwise use the route name
        $this->activeMenu = $taskView ?: $routeName;
    }

    public function render(): View
    {
        return view('livewire.sidebar');
    }
}
