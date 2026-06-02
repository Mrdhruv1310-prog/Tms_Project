<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterForm extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $phone_number = '';
    public $email = '';
    public $password = '';

    public $submitted = false;

    public function register()
    {
        $this->submitted = true;

        $validated = $this->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'email'        => 'required|email|unique:users,email|max:255',
            'password'     => 'required|min:8|max:255',
        ], [
            'first_name.required'   => 'Please enter your first name.',
            'last_name.required'    => 'Please enter your last name.',
            'phone_number.required' => 'Please enter your phone number.',
            'phone_number.digits'   => 'The phone number must be exactly 10 digits.',
            'email.required'        => 'Please enter the email address.',
            'email.email'           => 'Please enter a valid email address.',
            'email.unique'          => 'This email address is already registered.',
            'password.required'     => 'Please enter the password.',
            'password.min'          => 'The password must be at least 8 characters long.',
        ]);

        $user = User::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => 'user',
            'status' => 1,
            'password'     => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        session()->regenerate();

        session()->flash('successmessage', 'Registration completed successfully.');

        return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.auth.register')->layout('components.layouts.register', [
            'pageTitle' => 'Register | TMS',
        ]);
    }
}
