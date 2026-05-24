<?php

namespace App\Console\Commands;

use App\Models\Plant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * UpdateCareConsistency Command
 * 
 * Recalculates care consistency for all plants based on their care task completion patterns.
 * 
 * MECHANICS:
 * - Calculates each task from its next due date
 * - Not due yet: 100
 * - Due today / inside grace: 85
 * - Overdue: decays quickly based on days late and task frequency
 * - Range: 0-100%
 * 
 * Run frequency: Daily (via Laravel Scheduler)
 * 
 * Usage:
 *   php artisan update:care-consistency
 */
class UpdateCareConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:care-consistency';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Recalculate care consistency for all plants based on task completion patterns.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting care consistency update...');

        $plants = Plant::with('careTasks')->get();
        $updatedCount = 0;

        foreach ($plants as $plant) {
            $tasks = $plant->careTasks;

            if ($tasks->isEmpty()) {
                // No tasks = no consistency data
                continue;
            }

            $taskScores = [];
            $isNeglected = false;

            foreach ($tasks as $task) {
                if (!$task->last_completed) {
                    $taskScores[] = 0;
                    $isNeglected = true;
                    continue;
                }

                $dueDate = $task->last_completed->copy()->addDays($task->frequency_days);
                $gracePeriodStart = $dueDate->copy()->subHours(12);

                if (now()->lessThan($gracePeriodStart)) {
                    $taskScores[] = 100;
                    continue;
                }

                if (now()->lessThanOrEqualTo($dueDate)) {
                    $taskScores[] = 85;
                    continue;
                }

                $daysOverdue = (int) floor($dueDate->diffInDays(now()));
                $missedIntervals = max(1, (int) floor($daysOverdue / max(1, $task->frequency_days)) + 1);
                $overduePenalty = ($daysOverdue * 8) + ($missedIntervals * 12);

                $taskScores[] = max(0, 85 - $overduePenalty);

                if ($daysOverdue > 3) {
                    $isNeglected = true;
                }
            }

            $newConsistency = (int) round(array_sum($taskScores) / count($taskScores));

            if ($plant->care_consistency !== $newConsistency || $plant->is_neglected !== $isNeglected) {
                $oldConsistency = $plant->care_consistency;
                $plant->update([
                    'care_consistency' => $newConsistency,
                    'is_neglected' => $isNeglected,
                ]);
                $updatedCount++;

                Log::info('Care consistency updated', [
                    'plant_id' => $plant->id,
                    'plant_name' => $plant->name,
                    'old_consistency' => $oldConsistency,
                    'new_consistency' => $newConsistency,
                    'is_neglected' => $isNeglected,
                    'tasks_analyzed' => count($taskScores),
                    'timestamp' => now()->toIso8601String(),
                ]);
            }
        }

        $this->info("✓ Care consistency update completed!");
        $this->line("  Plants Updated: {$updatedCount}");

        Log::info('Care consistency update completed', [
            'plants_updated' => $updatedCount,
            'total_plants' => $plants->count(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return self::SUCCESS;
    }
}
