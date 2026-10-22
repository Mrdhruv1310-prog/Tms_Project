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

    // Specific Date Filter Persistent Variable
    public string $selectedStatusFilter = 'total';
    public ?string $selectedDate = null;

    protected $listeners = [
        'taskCreated' => 'refreshDashboardData',
        'taskStatusUpdated' => 'refreshDashboardData',
        'taskDeleted' => 'refreshDashboardData',
        'userDeleted' => 'refreshDashboardData',
    ];

    public function mount(): void
    {
        $this->selectedDate = session('dashboard_date', null);
        $this->refreshDashboardData();
    }

    public function getActiveFilterCountProperty(): int
    {
        return filled($this->selectedDate) ? 1 : 0;
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'selectedDate') {
            session(['dashboard_date' => $this->selectedDate]);
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

    public function navigateToGroupTasks(int $groupId)
    {
        $group = Group::find($groupId);
        $groupName = $group ? ($group->label ?? $group->name) : '';

        $queryParams = $this->getDateQueryParams();
        if (!empty($groupName)) {
            $queryParams['group'] = $groupName;
        }

        return redirect()->to('/tasks?' . http_build_query($queryParams));
    }

    public function clearDateFilters(): void
    {
        $this->selectedDate = null;
        session()->forget(['dashboard_date']);

        $this->refreshDashboardData();
    }

    public function getDateQueryParams(): array
    {
        $params = [];
        if (filled($this->selectedDate)) {
            $params['date'] = $this->selectedDate;
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

    private function applyDateFilters(Builder $query): void
    {
        if (filled($this->selectedDate)) {
            $formattedDate = Carbon::parse($this->selectedDate)->format('Y-m-d');
            $query->whereDate('created_at', $formattedDate);
        }
    }

    private function roleBasedTaskQuery(string $role, int $authUserId): Builder
    {
        $query = Task::query()->whereHas('creator');

        $this->applyDateFilters($query);

        if ($role === 'super-admin') {
            return $query;
        }

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

        // Calculate Weekly Comparison
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $calculateWeeklyDiff = function ($status = null) use ($role, $authUserId, $startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek) {
            $queryThisWeek = Task::query()->whereHas('creator');
            $queryLastWeek = Task::query()->whereHas('creator');

            if ($role !== 'super-admin') {
                $applyRole = function ($q) use ($authUserId) {
                    $q->where(function (Builder $sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                };
                $applyRole($queryThisWeek);
                $applyRole($queryLastWeek);
            }

            if ($status) {
                $queryThisWeek->where('status', $status);
                $queryLastWeek->where('status', $status);
            }

            $countThisWeek = $queryThisWeek->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $countLastWeek = $queryLastWeek->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

            $diff = $countThisWeek - $countLastWeek;

            if ($diff > 0) {
                return '+' . $diff . ' vs last week';
            } elseif ($diff < 0) {
                return $diff . ' vs last week';
            }

            return 'Same as last week';
        };

        $this->labels = [
            [
                'title' => 'Pending',
                'count' => $baseQuery()->where('status', 'pending')->count(),
                'status' => 'pending',
                'comparison' => $calculateWeeklyDiff('pending'),
                'bg' => '#fce2c7',
                'border' => '#f9d6b3',
            ],
            [
                'title' => 'In Progress',
                'count' => $baseQuery()->where('status', 'in_progress')->count(),
                'status' => 'in_progress',
                'comparison' => $calculateWeeklyDiff('in_progress'),
                'bg' => '#cbe6fc',
                'border' => '#afd7f9',
            ],
            [
                'title' => 'Completed',
                'count' => $baseQuery()->where('status', 'completed')->count(),
                'status' => 'completed',
                'comparison' => $calculateWeeklyDiff('completed'),
                'bg' => '#caf5de',
                'border' => '#9ffac9',
            ],
            [
                'title' => 'Total',
                'count' => $baseQuery()->count(),
                'status' => 'total',
                'comparison' => $calculateWeeklyDiff(),
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
            ->sortByDesc('pending')
            ->values()
            ->toArray();
    }

    private function setTeam(string $role, int $authUserId): void
    {
        /** @var Builder<User> $query */
        $query = User::query();

        if ($role !== 'super-admin') {
            $query->where('id', $authUserId);
        }

        $allMembers = $query->get()
            ->map(function ($user) use ($role, $authUserId) {
                $targetUserId = (int) $user->id;

                $assignedTaskIds = DB::table('task_assignments')
                    ->where('user_id', $targetUserId)
                    ->pluck('task_id');

                $createdTaskIds = Task::query()
                    ->where('user_id', $targetUserId)
                    ->pluck('id');

                $allUniqueTaskIds = $assignedTaskIds->merge($createdTaskIds)->unique()->values()->toArray();

                $taskBaseQuery = Task::query()
                    ->whereIn('id', $allUniqueTaskIds)
                    ->whereHas('creator');

                $this->applyDateFilters($taskBaseQuery);

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

                // PENDING RATIO CALCULATION (Pending Tasks / Total Tasks)
                $pendingPercentage = $total > 0 ? round(($pending / $total) * 100) : 0;

                return [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'name' => $fullName,
                    'pending' => $pending,
                    'in_progress' => $inProgress,
                    'completed' => $completed,
                    'total' => $total,
                    'percentage' => $pendingPercentage,
                ];
            });

        $this->team = $allMembers
            ->sortByDesc('pending')
            ->values()
            ->toArray();
    }

    private function setGroups(string $role, int $authUserId): void
    {
        $allGroups = Group::query()->get();

        $this->groups = $allGroups->map(function ($group) use ($role, $authUserId) {
            $groupIdStr = (string) $group->id;
            $groupLabel = $group->label ?? $group->name ?? '';

            $taskBaseQuery = Task::query()
                ->whereHas('creator')
                ->where(function (Builder $q) use ($group, $groupIdStr, $groupLabel) {
                    $q->where('tasks.label_id', $group->id)
                        ->orWhere('tasks.label_id', $groupIdStr)
                        ->orWhere('tasks.label_id', $groupLabel);
                });

            $this->applyDateFilters($taskBaseQuery);

            if ($role !== 'super-admin') {
                $taskBaseQuery->where(function ($sub) use ($authUserId) {
                    $sub->where('tasks.user_id', $authUserId)
                        ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                });
            }

            $pending = (clone $taskBaseQuery)->where('status', 'pending')->count();
            $inProgress = (clone $taskBaseQuery)->where('status', 'in_progress')->count();
            $completed = (clone $taskBaseQuery)->where('status', 'completed')->count();
            $total = (clone $taskBaseQuery)->count();

            return [
                'id' => $group->id,
                'name' => $groupLabel ?: 'No Group Name',
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'total' => $total,
                'percentage' => $total > 0
                    ? round(($completed / $total) * 100)
                    : 0,
            ];
        })
            ->sortByDesc('pending')
            ->values()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
