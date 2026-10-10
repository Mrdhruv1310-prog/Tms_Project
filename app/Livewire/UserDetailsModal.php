<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Locked;
use App\Mail\RegisterUserMail;
use Illuminate\Contracts\View\View;

class UserDetailsModal extends Component
{
    #[Locked]
    public string $route = '';
    public bool $isOpen = false;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $phone_number = null;
    public string $role = '';
    public string $status = '1'; // Default Active rakha hai

    public string $password = '';
    public ?int $user_id = null;
    public bool $submitted = true;
    public string $plainPassword = '';

    /** @var array<string, string> */
    protected $listeners = ['openModal' => 'open', 'closeModal' => 'close', 'edituser' => 'loadUser'];

    public function open(): void
    {
        $this->resetForm();
        $this->isOpen = true;
        $this->dispatch('addusermodalopened');
    }

    public function close(): void
    {
        $this->resetForm();
        $this->isOpen = false;
    }

    public function mount(): void
    {
        $this->route = Route::currentRouteName() ?? '';
    }

    public function saveUser(): mixed
    {
        $this->submitted = true;

        // Validation Rules
        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                $this->user_id ? 'unique:users,email,' . $this->user_id : 'unique:users,email',
            ],
            'phone_number' => [
                'required',
                'regex:/^[0-9]{10}$/'
            ],
            'role' => 'required|in:admin,manager,employee,hr',
            'status' => 'required|in:1,0',
            'password' => $this->user_id
                ? ['nullable', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%!]).+$/']
                : ['required', 'string', 'min:8', 'max:255', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%!]).+$/'],
        ];

        // Custom Validation Messages
        $messages = [
            'first_name.required' => 'Please enter first name.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'first_name.max' => 'First name must not exceed 255 characters.',

            'last_name.required' => 'Please enter last name.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'last_name.max' => 'Last name must not exceed 255 characters.',

            'email.required' => 'Please enter email address.',
            'email.regex' => 'Please enter a valid email format (e.g. user@gmail.com).',
            'email.unique' => 'This email is already registered.',

            'phone_number.regex' => 'Phone number must be exactly 10 digits.',
            'phone_number.required' => 'Please enter phone number.',
            
            'role.required' => 'Please select a role.',
            'role.in' => 'Please select a valid role.',

            'status.required' => 'Please select status.',
            'status.in' => 'Please select a valid status.',

            'password.required' => 'Please enter password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least 1 uppercase (A-Z), 1 lowercase (a-z), 1 number (0-9), and 1 special character (@ # $ % !).',
        ];

        $this->validate($rules, $messages);

        // HERE: $statusValue logic yahan add karein
        if (!$this->user_id) {
            // Insert karte waqt force Active ('1') set hoga
            $statusValue = '1';
        } else {
            // Edit karte waqt selected value save hogi
            $statusValue = $this->status;
        }

        $plainPassword = '';

        if ($this->user_id) {
            /** @var User $user */
            $user = User::query()->findOrFail($this->user_id);

            $updateData = [
                'first_name' => trim($this->first_name),
                'last_name' => trim($this->last_name),
                'email' => trim($this->email),
                'phone_number' => trim($this->phone_number),
                'role' => $this->role,
                'status' => $statusValue,
            ];

            if (!empty($this->password)) {
                $plainPassword = $this->password;
                $updateData['password'] = Hash::make($plainPassword);
            }

            $user->update($updateData);

            if (!empty($this->password)) {
                $user->refresh();
                Mail::to($user->email)->send(new RegisterUserMail($user, $plainPassword));
            }

            $message = 'Employee updated successfully.';
        } else {
            $plainPassword = $this->password;

            /** @var User $user */
            $user = User::query()->create([
                'first_name' => trim($this->first_name),
                'last_name' => trim($this->last_name),
                'email' => trim($this->email),
                'phone_number' => trim($this->phone_number),
                'role' => $this->role,
                'status' => $statusValue,
                'password' => Hash::make($plainPassword),
            ]);

            Mail::to($user->email)->send(new RegisterUserMail($user, $plainPassword));

            $message = 'Employee added successfully.';
        }

        $this->resetForm();
        $this->close();
        $this->dispatch('usercreated');

        if ($this->route === 'users') {
            $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
        } else {
            session()->flash('message', $message);
            return $this->redirect('users', navigate: true);
        }

        return null;
    }

    public function loadUser(int|string $id): void
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $this->user_id = (int) $user->getKey();
        $this->first_name = is_string($user->getAttribute('first_name')) ? $user->getAttribute('first_name') : null;
        $this->last_name = is_string($user->getAttribute('last_name')) ? $user->getAttribute('last_name') : null;
        $this->email = is_string($user->getAttribute('email')) ? $user->getAttribute('email') : null;
        $this->phone_number = is_string($user->getAttribute('phone_number')) ? $user->getAttribute('phone_number') : null;
        $this->role = (string) $user->getAttribute('role');
        $this->status = (string) $user->getAttribute('status');

        $this->isOpen = true;
    }

    public function resetForm(): void
    {
        $this->reset(['first_name', 'last_name', 'email', 'phone_number', 'role', 'user_id', 'password']);
        $this->status = '1'; // Default Active for Insert
        $this->submitted = false;
    }

    public function render(): View
    {
        return view('livewire.user-details-modal');
    }
}
