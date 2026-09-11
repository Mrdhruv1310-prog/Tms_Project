<?php

namespace App\Livewire;

use App\Models\Task as TaskModel; // Aliased to prevent naming collision with this class name
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Task extends Component
{
    public $tasks = [];

    public function mount()
    {
        // Example: Load tasks safely for the authenticated user
        $authUser = Auth::user();

        if ($authUser) {
            $this->tasks = TaskModel::query()
                ->when($authUser->role !== 'super-admin', function ($query) use ($authUser) {
                    $query->where('user_id', $authUser->id);
                })
                ->latest()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.task');
    }
}
