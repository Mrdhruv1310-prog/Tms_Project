<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeamPerformance extends Component
{
    public array $team = [];

    public function mount(): void
    {
        $authUserId = Auth::id();
        $authUser = Auth::user();

        if (! $authUser) {
            abort(403, 'Unauthorized action.');
        }

        $role = $authUser->role ?? 'user';

        if ($role === 'admin') {
            $this->team = User::withCount([
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
                ->map(function ($user) {
                    $fullName = trim($user->first_name . ' ' . $user->last_name);
                    if (empty($fullName)) {
                        $fullName = $user->name ?? 'Team Member';
                    }

                    return [
                        'name' => $fullName,
                        'completed' => $user->completed_tasks_count,
                        'in_progress' => $user->in_progress_tasks_count,
                        'pending' => $user->pending_tasks_count,
                        'total' => $user->total_tasks_count,
                        'percentage' => $user->total_tasks_count > 0 ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0,
                    ];
                })
                ->toArray();
        } else {
            $this->team = User::whereHas('taskAssignments', function ($query) use ($authUserId) {
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
                ->map(function ($user) {
                    $fullName = trim($user->first_name . ' ' . $user->last_name);
                    if (empty($fullName)) {
                        $fullName = $user->name ?? 'Team Member';
                    }

                    return [
                        'name' => $fullName,
                        'completed' => $user->completed_tasks_count,
                        'in_progress' => $user->in_progress_tasks_count,
                        'pending' => $user->pending_tasks_count,
                        'total' => $user->total_tasks_count,
                        'percentage' => ($user->total_tasks_count > 0) ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0,
                    ];
                })
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.team-performance')->layout('components.layouts.app', [
            'title' => 'Team Performance | TMS',
        ]);
    }
}
