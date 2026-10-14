<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Employee List')]
class Users extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, User>|array */
    public $users;

    // Modal control
    public bool $userDetailsModalOpen = false;
    public ?User $selectedUser = null;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->loadUsers();
    }

    #[On('usercreated')]
    #[On('userupdated')]
    public function handleUserRefresh(): void
    {
        $this->loadUsers();
    }

    private function authorizeAccess(): void
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $allowedRoles = ['super-admin', 'admin', 'hr', 'manager', 'employee'];

        if (!Auth::check() || !$currentUser || !in_array(strtolower(trim($currentUser->role ?? '')), $allowedRoles, true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function loadUsers(): void
    {
        $authUserId = Auth::id();

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $allUsers */
        $allUsers = User::query()->where('status', 1)->orderBy('created_at', 'desc')->get();

        // Keep Auth User ALWAYS on 1st Position
        $authUserObj = $allUsers->firstWhere('id', $authUserId);
        $otherUsers = $allUsers->reject(fn($u) => $u->id === $authUserId)->values();

        $sortedList = collect();
        if ($authUserObj) {
            $sortedList->push($authUserObj);
        }

        $this->users = $sortedList->concat($otherUsers);
    }

    public function editUser(int $userId): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (! $authUser) {
            return;
        }

        $authUserId = (int) $authUser->id;
        $userRole = strtolower(trim($authUser->role ?? ''));

        // Permission: Super Admin can edit anyone, OR Auth user can edit their own profile
        if ($userRole !== 'super-admin' && $authUserId !== $userId) {
            $this->dispatch('notify', ['message' => 'Aap sirf apna profile edit kar sakte hain.', 'type' => 'error']);
            return;
        }

        // Open modal event dispatch with target user ID
        $this->dispatch('openUserModal', userId: $userId);
        $this->dispatch('open-user-modal', userId: $userId);
    }

    public function showUserDetails(int $userId): void
    {
        $this->selectedUser = User::with(['categories', 'groups'])->find($userId);
        $this->userDetailsModalOpen = true;
    }

    public function closeUserDetailsModal(): void
    {
        $this->userDetailsModalOpen = false;
        $this->selectedUser = null;
    }

    public function delete(int $userId): void
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        // SIRF SUPER-ADMIN HI DELETE KAR SAKTA HAI
        if (! $authUser || strtolower(trim($authUser->role ?? '')) !== 'super-admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($authUser->id === $userId) {
            $this->dispatch('notify', ['message' => 'Aap khud ko delete nahi kar sakte.', 'type' => 'error']);
            return;
        }

        \App\Models\Reminder::query()->where('user_id', $userId)->delete();

        $user = User::query()->findOrFail($userId);
        $user->delete();

        $this->dispatch('userdeleted');
        $this->dispatch('notify', ['message' => 'User successfully deleted.', 'type' => 'success']);
    }

    public function render(): mixed
    {
        return view('livewire.users', [
            'users' => $this->users,
        ]);
    }
}
