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
    /** @var \Illuminate\Database\Eloquent\Collection<int, User> */
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

        $allowedRoles = ['admin', 'super-admin', 'hr', 'manager', 'employee'];

        if (!Auth::check() || !$currentUser || !in_array(strtolower(trim($currentUser->role ?? '')), $allowedRoles, true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function loadUsers(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = User::query()->where('status', 1)->orderBy('created_at', 'desc')->get();
        $this->users = $users;
    }

    /**
     * Fetch user info along with Categories and Groups
     */
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

        if (! $authUser || ! in_array(strtolower(trim($authUser->role ?? '')), ['admin'], true)) {
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
