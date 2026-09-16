<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureEmailIsVerifiedIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Logged-in users who open the login or register page go back to the lessons.
        $middleware->redirectUsersTo('/');

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'verifiedifauth' => EnsureEmailIsVerifiedIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
