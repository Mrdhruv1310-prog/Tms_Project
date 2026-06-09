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
    public $role = ''; // Default role to 'user'
    public $status = ''; // Default status to active (true)

    public $password = ''; // Default password is empty

    public $user_id; // To store the user ID for editing

    public $submitted = true;

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
        $this->submitted = true;
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email' . ($this->user_id ? '|unique:users,email,' . $this->user_id : '|unique:users,email'),
            'phone_number' => 'required|string|max:15',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:1,0',
        ];

        $messages = [
            'first_name.required' => 'Please enter the first name.',
            'last_name.required' => 'Please enter the last name.',
            'email.required' => 'Please enter the email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email address has already been registered.',
            'role.required' => 'Please select a role.',
            'role.in' => 'Please select a valid role.',
            'status.required' => 'Please select the status.',
        ];

        $this->validate($rules, $messages);

        if ($this->user_id) {
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
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'role' => $this->role,
                'status' => $this->status,
                'password' => Hash::make($this->password),
            ]);

            PasswordResetToken::where('email', $user->email)->delete();

            $token = Str::random(60);
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now(),
            ]);
            try {
                // Mail::to($user->email)->send(new SendResetPasswordEmail($user, $token));
                $message = 'User added successfully.';
            } catch (\Throwable $th) {
                $message = 'User added successfully, but password reset email not sent';
            }
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
