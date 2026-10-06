<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Group Performance | TMS')]
class GroupPerformance extends Component
{
    public ?Group $group = null;
    public string $groupName = 'Unknown Group';

    /** @var array<int, array<string, mixed>> */
    public array $users = [];

    public function mount(int|string $id): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser) {
            abort(403, 'Unauthorized action.');
        }

        $authUserId = (int) $authUser->id;
        $role = $authUser->role ?? 'user';
        $isUser = ($role === 'user');

        // If regular user, verify access to this group via assigned tasks
        if ($isUser) {
            $hasAccess = Group::query()->where('id', $id)
                ->whereHas('tasks.taskAssignments', function ($query) use ($authUserId) {
                    $query->where('user_id', $authUserId);
                })
                ->exists();

            abort_if(! $hasAccess, 403);
        }

        // Fetch group with users and tasks in a single query execution (eliminates redundant DB hits)
        /** @var Group $group */
        $group = Group::query()->with([
            'users' => function ($query) use ($isUser, $authUserId) {
                $query->when($isUser, function ($q) use ($authUserId) {
                    $q->where('users.id', $authUserId);
                })
                    ->whereHas('tasks')
                    ->with([
                        'tasks' => function ($taskQuery) use ($isUser, $authUserId) {
                            $taskQuery->select('tasks.id', 'tasks.status')
                                ->when($isUser, function ($q) use ($authUserId) {
                                    $q->whereHas('taskAssignments', function ($subQ) use ($authUserId) {
                                        $subQ->where('user_id', $authUserId);
                                    });
                                });
                        }
                    ]);
            }
        ])
            ->where('id', $id)
            ->firstOrFail();

        $this->group = $group;
        // Set group name safely from the fetched model instance
        $this->groupName = $this->group->label ?? $this->group->name ?? 'Unknown Group';

        // Map user performance metrics
        $this->users = $this->group->users->map(function ($user) {
            /** @var User $user */
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

    public function render(): View
    {
        return view('livewire.group-performance');
    }
}
