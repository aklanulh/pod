<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'technician' => \App\Http\Middleware\TechnicianMiddleware::class,
        ]);

        // Register global middleware for admin activity logging
        $middleware->append(\App\Http\Middleware\LogAdminActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
