<?php

namespace App\Livewire;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupPerformance extends Component
{
    public $group;
    public string $groupName = 'Unknown Group';
    public array $users = [];

    public function mount($id)
    {
        $authUser = Auth::user();
        if (! $authUser) {
            abort(403, 'Unauthorized action.');
        }

        $authUserId = (int) $authUser->id;
        $role = $authUser->role ?? 'user';
        $isUser = ($role === 'user');

        // If regular user, verify access to this group via assigned tasks
        if ($isUser) {
            $hasAccess = Group::where('id', $id)
                ->whereHas('tasks.taskAssignments', function ($query) use ($authUserId) {
                    $query->where('user_id', $authUserId);
                })
                ->exists();

            abort_if(! $hasAccess, 403);
        }

        // Fetch group with users and tasks in a single query execution (eliminates redundant DB hits)
        $this->group = Group::with([
            'users' => function ($query) use ($isUser, $authUserId) {
                $query->when($isUser, function ($q) use ($authUserId) {
                    $q->where('users.id', $authUserId);
                })
                    ->whereHas('tasks')
                    ->with([
                        'tasks' => function ($taskQuery) use ($isUser, $authUserId) {
                            $taskQuery->select('tasks.id', 'task_assignments.user_id', 'tasks.status')
                                ->join('task_assignments', 'tasks.id', '=', 'task_assignments.task_id')
                                ->when($isUser, function ($q) use ($authUserId) {
                                    $q->where('task_assignments.user_id', $authUserId);
                                });
                        }
                    ]);
            }
        ])
            ->where('id', $id)
            ->firstOrFail();

        // Set group name safely from the fetched model instance
        $this->groupName = $this->group->label ?? $this->group->name ?? 'Unknown Group';

        // Map user performance metrics
        $this->users = $this->group->users->map(function ($user) {
            $completedTasks = $user->tasks->where('status', 'completed')->count();
            $inProgressTasks = $user->tasks->where('status', 'in_progress')->count();
            $pendingTasks = $user->tasks->where('status', 'pending')->count();
            $totalTasks = $user->tasks->count();

            $fullName = trim($user->first_name . ' ' . $user->last_name);
            if (empty($fullName)) {
                $fullName = $user->name ?? 'User';
            }

            return [
                'id' => $user->id,
                'name' => $fullName,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'pending' => $pendingTasks,
                'total' => $totalTasks,
                'percentage' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.group-performance')->layout('components.layouts.app', [
            'title' => $this->groupName . ' Group | TMS',
        ]);
    }
}
