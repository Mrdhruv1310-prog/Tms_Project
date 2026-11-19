<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

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

    public string $selectedStatusFilter = 'total';
    public ?string $selectedDate = null;

    public function mount(): void
    {
        $this->selectedDate = session('dashboard_date');
        $this->refreshDashboardData();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return filled($this->selectedDate) ? 1 : 0;
    }

    public function updatedSelectedDate(): void
    {
        session(['dashboard_date' => $this->selectedDate]);
        $this->refreshDashboardData();
    }

    public function filterByStatus(string $status): void
    {
        $this->selectedStatusFilter = $status;
        $this->refreshDashboardData();
    }

    public function filterCategoryPendingTasks(string $categoryName): RedirectResponse|Redirector
    {
        $queryParams = array_merge($this->getDateQueryParams(), [
            'category' => $categoryName,
            'status' => 'pending',
        ]);

        return $this->redirect('/tasks?' . http_build_query($queryParams), navigate: true);
    }

    public function navigateToUserPendingTasks(int $userId): RedirectResponse|Redirector
    {
        $queryParams = array_merge($this->getDateQueryParams(), [
            'user_id' => $userId,
        ]);

        return $this->redirect('/tasks/user/pending?' . http_build_query($queryParams), navigate: true);
    }

    public function navigateToGroupTasks(int $groupId): RedirectResponse|Redirector
    {
        $group = Group::find($groupId);
        $groupName = $group ? ($group->label ?? $group->name) : '';

        $queryParams = $this->getDateQueryParams();
        if (filled($groupName)) {
            $queryParams['group'] = $groupName;
        }

        return $this->redirect('/tasks?' . http_build_query($queryParams), navigate: true);
    }

    public function clearDateFilters(): void
    {
        $this->selectedDate = null;
        session()->forget('dashboard_date');
        $this->refreshDashboardData();
    }

    /**
     * @return array<string, string>
     */
    public function getDateQueryParams(): array
    {
        return filled($this->selectedDate) ? ['date' => (string) $this->selectedDate] : [];
    }

    #[On('taskCreated')]
    #[On('taskStatusUpdated')]
    #[On('taskDeleted')]
    #[On('userDeleted')]
    public function refreshDashboardData(): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        if (! $authUser) {
            return;
        }

        $authUserId = (int) $authUser->id;
        $role = strtolower((string) ($authUser->role ?? 'employee'));

        $this->setLabels($role, $authUserId);
        $this->setCategories($role, $authUserId);
        $this->setTeam($role, $authUserId);
        $this->setGroups($role, $authUserId);
    }

    private function applyDateFilters(Builder $query): void
    {
        if (filled($this->selectedDate)) {
            try {
                $formattedDate = Carbon::parse($this->selectedDate)->format('Y-m-d');
                $query->whereDate('created_at', $formattedDate);
            } catch (\Throwable) {
                $this->selectedDate = null;
                session()->forget('dashboard_date');
            }
        }
    }

    private function roleBasedTaskQuery(string $role, int $authUserId): Builder
    {
        $query = Task::query()->whereHas('creator');
        $this->applyDateFilters($query);

        if ($role === 'super-admin') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($authUserId): void {
            $q->where('tasks.user_id', $authUserId)
                ->orWhereHas('taskAssignments', function (Builder $aq) use ($authUserId): void {
                    $aq->where('user_id', $authUserId)->whereHas('user');
                });
        });
    }

    private function setLabels(string $role, int $authUserId): void
    {
        $baseQuery = fn(): Builder => $this->roleBasedTaskQuery($role, $authUserId);

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $calculateWeeklyDiff = function (?string $status = null) use ($role, $authUserId, $startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek): string {
            $queryThisWeek = Task::query()->whereHas('creator');
            $queryLastWeek = Task::query()->whereHas('creator');

            if ($role !== 'super-admin') {
                $applyRole = function (Builder $q) use ($authUserId): void {
                    $q->where(function (Builder $sub) use ($authUserId): void {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn(Builder $aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                    });
                };
                $applyRole($queryThisWeek);
                $applyRole($queryLastWeek);
            }

            if ($status !== null) {
                $queryThisWeek->where('status', $status);
                $queryLastWeek->where('status', $status);
            }

            $countThisWeek = $queryThisWeek->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $countLastWeek = $queryLastWeek->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

            $diff = $countThisWeek - $countLastWeek;

            return match (true) {
                $diff > 0 => "+{$diff} vs last week",
                $diff < 0 => "{$diff} vs last week",
                default => 'Same as last week',
            };
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
                'tasks as pending_tasks_count' => function (Builder $query) use ($role, $authUserId): void {
                    $query->where('tasks.status', 'pending')->whereHas('creator');
                    $this->applyDateFilters($query);

                    if ($role !== 'super-admin') {
                        $query->where(function (Builder $sub) use ($authUserId): void {
                            $sub->where('tasks.user_id', $authUserId)
                                ->orWhereHas('taskAssignments', fn(Builder $aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                        });
                    }
                },
                'tasks as total_tasks_count' => function (Builder $query) use ($role, $authUserId): void {
                    $query->whereHas('creator');
                    $this->applyDateFilters($query);

                    if ($role !== 'super-admin') {
                        $query->where(function (Builder $sub) use ($authUserId): void {
                            $sub->where('tasks.user_id', $authUserId)
                                ->orWhereHas('taskAssignments', fn(Builder $aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                        });
                    }
                },
            ])
            ->get()
            ->map(function (Category $category): array {
                $pending = (int) ($category->pending_tasks_count ?? 0);
                $total = (int) ($category->total_tasks_count ?? 0);

                return [
                    'id' => $category->id,
                    'title' => $category->name,
                    'creator_name' => $category->creator ? ($category->creator->first_name ?? $category->creator->name) : 'N/A',
                    'pending' => $pending,
                    'total' => $total,
                    'percentage' => $total > 0 ? round(($pending / $total) * 100) : 0,
                ];
            })
            ->sortByDesc('pending')
            ->values()
            ->toArray();
    }

    private function setTeam(string $role, int $authUserId): void
    {
        $query = User::query();

        if ($role !== 'super-admin') {
            $query->where('id', $authUserId);
        }

        $allMembers = $query->get()->map(function (User $user) use ($role, $authUserId): array {
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
                $taskBaseQuery->where(function (Builder $q) use ($authUserId, $targetUserId): void {
                    $q->where(function (Builder $sub) use ($authUserId, $targetUserId): void {
                        $sub->where('user_id', $authUserId)
                            ->whereHas('taskAssignments', fn(Builder $a) => $a->where('user_id', $targetUserId));
                    })->orWhere(function (Builder $sub) use ($authUserId, $targetUserId): void {
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
            if ($fullName === '') {
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
                'percentage' => $total > 0 ? round(($pending / $total) * 100) : 0,
            ];
        });

        $this->team = $allMembers->sortByDesc('pending')->values()->toArray();
    }

    private function setGroups(string $role, int $authUserId): void
    {
        $this->groups = Group::query()->get()->map(function (Group $group) use ($role, $authUserId): array {
            $groupIdStr = (string) $group->id;
            $groupLabel = $group->label ?? $group->name ?? '';

            $taskBaseQuery = Task::query()
                ->whereHas('creator')
                ->where(function (Builder $q) use ($group, $groupIdStr, $groupLabel): void {
                    $q->where('tasks.label_id', $group->id)
                        ->orWhere('tasks.label_id', $groupIdStr)
                        ->orWhere('tasks.label_id', $groupLabel);
                });

            $this->applyDateFilters($taskBaseQuery);

            if ($role !== 'super-admin') {
                $taskBaseQuery->where(function (Builder $sub) use ($authUserId): void {
                    $sub->where('tasks.user_id', $authUserId)
                        ->orWhereHas('taskAssignments', fn(Builder $aq) => $aq->where('user_id', $authUserId)->whereHas('user'));
                });
            }

            $pending = (clone $taskBaseQuery)->where('status', 'pending')->count();
            $inProgress = (clone $taskBaseQuery)->where('status', 'in_progress')->count();
            $completed = (clone $taskBaseQuery)->where('status', 'completed')->count();
            $total = (clone $taskBaseQuery)->count();

            return [
                'id' => $group->id,
                'name' => filled($groupLabel) ? $groupLabel : 'No Group Name',
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'total' => $total,
                'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        })->sortByDesc('pending')->values()->toArray();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
