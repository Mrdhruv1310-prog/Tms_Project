<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Navbar | TMS')]
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

        // Livewire navigate mode ko completely bypass karke browser level full redirect karein
        return redirect('/login');
    }

    public function render(): View
    {
        return view('livewire.navbar', [
            'randomColor' => $this->generateRandomColor(),
        ]);
    }
}
