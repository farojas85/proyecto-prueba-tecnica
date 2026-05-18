<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Here we add our global CORS middleware (If we still use fruitcake in L11, or Illuminate\Http\Middleware\HandleCors)
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        
        // Registrar alias del middleware legacy
        $middleware->alias([
            'auth' => \App\Http\Middleware\LegacyTokenAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
