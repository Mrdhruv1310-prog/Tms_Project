<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Team Performance | TMS')]
class TeamPerformance extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $team = [];

    public function mount(): void
    {
        $authUserId = Auth::id();
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (! $authUser) {
            abort(403, 'Unauthorized action.');
        }

        $role = $authUser->role ?? 'user';

        if ($role === 'admin') {
            $this->team = User::query()
                ->withCount([
                    'taskAssignments as completed_tasks_count' => function ($query) {
                        $query->whereHas('task', function ($taskQuery) {
                            $taskQuery->where('status', 'completed');
                        });
                    },
                    'taskAssignments as in_progress_tasks_count' => function ($query) {
                        $query->whereHas('task', function ($taskQuery) {
                            $taskQuery->where('status', 'in_progress');
                        });
                    },
                    'taskAssignments as pending_tasks_count' => function ($query) {
                        $query->whereHas('task', function ($taskQuery) {
                            $taskQuery->where('status', 'pending');
                        });
                    },
                    'taskAssignments as total_tasks_count' => function ($query) {
                        $query->whereHas('task');
                    },
                ])
                ->get()
                ->map(function (User $user): array {
                    $firstName = is_string($user->getAttribute('first_name')) ? $user->getAttribute('first_name') : '';
                    $lastName = is_string($user->getAttribute('last_name')) ? $user->getAttribute('last_name') : '';
                    $fullName = trim($firstName . ' ' . $lastName);

                    if (empty($fullName)) {
                        $fullName = is_string($user->getAttribute('name')) ? $user->getAttribute('name') : 'Team Member';
                    }

                    $completed = (int) ($user->completed_tasks_count ?? 0);
                    $inProgress = (int) ($user->in_progress_tasks_count ?? 0);
                    $pending = (int) ($user->pending_tasks_count ?? 0);
                    $total = (int) ($user->total_tasks_count ?? 0);

                    return [
                        'name' => $fullName,
                        'completed' => $completed,
                        'in_progress' => $inProgress,
                        'pending' => $pending,
                        'total' => $total,
                        'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                    ];
                })
                ->toArray();
        } else {
            $this->team = User::query()
                ->whereHas('taskAssignments', function ($query) use ($authUserId) {
                    $query->whereHas('task', function ($taskQuery) use ($authUserId) {
                        $taskQuery->where('user_id', $authUserId);
                    });
                })
                ->where('id', '!=', $authUserId)
                ->withCount([
                    'taskAssignments as completed_tasks_count' => function ($query) use ($authUserId) {
                        $query->whereHas('task', function ($taskQuery) use ($authUserId) {
                            $taskQuery->where('status', 'completed')
                                ->where('user_id', $authUserId);
                        });
                    },
                    'taskAssignments as in_progress_tasks_count' => function ($query) use ($authUserId) {
                        $query->whereHas('task', function ($taskQuery) use ($authUserId) {
                            $taskQuery->where('status', 'in_progress')
                                ->where('user_id', $authUserId);
                        });
                    },
                    'taskAssignments as pending_tasks_count' => function ($query) use ($authUserId) {
                        $query->whereHas('task', function ($taskQuery) use ($authUserId) {
                            $taskQuery->where('status', 'pending')
                                ->where('user_id', $authUserId);
                        });
                    },
                    'taskAssignments as total_tasks_count' => function ($query) use ($authUserId) {
                        $query->whereHas('task', function ($taskQuery) use ($authUserId) {
                            $taskQuery->where('user_id', $authUserId);
                        });
                    },
                ])
                ->get()
                ->map(function (User $user): array {
                    $firstName = is_string($user->getAttribute('first_name')) ? $user->getAttribute('first_name') : '';
                    $lastName = is_string($user->getAttribute('last_name')) ? $user->getAttribute('last_name') : '';
                    $fullName = trim($firstName . ' ' . $lastName);

                    if (empty($fullName)) {
                        $fullName = is_string($user->getAttribute('name')) ? $user->getAttribute('name') : 'Team Member';
                    }

                    $completed = (int) ($user->completed_tasks_count ?? 0);
                    $inProgress = (int) ($user->in_progress_tasks_count ?? 0);
                    $pending = (int) ($user->pending_tasks_count ?? 0);
                    $total = (int) ($user->total_tasks_count ?? 0);

                    return [
                        'name' => $fullName,
                        'completed' => $completed,
                        'in_progress' => $inProgress,
                        'pending' => $pending,
                        'total' => $total,
                        'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                    ];
                })
                ->toArray();
        }
    }

    public function render(): mixed
    {
        return view('livewire.team-performance');
    }
}
