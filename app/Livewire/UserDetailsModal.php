<?php

namespace App\Livewire;

use App\Mail\SendResetPasswordEmail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Locked;

class UserDetailsModal extends Component
{
    #[Locked]
    public string $route;
    public bool $isOpen = false;
    public $first_name;
    public $last_name;
    public $email;
    public $phone_number;
    public $role = 'user'; // Default role to 'user'
    public $status = true; // Default status to active (true)
    public $user_id; // To store the user ID for editing

    protected $listeners = ['openModal' => 'open', 'closeModal' => 'close', 'edituser' => 'loadUser'];

    public function open()
    {
        $this->resetForm(); // Reset form when closing modal
        $this->isOpen = true;
        $this->dispatch('addusermodalopened');
    }

    public function close()
    {
        $this->resetForm(); // Reset form when closing modal
        $this->isOpen = false;
    }

    public function mount(): void
    {
        $this->route = Route::currentRouteName();
    }

    public function saveUser()
    {
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email' . ($this->user_id ? '|unique:users,email,' . $this->user_id : '|unique:users,email'),
            'phone_number' => 'nullable|string|max:15',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:1,0',
        ];

        $this->validate($rules);
        if ($this->user_id) {
            // Editing existing user
            $user = User::findOrFail($this->user_id);
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'role' => $this->role,
                'status' => $this->status,
            ]);
            $message = 'User updated successfully.';
        } else {
           
            // Creating new user
            $password = Str::random(12); // Generate a random password
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'role' => $this->role,
                'status' => $this->status,
                'password' => Hash::make($password), // Hash the random password
            ]);
            // Clear any existing reset tokens for the same email
            PasswordResetToken::where('email', $user->email)->delete();
            // Create a password reset token

            $token = Str::random(60);
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);
            try {
                //code...
                Mail::to($user->email)->send(new SendResetPasswordEmail($user, $token));
                $message = 'User added successfully.';
            } catch (\Throwable $th) {
                //throw $th;
                $message = 'User added successfully, but password reset email not sent';
            }
            // Send password reset email directly
        }

        $this->resetForm();
        $this->close();
        $this->dispatch('usercreated');

        if ($this->route === 'users') {
            $this->notify($message, 'success');
        } else {
            session()->flash('message', $message);
            return $this->redirect('users', navigate: true);
        }
    }

    public function loadUser($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->role = $user->role;
        $this->status = $user->status;

        $this->isOpen = true;
    }

    public function resetForm()
    {
        $this->reset(['first_name', 'last_name', 'email', 'phone_number', 'role', 'status', 'user_id']);
    }

    public function render()
    {
        return view('livewire.user-details-modal');
    }
}
