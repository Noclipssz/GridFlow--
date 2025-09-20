<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware aliases
        $middleware->alias([
            'prof.auth' => \App\Http\Middleware\EnsureProfessorAuthenticated::class,
        ]);
    })
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\Filament\AdminPanelProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
