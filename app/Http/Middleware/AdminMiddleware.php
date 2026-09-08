<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Yahan humne check update kar diya hai taaki 'admin' aur 'super-admin' dono allow ho jayein
        $role = auth()->user()->role;
        if (!in_array($role, ['admin', 'super-admin'])) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
