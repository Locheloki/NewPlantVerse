<?php

namespace App\Console\Commands;

use App\Models\CareTask;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CheckPlantNeglect Command
 * 
 * This command checks all care tasks and identifies plants that are neglected.
 * A plant is considered neglected if any of its care tasks are more than 3 days overdue.
 * When a plant becomes neglected, the owner is penalized 5 PVT (minimum balance: 0).
 * 
 * Designed to run daily via Laravel Scheduler (routes/console.php).
 * 
 * Usage:
 *   php artisan check:plant-neglect
 */
class CheckPlantNeglect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:plant-neglect';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check for neglected plants and update their status. Penalize users with 5 PVT deduction.';

    /**
     * Days overdue threshold to mark plant as neglected
     */
    private const NEGLECT_THRESHOLD_DAYS = 3;

    /**
     * PVT penalty amount for neglected plants
     */
    private const NEGLECT_PENALTY_PVT = 5;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting plant neglect check...');

        /**
         * VACATION MODE HANDLING
         * 
         * First, check all users on vacation to see if their vacation has ended.
         * If vacation_ends_at is in the past, automatically toggle is_on_vacation back to false.
         * This allows the system to stop exempting their plants from neglect checks.
         */
        $vacationUsersExpired = User::where('is_on_vacation', true)
            ->where('vacation_ends_at', '<', now())
            ->get();

        $vacationResetCount = 0;
        foreach ($vacationUsersExpired as $user) {
            $user->update([
                'is_on_vacation' => false,
                'vacation_ends_at' => null,
            ]);
            $vacationResetCount++;

            Log::info('User vacation automatically ended', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'vacation_ended_at' => now()->toIso8601String(),
            ]);
        }

        if ($vacationResetCount > 0) {
            $this->line("ℹ️  Vacation Mode Reset: {$vacationResetCount} user(s)");
        }

        // Get all care tasks with their associated plants and users
        $careTasks = CareTask::with(['plant', 'plant.user'])->get();

        $neglectedPlantsCount = 0;
        $penalizedUsersCount = 0;
        $penalizedAmount = 0;
        $skippedVacationCount = 0;

        foreach ($careTasks as $task) {
            // Skip if plant or user not found
            if (!$task->plant || !$task->plant->user) {
                continue;
            }

            $user = $task->plant->user;
            $plant = $task->plant;

            /**
             * VACATION MODE CHECK
             * 
             * If user is currently on vacation (is_on_vacation = true AND vacation_ends_at > now),
             * skip neglect checks for all their plants. This prevents penalties during travel.
             */
            if ($user->is_on_vacation && $user->vacation_ends_at && $user->vacation_ends_at->isFuture()) {
                $skippedVacationCount++;
                continue;
            }

            // Calculate days overdue
            $daysOverdue = now()->diffInDays($task->last_completed->addDays($task->frequency_days));

            // Check if task is more than 3 days overdue
            if ($daysOverdue > self::NEGLECT_THRESHOLD_DAYS) {
                // Mark plant as neglected only if not already marked
                if (!$plant->is_neglected) {
                    $plant->update(['is_neglected' => true]);
                    $neglectedPlantsCount++;

                    // Deduct PVT penalty from user (ensure balance doesn't go below 0)
                    $newBalance = max(0, $user->pvt_balance - self::NEGLECT_PENALTY_PVT);
                    $user->update(['pvt_balance' => $newBalance]);

                    $penalizedUsersCount++;
                    $penalizedAmount += self::NEGLECT_PENALTY_PVT;

                    // Log the neglect incident
                    Log::info('Plant marked as neglected', [
                        'plant_id' => $plant->id,
                        'plant_name' => $plant->name,
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'days_overdue' => $daysOverdue,
                        'task_type' => $task->type,
                        'pvt_deducted' => self::NEGLECT_PENALTY_PVT,
                        'new_pvt_balance' => $newBalance,
                        'timestamp' => now()->toIso8601String(),
                    ]);
                }
            }
        }

        // Console output summary
        $this->info("✓ Plant neglect check completed!");
        $this->line("  Neglected Plants Found: {$neglectedPlantsCount}");
        $this->line("  Users Penalized: {$penalizedUsersCount}");
        $this->line("  Total PVT Deducted: {$penalizedAmount}");
        $this->line("  Plants Skipped (Vacation): {$skippedVacationCount}");

        Log::info('Plant neglect check completed', [
            'neglected_plants_count' => $neglectedPlantsCount,
            'penalized_users_count' => $penalizedUsersCount,
            'total_pvt_deducted' => $penalizedAmount,
            'vacation_resets' => $vacationResetCount,
            'vacation_skipped' => $skippedVacationCount,
            'timestamp' => now()->toIso8601String(),
        ]);

        return self::SUCCESS;
    }
}
