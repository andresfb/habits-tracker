<?php

date_default_timezone_set('America/New_York');

use App\Http\Middleware\HasInvitationMiddleware;
use App\Http\Middleware\UserIsFullyRegisteredMiddleware;
use App\Jobs\NotificationsSummaryJob;
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
        $middleware->throttleWithRedis();
        $middleware->alias([
            'registered' => UserIsFullyRegisteredMiddleware::class,
            'invitation' => HasInvitationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (): void {
        Schedule::job(app(NotificationsSummaryJob::class))->dailyAt('23:35');
    })->create();
