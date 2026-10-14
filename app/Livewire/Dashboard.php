<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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

    // Filters with Persistence
    public string $selectedStatusFilter = 'total';
    public ?string $selectedYear = null;
    public ?string $selectedMonth = null;
    public ?string $selectedDate = null;
    public string $selectedTimeRange = 'all';

    protected $listeners = [
        'taskCreated' => 'refreshDashboardData',
        'taskStatusUpdated' => 'refreshDashboardData',
        'taskDeleted' => 'refreshDashboardData',
        'userDeleted' => 'refreshDashboardData',
    ];

    public function mount(): void
    {
        // Session se stored filters load karein (Persistence across navigation)
        $this->selectedTimeRange = session('dashboard_time_range', 'all');
        $this->selectedYear = session('dashboard_year', null);
        $this->selectedMonth = session('dashboard_month', null);
        $this->selectedDate = session('dashboard_date', null);

        $this->refreshDashboardData();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['selectedYear', 'selectedMonth', 'selectedDate', 'selectedTimeRange', 'selectedStatusFilter'])) {
            // Save to Session
            session([
                'dashboard_time_range' => $this->selectedTimeRange,
                'dashboard_year' => $this->selectedYear,
                'dashboard_month' => $this->selectedMonth,
                'dashboard_date' => $this->selectedDate,
            ]);

            $this->refreshDashboardData();
        }
    }

    public function filterByStatus(string $status): void
    {
        $this->selectedStatusFilter = $status;
        $this->refreshDashboardData();
    }

    public function filterCategoryPendingTasks(string $categoryName)
    {
        $queryParams = $this->getDateQueryParams();
        $queryParams['category'] = $categoryName;
        $queryParams['status'] = 'pending';

        return redirect()->to('/tasks?' . http_build_query($queryParams));
    }

    public function navigateToUserPendingTasks(int $userId)
    {
        $queryParams = $this->getDateQueryParams();
        $queryParams['user_id'] = $userId;

        return redirect()->to('/tasks/user/pending?' . http_build_query($queryParams));
    }

    public function clearDateFilters(): void
    {
        $this->selectedYear = null;
        $this->selectedMonth = null;
        $this->selectedDate = null;
        $this->selectedTimeRange = 'all';

        // Session reset karein
        session()->forget(['dashboard_time_range', 'dashboard_year', 'dashboard_month', 'dashboard_date']);

        $this->refreshDashboardData();
    }

    public function getDateQueryParams(): array
    {
        $params = [];
        if (filled($this->selectedDate)) {
            $params['date'] = $this->selectedDate;
        } elseif ($this->selectedTimeRange !== 'all') {
            $params['time_range'] = $this->selectedTimeRange;
        } else {
            if (filled($this->selectedYear)) {
                $params['year'] = $this->selectedYear;
            }
            if (filled($this->selectedMonth)) {
                $params['month'] = $this->selectedMonth;
            }
        }

        return $params;
    }

    public function refreshDashboardData(): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser) {
            return;
        }

        $authUserId = (int) $authUser->id;
        $role = strtolower($authUser->role ?? 'employee');

        $this->setLabels($role, $authUserId);
        $this->setCategories($role, $authUserId);
        $this->setTeam($role, $authUserId);
        $this->setGroups($role, $authUserId);
    }

    private function hasActiveDateFilter(): bool
    {
        return filled($this->selectedDate)
            || ($this->selectedTimeRange !== 'all')
            || filled($this->selectedYear)
            || filled($this->selectedMonth);
    }

    private function applyDateFilters(Builder $query): void
    {
        if (filled($this->selectedDate)) {
            $query->whereDate('created_at', Carbon::parse($this->selectedDate)->format('Y-m-d'));
            return;
        }

        if ($this->selectedTimeRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
            return;
        } elseif ($this->selectedTimeRange === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            return;
        } elseif ($this->selectedTimeRange === 'this_month') {
            $query->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);
            return;
        } elseif ($this->selectedTimeRange === 'this_year') {
            $query->whereYear('created_at', Carbon::now()->year);
            return;
        }

        if (filled($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        if (filled($this->selectedMonth)) {
            $query->whereMonth('created_at', $this->selectedMonth);
        }
    }

    private function roleBasedTaskQuery(string $role, int $authUserId): Builder
    {
        $query = Task::query()->whereHas('creator');

        $this->applyDateFilters($query);

        // ONLY Super-Admin gets un-restricted access across all tasks
        if ($role === 'super-admin') {
            return $query;
        }

        // For EVERYONE else (Admin, HR, Manager, Employee), count ONLY tasks created by or assigned to Auth User
        return $query->where(function (Builder $q) use ($authUserId) {
            $q->where('tasks.user_id', $authUserId)
                ->orWhereHas('taskAssignments', function ($aq) use ($authUserId) {
                    $aq->where('user_id', $authUserId)->whereHas('user');
                });
        });
    }

    private function setLabels(string $role, int $authUserId): void
    {
        $baseQuery = fn() => $this->roleBasedTaskQuery($role, $authUserId);

        $this->labels = [
            [
                'title' => 'Pending',
                'count' => $baseQuery()->where('status', 'pending')->count(),
                'status' => 'pending',
                'bg' => '#fce2c7',
                'border' => '#f9d6b3',
            ],
            [
                'title' => 'In Progress',
                'count' => $baseQuery()->where('status', 'in_progress')->count(),
                'status' => 'in_progress',
                'bg' => '#cbe6fc',
                'border' => '#afd7f9',
            ],
            [
                'title' => 'Completed',
                'count' => $baseQuery()->where('status', 'completed')->count(),
                'status' => 'completed',
                'bg' => '#caf5de',
                'border' => '#9ffac9',
            ],
            [
                'title' => 'Total',
                'count' => $baseQuery()->count(),
                'status' => 'total',
                'bg' => '#f5f3fa',
                'border' => '#d6d0e4',
            ],
        ];
    }

    private function setCategories(string $role, int $authUserId): void
    {
        $this->categories = Category::query()
            ->with('creator')
            ->withCount([
                'tasks as pending_tasks_count' => function ($query) use ($role, $authUserId) {
                    $query->where('tasks.status', 'pending')
                        ->whereHas('creator');
                    $this->applyDateFilters($query);

                    if ($role !== 'super-admin') {
                        $query->where(function ($sub) use ($authUserId) {
                            $sub->where('tasks.user_id', $authUserId)
                                ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                        });
                    }
                },
                'tasks as total_tasks_count' => function ($query) use ($role, $authUserId) {
                    $query->whereHas('creator');
                    $this->applyDateFilters($query);

                    if ($role !== 'super-admin') {
                        $query->where(function ($sub) use ($authUserId) {
                            $sub->where('tasks.user_id', $authUserId)
                                ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                        });
                    }
                },
            ])
            ->get()
            ->map(function ($category) {
                $pending = $category->pending_tasks_count ?? 0;
                $total = $category->total_tasks_count ?? 0;

                return [
                    'id' => $category->id,
                    'title' => $category->name,
                    'creator_name' => $category->creator ? ($category->creator->first_name ?? $category->creator->name) : 'N/A',
                    'pending' => $pending,
                    'total' => $total,
                    'percentage' => $total > 0
                        ? round(($pending / $total) * 100)
                        : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    private function setTeam(string $role, int $authUserId): void
    {
        /** @var Builder<User> $query */
        $query = User::query();

        // Unless super-admin, only fetch auth user card on dashboard
        if ($role !== 'super-admin') {
            $query->where('id', $authUserId);
        }

        $allMembers = $query->get()
            ->map(function ($user) use ($role, $authUserId) {
                $targetUserId = (int) $user->id;

                // Unique Task IDs where target user is Creator or Assignee
                $assignedTaskIds = \Illuminate\Support\Facades\DB::table('task_assignments')
                    ->where('user_id', $targetUserId)
                    ->pluck('task_id');

                $createdTaskIds = Task::query()
                    ->where('user_id', $targetUserId)
                    ->pluck('id');

                $allUniqueTaskIds = $assignedTaskIds->merge($createdTaskIds)->unique()->values()->toArray();

                $taskBaseQuery = Task::query()
                    ->whereIn('id', $allUniqueTaskIds)
                    ->whereHas('creator');

                // Apply Dashboard Active Date Filters
                $this->applyDateFilters($taskBaseQuery);

                // For Non Super-Admin, restrict tasks to strictly assigned-by or assigned-to Auth User
                if ($role !== 'super-admin' && $targetUserId !== $authUserId) {
                    $taskBaseQuery->where(function (Builder $q) use ($authUserId, $targetUserId) {
                        $q->where(function (Builder $sub) use ($authUserId, $targetUserId) {
                            $sub->where('user_id', $authUserId)
                                ->whereHas('taskAssignments', fn(Builder $a) => $a->where('user_id', $targetUserId));
                        })
                            ->orWhere(function (Builder $sub) use ($authUserId, $targetUserId) {
                                $sub->where('user_id', $targetUserId)
                                    ->whereHas('taskAssignments', fn(Builder $a) => $a->where('user_id', $authUserId));
                            });
                    });
                }

                $pending = (clone $taskBaseQuery)->where('status', 'pending')->count();
                $inProgress = (clone $taskBaseQuery)->where('status', 'in_progress')->count();
                $completed = (clone $taskBaseQuery)->where('status', 'completed')->count();
                $total = (clone $taskBaseQuery)->count();

                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                if (empty($fullName)) {
                    $fullName = $user->name ?? 'User';
                }

                return [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'name' => $fullName,
                    'pending' => $pending,
                    'in_progress' => $inProgress,
                    'completed' => $completed,
                    'total' => $total,
                    'percentage' => $total > 0
                        ? round(($completed / $total) * 100)
                        : 0,
                ];
            });

        $authUserCard = $allMembers->firstWhere('id', $authUserId);
        $otherCards = $allMembers->reject(fn($member) => $member['id'] === $authUserId)->values();

        $sortedTeam = collect();
        if ($authUserCard) {
            $sortedTeam->push($authUserCard);
        }

        $this->team = $sortedTeam->concat($otherCards)->toArray();
    }

    private function setGroups(string $role, int $authUserId): void
    {
        /** @var Builder<Group> $groupQuery */
        $groupQuery = Group::query();

        $this->groups = $groupQuery->withCount([
            'tasks as pending_tasks_count' => function ($query) use ($role, $authUserId) {
                $query->where('status', 'pending')->whereHas('creator');
                $this->applyDateFilters($query);

                if ($role !== 'super-admin') {
                    $query->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                }
            },
            'tasks as inprogress_tasks_count' => function ($query) use ($role, $authUserId) {
                $query->where('status', 'in_progress')->whereHas('creator');
                $this->applyDateFilters($query);

                if ($role !== 'super-admin') {
                    $query->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                }
            },
            'tasks as completed_tasks_count' => function ($query) use ($role, $authUserId) {
                $query->where('status', 'completed')->whereHas('creator');
                $this->applyDateFilters($query);

                if ($role !== 'super-admin') {
                    $query->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                }
            },
            'tasks as total_tasks_count' => function ($query) use ($role, $authUserId) {
                $query->whereHas('creator');
                $this->applyDateFilters($query);

                if ($role !== 'super-admin') {
                    $query->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                }
            },
        ])
            ->get()
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

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
