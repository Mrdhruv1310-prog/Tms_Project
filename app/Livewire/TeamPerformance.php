<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Database\Eloquent\Builder;

#[Layout('components.layouts.app')]
#[Title('Team Performance | TMS')]
class TeamPerformance extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $team = [];

    public function mount(): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (! $authUser) {
            abort(403, 'Unauthorized action.');
        }

        $authUserId = (int) $authUser->id;
        $role = strtolower($authUser->role ?? 'employee');
        $isAdmin = in_array($role, ['admin', 'manager', 'hr', 'super-admin'], true);

        if ($isAdmin) {
            $userQuery = User::query();
        } else {
            $relevantUserIds = collect([$authUserId]);

            // Group Members
            if (!empty($authUser->group_id)) {
                $sameGroupUserIds = User::where('group_id', $authUser->group_id)->pluck('id');
                $relevantUserIds = $relevantUserIds->merge($sameGroupUserIds);
            }

            // Task Creators (Users who assigned tasks to Auth User)
            $creatorsOfMyTasks = Task::query()
                ->where(function ($q) use ($authUserId) {
                    $q->whereHas('taskAssignments', fn($sub) => $sub->where('user_id', $authUserId))
                        ->orWhere('user_id', $authUserId);
                })
                ->pluck('user_id');
            $relevantUserIds = $relevantUserIds->merge($creatorsOfMyTasks);

            // Assignees (Users assigned to tasks created by Auth User)
            $assigneesOfMyTasks = DB::table('task_assignments')
                ->whereIn('task_id', Task::where('user_id', $authUserId)->pluck('id'))
                ->pluck('user_id');
            $relevantUserIds = $relevantUserIds->merge($assigneesOfMyTasks);

            $uniqueUserIds = $relevantUserIds->filter()->unique()->values()->toArray();

            $userQuery = User::query()->whereIn('id', $uniqueUserIds);
        }

        $allMembers = $userQuery->get()
            ->map(function (User $user) use ($isAdmin, $authUserId): array {
                $targetUserId = (int) $user->id;

                // Extract UNIQUE task IDs where target user is Creator or Assignee
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

                if (! $isAdmin && $targetUserId !== $authUserId) {
                    $taskBaseQuery->where(function (Builder $q) use ($authUserId, $targetUserId, $user) {
                        $q->where(function (Builder $sub) use ($authUserId, $targetUserId) {
                            $sub->where('user_id', $authUserId)
                                ->whereHas('taskAssignments', fn(Builder $a) => $a->where('user_id', $targetUserId));
                        })
                            ->orWhere(function (Builder $sub) use ($authUserId, $targetUserId) {
                                $sub->where('user_id', $targetUserId)
                                    ->whereHas('taskAssignments', fn(Builder $a) => $a->where('user_id', $authUserId));
                            });

                        if (!empty(Auth::user()->group_id) && (int) Auth::user()->group_id === (int) $user->group_id) {
                            $q->orWhere('user_id', $targetUserId);
                        }
                    });
                }

                $pending = (clone $taskBaseQuery)->where('status', 'pending')->count();
                $inProgress = (clone $taskBaseQuery)->where('status', 'in_progress')->count();
                $completed = (clone $taskBaseQuery)->where('status', 'completed')->count();
                $total = (clone $taskBaseQuery)->count();

                $firstName = is_string($user->getAttribute('first_name')) ? $user->getAttribute('first_name') : '';
                $lastName = is_string($user->getAttribute('last_name')) ? $user->getAttribute('last_name') : '';
                $fullName = trim($firstName . ' ' . $lastName);

                if (empty($fullName)) {
                    $fullName = is_string($user->getAttribute('name')) ? $user->getAttribute('name') : 'Team Member';
                }

                return [
                    'id' => $user->id,
                    'name' => $fullName,
                    'completed' => $completed,
                    'in_progress' => $inProgress,
                    'pending' => $pending,
                    'total' => $total,
                    'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
                ];
            })
            ->filter(fn($m) => $m['pending'] > 0)
            ->values();

        // Ensure Auth User is Always 1st Card
        $authUserCard = $allMembers->firstWhere('id', $authUserId);
        $otherCards = $allMembers->reject(fn($m) => $m['id'] === $authUserId)->values();

        $sortedTeam = collect();
        if ($authUserCard) {
            $sortedTeam->push($authUserCard);
        }

        $this->team = $sortedTeam->concat($otherCards)->toArray();
    }

    public function render(): mixed
    {
        return view('livewire.team-performance');
    }
}
