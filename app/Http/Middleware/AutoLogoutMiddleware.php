<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User|null $authUser */
            $authUser = Auth::user();

            // Agar user status = 0 (Inactive) ho gaya hai, toh turant logout kar do
            if ($authUser && (int) $authUser->status === 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'credentials' => 'Invalid credentials!',
                ]);
            }

            $lastActivity = session('last_activity_time');
            // 2 Days in seconds (2 * 24 * 60 * 60)
            $timeout = 172800;

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                $role = $authUser?->role ?? 'user';

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $redirectRoute = in_array($role, ['admin', 'super-admin'], true) ? 'admin.login' : 'login';

                return redirect()->route($redirectRoute)->with('message', 'Please log in again.');
            }

            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
