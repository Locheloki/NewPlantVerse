<?php

namespace App\Console;

use App\Console\Commands\CheckPlantNeglect;
use App\Console\Commands\SendCareReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Console Kernel
 * 
 * Registers and schedules Laravel artisan commands.
 * All scheduled commands run via the Laravel Scheduler.
 * 
 * Production Setup:
 * To enable scheduled tasks, add this line to your system crontab:
 * * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
 * 
 * This ensures the scheduler runs every minute and triggers registered commands at their scheduled times.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * CheckPlantNeglect Command
         * 
         * Runs daily at 2:00 AM to:
         * - Check all care tasks for neglected plants
         * - Mark plants neglected if any task is more than 3 days overdue
         * - Deduct 5 PVT from owners (minimum balance: 0)
         * - Log all incidents for monitoring
         * 
         * Frequency: Daily (runs at 2:00 AM server time)
         * Impact: Affects plant status, user PVT balance, system logs
         * 
         * Command: php artisan check:plant-neglect
         * Manual execution supported for testing/emergency use
         */
        $schedule->command(CheckPlantNeglect::class)
                 ->daily()
                 ->at('02:00')
                 ->name('check-plant-neglect')
                 ->description('Check and penalize neglected plants');

        /**
         * SendCareReminders Command
         * 
         * Runs daily at 9:00 AM to:
         * - Identify users with plants that need care today or are overdue
         * - Group tasks by plant for a readable digest
         * - Skip users currently on vacation
         * - Send DailyCareDigest email notification
         * 
         * Frequency: Daily (runs at 9:00 AM server time - good morning reminder)
         * Impact: Sends email notifications to engaged users
         * 
         * Command: php artisan send:care-reminders
         * Manual execution supported for testing
         */
        $schedule->command(SendCareReminders::class)
                 ->daily()
                 ->at('09:00')
                 ->name('send-care-reminders')
                 ->description('Send daily care reminders to users');

        /**
         * Optional: Debug/Development Only
         * Uncomment below to test scheduling every minute (development only!)
         * $schedule->command(CheckPlantNeglect::class)->everyMinute();
         * $schedule->command(SendCareReminders::class)->everyMinute();
         */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
