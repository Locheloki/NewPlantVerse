<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * Register custom middleware aliases
         * 
         * The 'admin' middleware ensures that only authenticated admin users
         * can access protected routes (e.g., shop management).
         */
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('check:plant-neglect')->dailyAt('02:00');
        $schedule->command('send:care-reminders')->dailyAt('09:00');
        $schedule->command('update:care-consistency')->dailyAt('03:00');
        $schedule->command('check:streak-decay')->dailyAt('00:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
