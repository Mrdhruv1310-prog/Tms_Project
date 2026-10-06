<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Dashboard | TMS')]
class Dashboard extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $labels = [];

    /** @var array<int, array<string, mixed>> */
    public array $categories = [];

    /** @var array<int, array<string, mixed>> */
    public array $team = [];

    /** @var array<int, array<string, mixed>> */
    public array $groups = [];

    /** @var \Illuminate\Database\Eloquent\Collection<int, Task> */
    public $tasksAssignedByUser;

    public ?int $openStatusTaskId = null;

    /** @var array<int, array<string, mixed>> */
    public array $statusForm = [];

    public function mount(): void
    {
        $this->refreshDashboardData();
    }

    private function refreshDashboardData(): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser) {
            return;
        }

        $authUserId = (int) $authUser->id;
        $role = $authUser->role ?? 'user';

        $isSuperAdmin = ($role === 'super-admin');
        $isAdmin = ($role === 'admin');
        $isUser = ($role === 'user' || $role === 'employee');

        $this->setLabels($isSuperAdmin, $authUserId);
        $this->setCategories($isSuperAdmin, $isAdmin, $isUser, $authUserId);
        $this->setTeam($isSuperAdmin, $isAdmin, $isUser, $authUserId);
        $this->setGroups($isSuperAdmin, $isAdmin, $isUser, $authUserId);
        $this->setTasksAssignedByUser($isSuperAdmin, $authUserId);
    }

    /**
     * @return Builder<Task>
     */
    private function assignedTaskQuery(bool $isSuperAdmin, int $authUserId): Builder
    {
        $query = Task::query();

        if (! $isSuperAdmin) {
            $query->where(function ($subQuery) use ($authUserId) {
                /** @var Builder<Task> $subQuery */
                $subQuery->whereHas('taskAssignments', function ($assignmentQuery) use ($authUserId) {
                    $assignmentQuery->where('user_id', $authUserId);
                })
                    ->orWhere('user_id', $authUserId);
            });
        }

        return $query;
    }

    private function setLabels(bool $isSuperAdmin, int $authUserId): void
    {
        $this->labels = [
            [
                'title' => 'Pending',
                'count' => (clone $this->assignedTaskQuery($isSuperAdmin, $authUserId))->where('status', 'pending')->count(),
                'status' => 'pending',
                'bg' => '#fce2c7',
                'border' => '#f9d6b3',
            ],
            [
                'title' => 'In Progress',
                'count' => (clone $this->assignedTaskQuery($isSuperAdmin, $authUserId))->where('status', 'in_progress')->count(),
                'status' => 'in-progress',
                'bg' => '#cbe6fc',
                'border' => '#afd7f9',
            ],
            [
                'title' => 'Completed',
                'count' => (clone $this->assignedTaskQuery($isSuperAdmin, $authUserId))->where('status', 'completed')->count(),
                'status' => 'completed',
                'bg' => '#caf5de',
                'border' => '#9ffac9',
            ],
            [
                'title' => 'Total',
                'count' => (clone $this->assignedTaskQuery($isSuperAdmin, $authUserId))->count(),
                'status' => 'total',
                'bg' => '#f5f3fa',
                'border' => '#d6d0e4',
            ],
        ];
    }

    private function setCategories(bool $isSuperAdmin, bool $isAdmin, bool $isUser, int $authUserId): void
    {
        $this->categories = Category::withCount([
            'tasks as completed_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->where('status', 'completed')
                    ->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                        /** @var Builder<Task> $taskQuery */
                        $taskQuery->where(function ($sub) use ($authUserId) {
                            /** @var Builder<Task> $sub */
                            $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                                $aq->where('user_id', $authUserId);
                            })->orWhere('user_id', $authUserId);
                        });
                    });
            },
            'tasks as total_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                    /** @var Builder<Task> $taskQuery */
                    $taskQuery->where(function ($sub) use ($authUserId) {
                        /** @var Builder<Task> $sub */
                        $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                            $aq->where('user_id', $authUserId);
                        })->orWhere('user_id', $authUserId);
                    });
                });
            },
        ])
            ->get()
            ->filter(function ($category) use ($isSuperAdmin) {
                /** @var Category $category */
                if ($isSuperAdmin) {
                    return true;
                }
                return $category->total_tasks_count > 0;
            })
            ->map(function ($category) {
                /** @var Category $category */
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

    private function setTeam(bool $isSuperAdmin, bool $isAdmin, bool $isUser, int $authUserId): void
    {
        /** @var Builder<User> $query */
        $query = User::query();

        $query->when($isSuperAdmin, function ($q) {
            /** @var Builder<User> $q */
            return $q->whereIn('role', ['admin', 'user', 'employee']);
        });

        $query->when($isAdmin, function ($q) use ($authUserId) {
            /** @var Builder<User> $q */
            return $q->where('role', '!=', 'super-admin')
                ->where('id', '!=', $authUserId);
        });

        $query->when($isUser, function ($q) use ($authUserId) {
            /** @var Builder<User> $q */
            return $q->where('id', $authUserId);
        });

        $query->withCount([
            'taskAssignments as completed_tasks_count' => function ($q) {
                /** @var Builder<\App\Models\TaskAssignment> $q */
                $q->whereHas('task', function ($taskQuery) {
                    /** @var Builder<Task> $taskQuery */
                    $taskQuery->where('status', 'completed');
                });
            },
            'taskAssignments as total_tasks_count',
        ]);

        $this->team = $query->get()
            ->filter(function ($user) use ($isSuperAdmin) {
                /** @var User $user */
                if ($isSuperAdmin) {
                    return true;
                }
                return $user->total_tasks_count > 0;
            })
            ->map(function ($user) {
                /** @var User $user */
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


    private function setGroups(bool $isSuperAdmin, bool $isAdmin, bool $isUser, int $authUserId): void
    {
        /** @var Builder<Group> $groupQuery */
        $groupQuery = Group::query();

        $this->groups = $groupQuery->withCount([
            'tasks as pending_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->where('status', 'pending')
                    ->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                        /** @var Builder<Task> $taskQuery */
                        $taskQuery->where(function ($sub) use ($authUserId) {
                            /** @var Builder<Task> $sub */
                            $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                                $aq->where('user_id', $authUserId);
                            })->orWhere('user_id', $authUserId);
                        });
                    });
            },
            'tasks as inprogress_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->where('status', 'in_progress')
                    ->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                        /** @var Builder<Task> $taskQuery */
                        $taskQuery->where(function ($sub) use ($authUserId) {
                            /** @var Builder<Task> $sub */
                            $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                                $aq->where('user_id', $authUserId);
                            })->orWhere('user_id', $authUserId);
                        });
                    });
            },
            'tasks as completed_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->where('status', 'completed')
                    ->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                        /** @var Builder<Task> $taskQuery */
                        $taskQuery->where(function ($sub) use ($authUserId) {
                            /** @var Builder<Task> $sub */
                            $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                                $aq->where('user_id', $authUserId);
                            })->orWhere('user_id', $authUserId);
                        });
                    });
            },
            'tasks as total_tasks_count' => function ($query) use ($isSuperAdmin, $authUserId) {
                /** @var Builder<Task> $query */
                $query->when(! $isSuperAdmin, function ($taskQuery) use ($authUserId) {
                    /** @var Builder<Task> $taskQuery */
                    $taskQuery->where(function ($sub) use ($authUserId) {
                        /** @var Builder<Task> $sub */
                        $sub->whereHas('taskAssignments', function ($aq) use ($authUserId) {
                            $aq->where('user_id', $authUserId);
                        })->orWhere('user_id', $authUserId);
                    });
                });
            },
        ])
            ->get()
            ->filter(function ($group) use ($isSuperAdmin) {
                /** @var Group $group */
                return $isSuperAdmin || $group->total_tasks_count > 0;
            })
            ->map(function ($group) {
                /** @var Group $group */
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

    private function setTasksAssignedByUser(bool $isSuperAdmin, int $authUserId): void
    {
        $this->tasksAssignedByUser = $this->assignedTaskQuery($isSuperAdmin, $authUserId)
            ->with(['assignedBy', 'category', 'group'])
            ->latest('id')
            ->get();
    }

    public function openStatusDropdown(int $taskId): void
    {
        $task = $this->getAllowedTask($taskId);

        if (! $task || $task->status === 'completed') {
            return;
        }

        $this->openStatusTaskId = $task->id;

        $this->statusForm[$task->id] = [
            'status' => in_array($task->status, ['pending', 'in_progress'], true) ? 'in_progress' : 'completed',
            'comment' => ''
        ];
    }

    public function cancelStatusDropdown(): void
    {
        $this->openStatusTaskId = null;
        $this->resetValidation();
    }

    public function saveTaskStatus(int $taskId): void
    {
        $task = $this->getAllowedTask($taskId);

        if (! $task || $task->status === 'completed') {
            return;
        }

        $this->validate([
            "statusForm.$taskId.status" => 'required|in:in_progress,completed',
            "statusForm.$taskId.comment" => 'nullable|string|max:1000',
        ]);

        $newStatus = (string) ($this->statusForm[$taskId]['status'] ?? '');
        $comment = trim((string) ($this->statusForm[$taskId]['comment'] ?? ''));

        if (! in_array($newStatus, $this->allowedNextStatuses($task->status), true)) {
            $this->addError("statusForm.$taskId.status", 'Selected status is not allowed for this task.');
            return;
        }

        DB::transaction(function () use ($task, $newStatus, $comment) {
            $task->update([
                'status' => $newStatus,
            ]);

            DB::table('task_updates')->insert([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'status' => $newStatus,
                'comment' => $comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        unset($this->statusForm[$taskId]);
        $this->openStatusTaskId = null;
        $this->refreshDashboardData();

        session()->flash('success', 'Task status updated successfully.');
    }

    /**
     * @return array<int, string>
     */
    public function allowedNextStatuses(string $currentStatus): array
    {
        return match ($currentStatus) {
            'pending', 'in_progress' => [
                'in_progress',
                'completed',
            ],
            default => [],
        };
    }

    private function getAllowedTask(int $taskId): ?Task
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser) {
            return null;
        }

        $authUserId = (int) $authUser->id;
        $isSuperAdmin = ($authUser->role === 'super-admin');

        /** @var Task|null $task */
        $task = $this->assignedTaskQuery($isSuperAdmin, $authUserId)->where('id', $taskId)->first();

        return $task;
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'in_progress' => 'In Progress',
            'completed' => 'Complete',
            default => 'Pending',
        };
    }

    public function statusClass(string $status): string
    {
        return match ($status) {
            'in_progress' => 'background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd;',
            'completed' => 'background:#dcfce7;color:#15803d;border:1px solid #86efac;',
            default => 'background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;',
        };
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
