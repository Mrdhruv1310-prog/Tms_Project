<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ViewUserGroups extends Component
{
    public bool $isOpen = false;
    public ?int $labelId = null;
    public string $labelName = '';
    public array $groupUsers = [];
    public array $availableUsers = [];

    protected $listeners = ['openUserGroupModal' => 'loadUsers'];

    public function loadUsers($labelId): void
    {
        $resolvedLabelId = is_array($labelId) ? ($labelId['labelId'] ?? null) : $labelId;

        if (empty($resolvedLabelId)) {
            $this->labelName = 'Unknown Group';
            $this->groupUsers = [];
            return;
        }

        $this->labelId = (int) $resolvedLabelId;
        $group = Group::find($this->labelId);

        if (!$group) {
            $this->labelName = 'Unknown Group';
            $this->groupUsers = [];
            return;
        }

        $this->labelName = is_string($group->label) && !empty($group->label)
            ? $group->label
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
        $existingUserIds = array_column($this->groupUsers ?? [], 'id');
        $currentUser = Auth::user();
        $currentUserId = Auth::id();

        $query = User::select('id', 'first_name', 'last_name', 'role')
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

        $this->availableUsers = $query
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => trim(
                        (string) ($user->first_name ?? '') . ' ' .
                            (string) ($user->last_name ?? '')
                    ),
                ];
            })
            ->toArray();
    }

    public function addUser($userId): void
    {
        $group = Group::find($this->labelId);
        $user = User::find($userId);
        $currentUser = Auth::user();
        $currentUserId = Auth::id();

        if (!$group || !$user || !$currentUser || !$currentUserId) {
            return;
        }

        if ($user->id === $currentUserId) {
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
            'message' => "Added {$userName} to the " . ucwords($this->labelName ?? 'Group') . " group.",
            'type' => 'success'
        ]);
    }

    public function deleteUser($userId): void
    {
        $group = Group::find($this->labelId);
        $user = User::find($userId);

        if ($group && $user) {
            $group->users()->detach($userId);
            $this->loadUsers($this->labelId);

            $userName = trim(ucfirst($user->first_name ?? '') . ' ' . ucfirst($user->last_name ?? ''));
            $this->dispatch('notify', [
                'message' => "Removed {$userName} from the " . ucwords($this->labelName ?? 'Group') . " group.",
                'type' => 'success'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.view-user-groups');
    }
}
