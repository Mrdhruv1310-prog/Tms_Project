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
        // Yahan 'admin' aur 'super-admin' dono ko access diya gaya hai taaki 403 error na aaye
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $this->users = User::where('status', 1)->orderBy('created_at', 'desc')->get();
    }

    public function delete(User $user)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            $this->dispatch('notify', message: 'You are not authorized to delete users.', type: 'error');
            return;
        }

        if ($user->tasks()->count() > 0) {
            $this->dispatch('notify', message: 'User has assigned tasks. Cannot delete.', type: 'warning');
            return;
        }

        try {
            DB::transaction(function () use ($user) {
                $user->notifications()->delete();
                $user->reminders()->delete();
                DB::table('task_assignments')->where('user_id', $user->id)->delete();
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
        return view('livewire.users', ['users' => $this->users])->layout('components.layouts.app', ['title' => 'Manage Users']);
    }
}
