<?php

namespace App\Livewire;

use App\Models\Task as TaskModel; // Aliased to prevent naming collision with this class name
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Task extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, TaskModel> */
    public $tasks;

    public function mount(): void
    {
        // Example: Load tasks safely for the authenticated user
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if ($authUser) {
            $query = TaskModel::query();

            if ($authUser->role !== 'super-admin') {
                $query->where('user_id', $authUser->id);
            }

            $this->tasks = $query->latest()->get();
        } else {
            $this->tasks = new \Illuminate\Database\Eloquent\Collection();
        }
    }

    public function render(): mixed
    {
        return view('livewire.task');
    }
}
