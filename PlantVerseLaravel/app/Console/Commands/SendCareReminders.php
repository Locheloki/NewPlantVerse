<?php

namespace App\Console\Commands;

use App\Models\CareTask;
use App\Models\User;
use App\Notifications\DailyCareDigest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SendCareReminders Command
 * 
 * Sends daily email reminders to users about plants that need care today or are overdue.
 * This keeps users engaged with their plant care schedules and reduces plant neglect.
 * 
 * Designed to run daily via Laravel Scheduler (app/Console/Kernel.php).
 * 
 * Logic:
 * 1. Fetch all users who are not on vacation
 * 2. For each user, find care tasks due today or overdue
 * 3. Group tasks by plant for a digestible summary
 * 4. Send DailyCareDigest notification if user has pending care tasks
 * 
 * Usage:
 *   php artisan send:care-reminders
 */
class SendCareReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:care-reminders';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send daily care reminders to users about plants needing attention.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting care reminder notifications...');

        /**
         * Get all users who are NOT currently on vacation
         * 
         * Users on vacation (is_on_vacation=true AND vacation_ends_at in future)
         * are exempt from reminders since they won't be caring for plants anyway.
         * Improves user experience by reducing unnecessary notifications.
         */
        $users = User::where(function ($query) {
            // Either not on vacation OR vacation has expired
            $query->where('is_on_vacation', false)
                  ->orWhere(function ($q) {
                      $q->where('is_on_vacation', true)
                        ->where('vacation_ends_at', '<', now());
                  });
        })->get();

        $remindersCount = 0;
        $usersSkipped = 0;

        foreach ($users as $user) {
            /**
             * Find all care tasks for this user's plants that are:
             * - Due today (last_completed + frequency_days <= today)
             * - Overdue (last_completed + frequency_days < today)
             */
            $userPlants = $user->plants()->pluck('id')->toArray();

            if (empty($userPlants)) {
                continue; // Skip users with no plants
            }

            $dueCareTasks = CareTask::whereIn('plant_id', $userPlants)
                ->with(['plant'])
                ->get()
                ->filter(function ($task) {
                    /**
                     * Check if task is due today or overdue
                     * Due: last_completed + frequency_days >= today
                     * Overdue: last_completed + frequency_days < today
                     */
                    $dueDate = $task->last_completed->addDays($task->frequency_days);
                    return $dueDate->lessThanOrEqualTo(now());
                });

            // Skip users with no pending care tasks
            if ($dueCareTasks->isEmpty()) {
                $usersSkipped++;
                continue;
            }

            /**
             * GROUP TASKS BY PLANT
             * 
             * Transform flat list of tasks into a grouped structure:
             * [
             *   ['plant_name' => 'Rose', 'tasks' => ['Water', 'Fertilize']],
             *   ['plant_name' => 'Fern', 'tasks' => ['Water']],
             * ]
             */
            $plantCares = [];
            $plantIds = [];

            foreach ($dueCareTasks as $task) {
                $plant = $task->plant;
                $plantKey = $plant->id;

                if (!in_array($plantKey, $plantIds)) {
                    $plantIds[] = $plantKey;
                    $plantCares[] = [
                        'plant_name' => $plant->name,
                        'tasks' => [],
                    ];
                }

                // Find the plant entry and add task type
                foreach ($plantCares as &$care) {
                    if ($care['plant_name'] === $plant->name) {
                        $care['tasks'][] = $task->type;
                        break;
                    }
                }
            }

            // Send notification
            try {
                $user->notify(new DailyCareDigest($dueCareTasks->count(), $plantCares));
                $remindersCount++;

                Log::info('Care reminder notification sent', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'plants_needing_care' => count($plantCares),
                    'total_tasks' => $dueCareTasks->count(),
                    'timestamp' => now()->toIso8601String(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send care reminder', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error_message' => $e->getMessage(),
                    'timestamp' => now()->toIso8601String(),
                ]);
            }
        }

        // Console output summary
        $this->info("✓ Care reminder sending completed!");
        $this->line("  Reminders Sent: {$remindersCount}");
        $this->line("  Users Skipped: {$usersSkipped}");

        Log::info('Care reminders command completed', [
            'reminders_sent' => $remindersCount,
            'users_skipped' => $usersSkipped,
            'timestamp' => now()->toIso8601String(),
        ]);

        return self::SUCCESS;
    }
}
