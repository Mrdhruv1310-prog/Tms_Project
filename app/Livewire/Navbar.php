<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Navbar extends Component
{
    public function generateRandomColor()
    {
        // Generate a color if not already set in the session
        if (!session()->has('user_color')) {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            session()->put('user_color', $color);
        }

        return session('user_color');
    }
    // public function logout(Request $request){
    //     $user  = Auth::user();
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return $this->redirect('/login');
    // }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on role
        if ($role === 'admin') {
            return $this->redirect('/admin-login');
        }

        return $this->redirect('/login');
    }

    public function render()
    {
        return view('livewire.navbar', [
            'randomColor' => $this->generateRandomColor(),
        ]);
    }
}
