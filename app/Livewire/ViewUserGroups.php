<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class ViewUserGroups extends Component
{
    public bool $isOpen = false;
    public ?int $labelId = null;
    public string $labelName = '';

    /** @var array<int, array<string, mixed>> */
    public array $groupUsers = [];

    /** @var array<int, array<string, mixed>> */
    public array $availableUsers = [];

    /** @var array<string, string> */
    protected $listeners = ['openUserGroupModal' => 'loadUsers'];

    public function loadUsers(mixed $labelId): void
    {
        $resolvedLabelId = is_array($labelId) ? ($labelId['labelId'] ?? null) : $labelId;

        if (empty($resolvedLabelId)) {
            $this->labelName = 'Unknown Group';
            $this->groupUsers = [];
            return;
        }

        $this->labelId = (int) $resolvedLabelId;

        /** @var Group|null $group */
        $group = Group::query()->find($this->labelId);

        if (!$group) {
            $this->labelName = 'Unknown Group';
            $this->groupUsers = [];
            return;
        }

        $groupLabel = $group->getAttribute('label');
        $this->labelName = is_string($groupLabel) && !empty($groupLabel)
            ? $groupLabel
            : 'Unknown Group';

        // Fix: Removed 'users.name' from select list
        if (method_exists($group, 'users')) {
            $this->groupUsers = $group->users()
                ->get(['users.id', 'users.first_name', 'users.last_name'])
                ->map(function ($u) {
                    $fn = $u->first_name ?? '';
                    $ln = $u->last_name ?? '';
                    $fullName = trim($fn . ' ' . $ln);

                    return [
                        'id' => $u->id,
                        'first_name' => $u->first_name ?: ($fullName ?: 'User'),
                        'last_name' => $u->last_name ?: '',
                        'name' => $fullName ?: 'User #' . $u->id
                    ];
                })
                ->toArray();
        } else {
            $this->groupUsers = User::query()
                ->where('group_id', $this->labelId)
                ->get(['id', 'first_name', 'last_name'])
                ->map(function ($u) {
                    $fn = $u->first_name ?? '';
                    $ln = $u->last_name ?? '';
                    $fullName = trim($fn . ' ' . $ln);

                    return [
                        'id' => $u->id,
                        'first_name' => $u->first_name ?: ($fullName ?: 'User'),
                        'last_name' => $u->last_name ?: '',
                        'name' => $fullName ?: 'User #' . $u->id
                    ];
                })
                ->toArray();
        }

        $this->fetchAvailableUsers();
        $this->isOpen = true;
        $this->dispatch('viewusergroupmodalopened');
    }

    public function fetchAvailableUsers(): void
    {
        $existingUserIds = array_column($this->groupUsers, 'id');
        $currentUserId = Auth::id();

        // Fix: Removed 'name' column selection from User query
        /** @var \Illuminate\Database\Eloquent\Builder<User> $query */
        $query = User::query()
            ->select('id', 'first_name', 'last_name', 'role')
            ->whereNotIn('id', $existingUserIds);

        if ($currentUserId) {
            $query->where('id', '!=', $currentUserId);
        }

        $query->where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'super-admin');
        });

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = $query->get();

        $this->availableUsers = $users
            ->map(function (User $user): array {
                $firstName = (string) ($user->first_name ?? '');
                $lastName = (string) ($user->last_name ?? '');
                $fullName = trim($firstName . ' ' . $lastName);

                if (empty($fullName)) {
                    $fullName = 'User #' . $user->id;
                }

                return [
                    'id' => (int) $user->getKey(),
                    'name' => $fullName,
                    'role' => $user->role ?? 'user',
                ];
            })
            ->toArray();
    }

    public function addUser(int $userId): void
    {
        /** @var Group|null $group */
        $group = Group::query()->find($this->labelId);

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if (!$group || !$user) {
            return;
        }

        if (method_exists($group, 'users')) {
            $group->users()->syncWithoutDetaching([$userId]);
        } else {
            $user->update(['group_id' => $group->id]);
        }

        $this->loadUsers($this->labelId);

        $userName = trim(ucfirst($user->first_name ?? '') . ' ' . ucfirst($user->last_name ?? ''));
        if (empty($userName)) {
            $userName = 'User #' . $user->id;
        }

        $this->dispatch('notify', [
            'message' => "Added {$userName} to the " . ucwords($this->labelName) . " group.",
            'type' => 'success'
        ]);
    }

    public function deleteUser(int $userId): void
    {
        /** @var Group|null $group */
        $group = Group::query()->find($this->labelId);

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if ($group && $user) {
            if (method_exists($group, 'users')) {
                $group->users()->detach($userId);
            } else {
                $user->update(['group_id' => null]);
            }

            $this->loadUsers($this->labelId);

            $userName = trim(ucfirst($user->first_name ?? '') . ' ' . ucfirst($user->last_name ?? ''));
            if (empty($userName)) {
                $userName = 'User #' . $user->id;
            }

            $this->dispatch('notify', [
                'message' => "Removed {$userName} from the " . ucwords($this->labelName) . " group.",
                'type' => 'success'
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.view-user-groups');
    }
}
