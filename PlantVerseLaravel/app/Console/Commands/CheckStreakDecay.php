<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CheckStreakDecay Command
 * 
 * This command implements the GRADUAL DECAY STREAK SYSTEM.
 * 
 * MECHANICS:
 * - Each day player logs care: streak +1
 * - If player misses a day (24h+): streak -2
 * - SOFT FLOOR PROTECTION: If streak >= 7, first missed day only -1 instead of -2
 * - Streak cannot go below 0
 * 
 * TIME RULE (No Grace Period):
 * - 24-hour cycle from last_care_date
 * - After 24h with no care logged: decay applies on next check
 * 
 * Designed to run daily via Laravel Scheduler (routes/console.php).
 * 
 * Usage:
 *   php artisan check:streak-decay
 */
class CheckStreakDecay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:streak-decay';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check for missed care days and apply gradual streak decay (with soft floor protection).';

    /**
     * Decay amount for missed day
     */
    private const STREAK_DECAY_NORMAL = 2;

    /**
     * Decay amount for streak >= 7 (soft floor protection)
     */
    private const STREAK_DECAY_PROTECTED = 1;

    /**
     * Threshold for soft floor protection
     */
    private const SOFT_FLOOR_THRESHOLD = 7;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting streak decay check...');

        $allUsers = User::all();

        $decayedCount = 0;
        $protectedCount = 0;

        foreach ($allUsers as $user) {
            // Skip users with no last_care_date or today's date
            if (!$user->last_care_date) {
                continue;
            }

            $lastCareDate = $user->last_care_date->toDateString();
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();

            // Only apply decay if last care was before yesterday (24h+ has passed)
            if ($lastCareDate !== $today && $lastCareDate !== $yesterday) {
                // Player missed at least one day - apply decay
                $decayAmount = self::STREAK_DECAY_NORMAL;

                // SOFT FLOOR PROTECTION: If streak >= 7, use gentler decay
                if ($user->daily_streak >= self::SOFT_FLOOR_THRESHOLD) {
                    $decayAmount = self::STREAK_DECAY_PROTECTED;
                    $protectedCount++;
                }

                $oldStreak = $user->daily_streak;
                $user->daily_streak = max(0, $user->daily_streak - $decayAmount);
                $user->save();

                $decayedCount++;

                // Log the decay event
                Log::info('Streak decay applied', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'old_streak' => $oldStreak,
                    'new_streak' => $user->daily_streak,
                    'decay_amount' => $decayAmount,
                    'was_protected' => $user->daily_streak >= self::SOFT_FLOOR_THRESHOLD,
                    'last_care_date' => $lastCareDate,
                    'days_missed' => now()->diffInDays($user->last_care_date),
                    'timestamp' => now()->toIso8601String(),
                ]);
            }
        }

        // Console output summary
        $this->info("✓ Streak decay check completed!");
        $this->line("  Users with Decay Applied: {$decayedCount}");
        $this->line("  Protected by Soft Floor: {$protectedCount}");

        Log::info('Streak decay check completed', [
            'users_decayed' => $decayedCount,
            'users_protected' => $protectedCount,
            'timestamp' => now()->toIso8601String(),
        ]);

        return self::SUCCESS;
    }
}
