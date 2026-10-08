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
        // Agar user authenticated (logged in) hai
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');
            $timeout = 10 * 60; // 10 minutes (600 seconds)

            // Check karein ki last activity se 10 minutes (600 seconds) zyada ho chuke hain ya nahi
            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                $authUser = Auth::user();
                $role = $authUser?->role ?? 'user';

                // User ko logout karein aur session clear karein
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Role ke hisab se login route par redirect karein
                $redirectRoute = in_array($role, ['admin', 'super-admin'], true) ? 'admin.login' : 'login';

                return redirect()->route($redirectRoute)->with('message', 'Session 10 minutes inactivity ki wajah se expire ho gaya hai.');
            }

            // Current time ko session me last activity update karein
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
