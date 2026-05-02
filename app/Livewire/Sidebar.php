<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public $activeMenu;

    public function mount()
    {
        $routeName = request()->route()->getName();  // Get route name
        $taskView = request()->query('task_view');   // Get query param 'task_view'
    
        // If 'task_view' exists, set it as activeMenu, otherwise use route name
        $this->activeMenu = $taskView ? $taskView : $routeName;
    }

    // public function hydrate()
    // {
    //     // Log when the component is hydrated
    //     logger('SidebarComponent hydrated');
    // }

    // public function boot()
    // {
    //     // Log when the component is booted
    //     logger('SidebarComponent booted');
    // }

    // public function updating($propertyName)
    // {
    //     // Log before updating a property
    //     logger("SidebarComponent updating: {$propertyName}");
    // }

    // public function updated($propertyName)
    // {
    //     // Log after updating a property
    //     logger("SidebarComponent updated: {$propertyName}");
    // }

    // public function rendering()
    // {
    //     // Log before rendering
    //     logger('SidebarComponent rendering');
    // }

    // public function rendered()
    // {
    //     // Log after rendering
    //     logger('SidebarComponent rendered');
    // }

    // public function dehydrate()
    // {
    //     // Log when the component is dehydrated
    //     logger('SidebarComponent dehydrated');
    // }

    // public function exception($e, $stopPropagation)
    // {
    //     // Log when an exception is thrown
    //     logger("SidebarComponent exception: {$e->getMessage()}");
    // }

    // public function addMenuItem()
    // {
    //     try {
    //         if (empty($this->newMenuItem)) {
    //             throw new \Exception('Menu item cannot be empty');
    //         }
    //         $this->menuItems[] = $this->newMenuItem;
    //         $this->newMenuItem = '';
    //     } catch (\Exception $e) {
    //         $this->exception($e, false);
    //     }
    // }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
