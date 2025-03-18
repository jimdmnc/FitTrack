<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\ScheduleServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        App\Console\Commands\UpdateMemberStatus::class, // Register your command here
    ])
    ->withProviders([
        ScheduleServiceProvider::class, // Register the provider here
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
