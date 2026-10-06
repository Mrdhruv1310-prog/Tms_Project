<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.login')]
#[Title('Sign In | TMS')]
class LoginForm extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';
    public bool $error = false;
    public bool $submitted = true;

    public function mount(): void
    {
        // Agar user pehle se logged in hai, toh use seedhe dashboard par bhej do
        if (Auth::check()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function login(Request $request): mixed
    {
        $this->submitted = true;
        $this->error = false;

        $credentials = $this->validate([
            'email' => 'required|max:255',
            'password' => 'required|min:6|max:255',
        ], [
            'email.required' => 'Please enter the email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter the password.',
            'password.min' => 'The password must be at least 6 characters long.',
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user && $user->status == 0) {
            $this->addError('credentials', 'Permission denied.');
            $this->error = true;
            return null;
        }

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return $this->redirect(route('dashboard'), navigate: true);
        }

        $this->addError('credentials', 'Invalid credentials!');
        $this->error = true;
        return null;
    }

    public function render(): mixed
    {
        return view('livewire.pages.auth.login-form');
    }
}
