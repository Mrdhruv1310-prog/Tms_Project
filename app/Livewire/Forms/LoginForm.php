<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;

class LoginForm extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;
    public $error = false; // Add this line to define the property


    public function login(Request $request)
    {
        $credentials = $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status == 0) {
            $this->addError('credentials', 'Permission denied.');
            $this->error = true;
            return;
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirect('dashboard');
        }

        $this->addError('credentials', 'Invalid credentials!');
        $this->error = true;
    }
    public function render()
    {
        return view('livewire.pages.auth.login-form')->layout('components.layouts.login', [
            'pageTitle' => 'Sign In | TMS',
        ]);
    }
}
