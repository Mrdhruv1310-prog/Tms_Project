<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Mail\RegisterUserMail;
use Illuminate\Support\Facades\Mail;

#[Layout('components.layouts.register')]
#[Title('Register | TMS')]
class RegisterForm extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $phone_number = '';
    public string $email = '';
    public string $password = '';

    public bool $submitted = false;

    public function register(): void
    {
        $this->submitted = true;

        $validated = $this->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'email'        => 'required|email:rfc,dns|unique:users,email|max:255',
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

        $plainPassword = $validated['password'];

        $user = User::query()->create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => 'user',
            'status' => 1,
            'password'     => Hash::make($validated['password']),
        ]);

        Mail::to($user->email)->send(new RegisterUserMail($user, $plainPassword));

        Auth::login($user);

        session()->regenerate();

        session()->flash('successmessage', 'Registration completed successfully.');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.pages.auth.register');
    }
}
