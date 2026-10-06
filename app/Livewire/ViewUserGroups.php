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

        $this->groupUsers = $group->users()
            ->get(['users.id', 'users.first_name', 'users.last_name'])
            ->toArray();

        $this->fetchAvailableUsers();
        $this->isOpen = true;
        $this->dispatch('viewusergroupmodalopened');
    }

    public function fetchAvailableUsers(): void
    {
        $existingUserIds = array_column($this->groupUsers, 'id');
        $currentUser = Auth::user();
        $currentUserId = Auth::id();

        /** @var \Illuminate\Database\Eloquent\Builder<User> $query */
        $query = User::query()->select('id', 'first_name', 'last_name', 'role')
            ->whereNotIn('id', $existingUserIds);

        if ($currentUser && $currentUserId) {
            $query->where('id', '!=', $currentUserId);

            $role = $currentUser->role ?? '';
            if ($role === 'admin') {
                $query->where('role', 'user');
            } elseif ($role === 'super-admin') {
                $query->whereIn('role', ['admin', 'user']);
            }
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = $query->get();

        $this->availableUsers = $users
            ->map(function (User $user): array {
                return [
                    'id' => (int) $user->getKey(),
                    'name' => trim(
                        (string) ($user->first_name ?? '') . ' ' .
                            (string) ($user->last_name ?? '')
                    ),
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

        $currentUser = Auth::user();
        $currentUserId = Auth::id();

        if (!$group || !$user || !$currentUser || !$currentUserId) {
            return;
        }

        if ((int) $user->getKey() === (int) $currentUserId) {
            $this->dispatch('notify', ['message' => 'You cannot add yourself to the group.', 'type' => 'error']);
            return;
        }

        $role = $currentUser->role ?? '';

        if ($role === 'admin' && $user->role !== 'user') {
            $this->dispatch('notify', ['message' => 'Admin can only add users to the group.', 'type' => 'error']);
            return;
        }

        if ($role === 'super-admin' && !in_array($user->role, ['admin', 'user'], true)) {
            $this->dispatch('notify', ['message' => 'Super-admin can add only admin and user accounts.', 'type' => 'error']);
            return;
        }

        $group->users()->syncWithoutDetaching([$userId]);
        $this->loadUsers($this->labelId);

        $userName = trim(ucfirst($user->first_name ?? '') . ' ' . ucfirst($user->last_name ?? ''));
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
            $group->users()->detach($userId);
            $this->loadUsers($this->labelId);

            $userName = trim(ucfirst($user->first_name ?? '') . ' ' . ucfirst($user->last_name ?? ''));
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
