<?php

namespace App\Livewire\Forms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class LoginForm extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;
    public $error = false; // Add this line to define the property

    public function login(Request $request)
    {
        $validated = $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        if (Auth::attempt($validated)) {
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
