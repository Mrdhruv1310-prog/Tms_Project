<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Assuming you have a 'hasRole' method in your User model
        if (!$request->user() || !$request->user()->hasRole($role)) {
            // Redirect or return a 403 response if unauthorized
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
