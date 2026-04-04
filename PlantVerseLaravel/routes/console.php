<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * ARTISAN COMMANDS DOCUMENTATION
 * 
 * Commands registered and available:
 * 
 * 1. check:plant-neglect
 *    - Runs daily at 2:00 AM via Scheduler
 *    - Checks all care tasks for neglected plants
 *    - Marks plants neglected if any task is more than 3 days overdue
 *    - Deducts 5 PVT from owner (minimum: 0)
 *    - Skips users currently on vacation
 *    - Auto-toggles vacation mode off if it has expired
 *    - Manual execution: php artisan check:plant-neglect
 * 
 * 2. send:care-reminders
 *    - Runs daily at 9:00 AM via Scheduler
 *    - Sends email reminders to users about plants needing care
 *    - Only sends to users not currently on vacation
 *    - Groups tasks by plant for readability
 *    - Skips users with no plants or no pending tasks
 *    - Manual execution: php artisan send:care-reminders
 * 
 * Scheduler Configuration:
 * - Registered in app/Console/Kernel.php
 * - Requires crontab entry: * * * * * cd /app && php artisan schedule:run
 * - For development, uncomment the everyMinute() entries in Kernel.php to test
 */



