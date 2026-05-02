<?php

namespace App\Livewire\Forms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

class AdminLoginForm extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    public $error = false;

    public function Adminlogin(Request $request)
    {
        $validated = $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => 1,
        ];

        if (Auth::attempt($credentials)) {

            // ✅ Check admin role after login
            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                $this->addError('credentials', 'Access denied. Admin only.');
                $this->error = true;
                return;
            }

            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        $this->addError('credentials', 'Invalid credentials!');
        $this->error = true;
    }

    public function render()
    {
        return view('livewire.pages.admin.auth.login-form')
            ->layout('components.layouts.admin.login');
    }
}
