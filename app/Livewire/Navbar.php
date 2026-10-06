<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class Navbar extends Component
{
    /**
     * Generate or retrieve a persistent random avatar color for the user session.
     */
    public function generateRandomColor(): string
    {
        if (! session()->has('user_color')) {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            session()->put('user_color', $color);
        }

        /** @var string */
        return session('user_color');
    }

    /**
     * Handle user logout securely, clear session data, and redirect to login.
     */
    public function logout(Request $request): mixed
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->redirect('/login', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.navbar', [
            'randomColor' => $this->generateRandomColor(),
        ]);
    }
}
