<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserDailyActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * UpdateAttendanceStreak Command
 * 
 * Implements the ATTENDANCE-BASED STREAK SYSTEM.
 * 
 * MECHANICS:
 * - User must log in AND do minimum activity (check plants OR log care task) each day
 * - If user maintains minimum activity: streak +1
 * - If user misses a day (no login or no minimum activity): streak resets to 0
 * 
 * TIME RULE:
 * - Checks yesterday's activity
 * - If user had minimum activity yesterday: continue/increment streak
 * - If no activity: reset streak
 * 
 * REQUIREMENTS FOR STREAK:
 * - Must log in to dashboard/app
 * - Must visit plants page OR log at least one care task
 * 
 * Designed to run daily via Laravel Scheduler.
 * 
 * Usage:
 *   php artisan update:attendance-streak
 */
class UpdateAttendanceStreak extends Command
{
    protected $signature = 'update:attendance-streak';

    protected $description = 'Update daily streaks based on user attendance and minimum daily activity.';

    public function handle()
    {
        $this->info('Starting attendance streak update...');

        $yesterday = now()->subDay()->toDateString();
        $today = now()->toDateString();

        $allUsers = User::all();

        $streakIncrementedCount = 0;
        $streakResetCount = 0;
        $onVacationCount = 0;

        foreach ($allUsers as $user) {
            // Skip users on vacation
            if ($user->is_on_vacation && $user->vacation_ends_at && $user->vacation_ends_at->isFuture()) {
                $onVacationCount++;
                continue;
            }

            // Get yesterday's activity
            $yesterdayActivity = UserDailyActivity::where('user_id', $user->id)
                ->where('activity_date', $yesterday)
                ->first();

            $oldStreak = $user->daily_streak;

            if ($yesterdayActivity && $yesterdayActivity->hasMinimumActivity()) {
                // User had minimum activity yesterday - continue/increment streak
                if ($user->daily_streak === 0) {
                    // Starting a new streak
                    $user->daily_streak = 1;
                    $user->daily_streak_start_date = $yesterday;
                } else {
                    // Continue existing streak
                    $user->daily_streak += 1;
                }

                $streakIncrementedCount++;

                Log::info('Attendance streak incremented', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'old_streak' => $oldStreak,
                    'new_streak' => $user->daily_streak,
                    'streak_start_date' => $user->daily_streak_start_date,
                    'activity_date' => $yesterday,
                    'logged_in' => $yesterdayActivity->logged_in,
                    'care_logs' => $yesterdayActivity->care_logs_count,
                    'visited_plants' => $yesterdayActivity->visited_plants,
                ]);
            } else {
                // User missed yesterday or no minimum activity - reset streak
                if ($user->daily_streak > 0) {
                    $user->daily_streak = 0;
                    $user->daily_streak_start_date = null;

                    $streakResetCount++;

                    Log::info('Attendance streak reset', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'previous_streak' => $oldStreak,
                        'had_activity' => $yesterdayActivity !== null,
                        'had_minimum_activity' => $yesterdayActivity?->hasMinimumActivity() ?? false,
                    ]);
                }
            }

            $user->save();
        }

        $this->info("✓ Attendance streak update completed!");
        $this->line("  Streaks Incremented: {$streakIncrementedCount}");
        $this->line("  Streaks Reset: {$streakResetCount}");
        $this->line("  Users on Vacation: {$onVacationCount}");

        Log::info('Attendance streak update completed', [
            'streaks_incremented' => $streakIncrementedCount,
            'streaks_reset' => $streakResetCount,
            'users_on_vacation' => $onVacationCount,
            'timestamp' => now()->toIso8601String(),
        ]);

        return self::SUCCESS;
    }
}
