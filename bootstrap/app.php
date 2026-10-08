<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AutoLogoutMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'prevent-back-history' => \App\Http\Middleware\PreventBackButtonMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // 10-minute Auto Logout Middleware ko global web stack me add kiya gaya hai
        $middleware->web(append: [
            AutoLogoutMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // CSRF/Session expire hone par 419 page ke bajaye login par redirect karega
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->route('login')->with('errormessage', 'Your session expired. Please log in again.');
            }
        });
    })->create();
