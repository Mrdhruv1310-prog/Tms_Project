<?php

namespace App\Livewire;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupPerformance extends Component
{
    public $group;
    public $groupName;
    public $users = [];

    public function mount($id)
    {
        // get group label name from id
        $this->groupName = Group::find($id)->label ?? 'Unknown Group';
        $this->group = Group::with([
            'users' => function ($query) {
                $query->whereHas('tasks')->with([
                    'tasks' => function ($taskQuery) {
                        $taskQuery->select('tasks.id', 'task_assignments.user_id', 'tasks.status')
                                  ->join('task_assignments', 'tasks.id', '=', 'task_assignments.task_id');
                    }
                ]);
            }
        ])
        ->where('id', $id)
        ->firstOrFail();

        // Fetch users only in this specific group
        $this->users = $this->group->users->map(function ($user) {
            $completedTasks = $user->tasks->where('status', 'completed')->count();
            $inProgressTasks = $user->tasks->where('status', 'in_progress')->count();
            $pendingTasks = $user->tasks->where('status', 'pending')->count();
            $totalTasks = $user->tasks->count();
            $percentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'pending' => $pendingTasks,
                'total' => $totalTasks,
                'percentage' => $percentage,
            ];
        })->toArray();
    }


    public function render()
    {
        return view('livewire.group-performance')->layout('components.layouts.app', [
            'title' => $this->groupName.' Group | TMS',
        ]);
    }
}
