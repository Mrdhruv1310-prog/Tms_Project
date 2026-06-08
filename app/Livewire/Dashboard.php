<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $labels = [];
    public $categories = [];
    public $team = [];
    public $groups = [];
    public $tasksAssignedByUser = [];

    public function mount()
    {
        $authUser = Auth::user();
        $authUserId = (int) $authUser->id;

        $isAdmin = $authUser->role === 'admin';
        $isUser = $authUser->role === 'user';

        $this->setLabels($isAdmin, $authUserId);
        $this->setCategories($isAdmin, $isUser, $authUserId);
        $this->setTeam($isAdmin, $isUser, $authUserId);
        $this->setGroups($isAdmin, $isUser, $authUserId);
    }

    private function assignedTaskQuery(bool $isAdmin, int $authUserId)
    {
        return Task::query()
            ->when(! $isAdmin, function ($query) use ($authUserId) {
                $query->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                    $assignmentQuery->where('user_id', $authUserId);
                });
            });
    }

    private function setLabels(bool $isAdmin, int $authUserId): void
    {
        $this->labels = [
            [
                'title' => 'Pending',
                'count' => (clone $this->assignedTaskQuery($isAdmin, $authUserId))->where('status', 'pending')->count(),
                'status' => 'pending',
                'bg' => '#fce2c7',
                'border' => '#f9d6b3',
            ],
            [
                'title' => 'In Progress',
                'count' => (clone $this->assignedTaskQuery($isAdmin, $authUserId))->where('status', 'in_progress')->count(),
                'status' => 'in-progress',
                'bg' => '#cbe6fc',
                'border' => '#afd7f9',
            ],
            [
                'title' => 'Completed',
                'count' => (clone $this->assignedTaskQuery($isAdmin, $authUserId))->where('status', 'completed')->count(),
                'status' => 'completed',
                'bg' => '#caf5de',
                'border' => '#9ffac9',
            ],
            [
                'title' => 'Total',
                'count' => (clone $this->assignedTaskQuery($isAdmin, $authUserId))->count(),
                'status' => 'total',
                'bg' => '#f5f3fa',
                'border' => '#d6d0e4',
            ],
        ];
    }

    private function setCategories(bool $isAdmin, bool $isUser, int $authUserId): void
    {
        if (! $isAdmin && ! $isUser) {
            $this->categories = [];
            return;
        }

        $this->categories = Category::withCount([
            'tasks as completed_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->where('status', 'completed')
                    ->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                        $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                            $assignmentQuery->where('user_id', $authUserId);
                        });
                    });
            },
            'tasks as total_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                    $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                        $assignmentQuery->where('user_id', $authUserId);
                    });
                });
            },
        ])
            ->get()
            ->filter(function ($category) use ($isAdmin) {
                return $isAdmin || $category->total_tasks_count > 0;
            })
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->name,
                    'completed' => $category->completed_tasks_count,
                    'total' => $category->total_tasks_count,
                    'percentage' => $category->total_tasks_count > 0
                        ? round(($category->completed_tasks_count / $category->total_tasks_count) * 100)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    private function setTeam(bool $isAdmin, bool $isUser, int $authUserId): void
    {
        if (! $isAdmin && ! $isUser) {
            $this->team = [];
            return;
        }

        $this->team = User::query()
            ->when($isUser, function ($query) use ($authUserId) {
                $query->where('id', $authUserId);
            })
            ->withCount([
                'taskAssignments as completed_tasks_count' => function ($query) {
                    $query->whereHas('task', function ($taskQuery) {
                        $taskQuery->where('status', 'completed');
                    });
                },
                'taskAssignments as total_tasks_count',
            ])
            ->get()
            ->filter(function ($user) use ($isAdmin) {
                return $isAdmin || $user->total_tasks_count > 0;
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name) ?: ($user->name ?? 'User'),
                    'completed' => $user->completed_tasks_count,
                    'total' => $user->total_tasks_count,
                    'percentage' => $user->total_tasks_count > 0
                        ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    private function setGroups(bool $isAdmin, bool $isUser, int $authUserId): void
    {
        if (! $isAdmin && ! $isUser) {
            $this->groups = [];
            return;
        }

        $this->groups = Group::withCount([
            'tasks as pending_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->where('status', 'pending')
                    ->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                        $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                            $assignmentQuery->where('user_id', $authUserId);
                        });
                    });
            },
            'tasks as inprogress_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->where('status', 'in_progress')
                    ->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                        $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                            $assignmentQuery->where('user_id', $authUserId);
                        });
                    });
            },
            'tasks as completed_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->where('status', 'completed')
                    ->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                        $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                            $assignmentQuery->where('user_id', $authUserId);
                        });
                    });
            },
            'tasks as total_tasks_count' => function ($query) use ($isAdmin, $authUserId) {
                $query->when(! $isAdmin, function ($taskQuery) use ($authUserId) {
                    $taskQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                        $assignmentQuery->where('user_id', $authUserId);
                    });
                });
            },
        ])
            ->get()
            ->filter(function ($group) use ($isAdmin) {
                return $isAdmin || $group->total_tasks_count > 0;
            })
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->label ?? $group->name ?? 'No Group Name',
                    'pending' => $group->pending_tasks_count,
                    'in_progress' => $group->inprogress_tasks_count,
                    'completed' => $group->completed_tasks_count,
                    'total' => $group->total_tasks_count,
                    'percentage' => $group->total_tasks_count > 0
                        ? round(($group->completed_tasks_count / $group->total_tasks_count) * 100)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', [
            'title' => 'Dashboard | TMS',
        ]);
    }
}
