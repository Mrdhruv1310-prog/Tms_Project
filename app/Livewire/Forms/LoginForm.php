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

    // public function login(Request $request)
    // {
    //     $validated = $this->validate([
    //         'email' => 'required|email|max:255',
    //         'password' => 'required|min:6|max:255',
    //     ]);

    //     // Add status check here
    //     $credentials = [
    //         'email' => $validated['email'],
    //         'password' => $validated['password'],
    //         'status' => 1, // only active users can login
    //     ];

    //     if (Auth::attempt($credentials)) {
    //         // ✅ Check admin role after login
    //         if (auth()->user()->role !== 'admin') {
    //             Auth::logout();
    //             $this->addError('credentials', 'Access denied. Admin only.');
    //             $this->error = true;
    //             return;
    //         }

    //         $request->session()->regenerate();

    //         return $this->redirect('dashboard');
    //     }

    //     $user = \App\Models\User::where('email', $validated['email'])->first();

    //     if ($user && $user->status == 0) {
    //         $this->addError('credentials', 'You Can Not Login. Please contact admin.');
    //         $this->error = true;
    //         return;
    //     }

    //     $this->addError('credentials', 'Invalid credentials!');
    //     $this->error = true;
    // }


    public function login(Request $request)
    {
        $validated = $this->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        // User not found
        if (!$user) {
            $this->addError('credentials', 'No account found. Please sign up!');
            $this->error = true;
            return;
        }

        // Block admin users
        if ($user->role === 'admin') {
            $this->addError('credentials', 'Invalid access for this login!');
            $this->error = true;
            return;
        }

        // Block inactive users
        if ($user->status == 0) {
            $this->addError('credentials', 'Access denied!.');
            $this->error = true;
            return;
        }

        // ✅ Now attempt login only for allowed users
        if (Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
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
