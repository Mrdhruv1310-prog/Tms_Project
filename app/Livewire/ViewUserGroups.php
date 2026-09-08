<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ViewUserGroups extends Component
{
    public $isOpen = false; // Controls modal visibility
    public $labelId; // Group ID
    public $labelName; // Group Name (optional)

    public $groupUsers = []; // Users already in the group
    public $availableUsers = []; // Users available for the dropdown

    protected $listeners = ['openUserGroupModal' => 'loadUsers'];

    // Load users for the group
    public function loadUsers($labelId)
    {
        if (empty($labelId)) {
            $this->labelName = 'Unknown Group';
            $this->groupUsers = [];
            return;
        }

        $this->labelId = $labelId;
        $group = Group::find($labelId);

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

    // Fetch users not already in the group
    public function fetchAvailableUsers()
    {
        $existingUserIds = array_column($this->groupUsers ?? [], 'id');

        $currentUser = Auth::user();

        $query = User::select('id', 'first_name', 'last_name', 'role')
            ->whereNotIn('id', $existingUserIds);

        // Logged-in user ko dropdown mein kabhi show nahi karna
        if ($currentUser) {
            $query->where('id', '!=', $currentUser->id);

            // Admin sirf normal users ko group mein add kar sakta hai
            if ($currentUser->role === 'admin') {
                $query->where('role', 'user');
            }

            // Super-admin admin + user dono ko add kar sakta hai
            elseif ($currentUser->role === 'super-admin') {
                $query->whereIn('role', ['admin', 'user']);
            }
        }

        $this->availableUsers = $query
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => trim(
                        (string)($user->first_name ?? '') . ' ' .
                            (string)($user->last_name ?? '')
                    ),
                ];
            })
            ->toArray();
    }

    // Add a user to the group
    public function addUser($userId)
    {
        $group = Group::find($this->labelId);
        $user = User::find($userId);
        $currentUser = Auth::user();

        if (!$group || !$user || !$currentUser) {
            return;
        }

        // Logged-in user khud ko add nahi kar sakta
        if ($user->id === $currentUser->id) {
            $this->notify(
                'You cannot add yourself to the group.',
                'error'
            );

            return;
        }

        // Admin sirf normal users ko add kar sakta hai
        if (
            $currentUser->role === 'admin' &&
            $user->role !== 'user'
        ) {
            $this->notify(
                'Admin can only add users to the group.',
                'error'
            );

            return;
        }

        // Super-admin admin aur user dono ko add kar sakta hai
        if (
            $currentUser->role === 'super-admin' &&
            !in_array($user->role, ['admin', 'user'])
        ) {
            $this->notify(
                'Super-admin can add only admin and user accounts.',
                'error'
            );

            return;
        }

        $group->users()->syncWithoutDetaching([$userId]);

        $this->loadUsers($this->labelId);

        $this->notify(
            "Added " . ucfirst($user->first_name ?? '') . " " .
                ucfirst($user->last_name ?? '') . " to the " .
                ucwords($this->labelName ?? 'Group') . " group.",
            'success'
        );
    }

    // Remove a user from the group
    public function deleteUser($userId)
    {
        $group = Group::find($this->labelId);
        $user = User::find($userId); // Fetch user information
        if ($group && $user) {
            $group->users()->detach($userId);
            $this->loadUsers($this->labelId); // Reload data
            $this->notify(
                "Removed " . ucfirst($user->first_name ?? '') . " " .
                    ucfirst($user->last_name ?? '') . " from the " .
                    ucwords($this->labelName ?? 'Group') . " group.",
                'success'
            );
        }
    }

    public function render()
    {
        return view('livewire.view-user-groups');
    }
}
