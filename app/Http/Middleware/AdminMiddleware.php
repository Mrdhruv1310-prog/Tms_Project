<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $role = $authUser?->role ?? 'user';

        if (! in_array($role, ['admin', 'super-admin'], true)) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
