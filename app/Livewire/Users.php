<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class Users extends Component
{
    public $users;

    #[On('usercreated')]
    #[On('userupdated')]
    public function mount()
    {
        $this->users = User::where('status', 1)->orderBy('created_at', 'desc')->get();
    }

    public function delete(User $user)
    {
        try {

            if (!auth()->check() || auth()->user()->role !== 'admin') {
                $this->dispatch('notify', message: 'You are not authorized to delete users.', type: 'error');
                return;
            }

            if ($user->tasks()->count() > 0) {
                $this->dispatch('notify', message: 'User has assigned tasks. Cannot delete.', type: 'warning');
                return;
            }

            DB::transaction(function () use ($user) {

                $user->notifications()->delete();
                $user->reminders()->delete();
                $user->tasks()->detach();
                $user->groups()->detach();
                $user->delete();
            });

            $this->users = User::whereStatus(1)->latest()->get();

            $this->dispatch('userdeleted');
            $this->dispatch('notify', message: 'User deleted successfully.', type: 'success');
        } catch (\Throwable $e) {
            Log::error("User delete failed: " . $e->getMessage());
            $this->dispatch('notify', message: 'Unable to delete user.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.users', ['users' => $this->users,])->layout('components.layouts.app', ['title' => 'Manage Users']);
    }
}
