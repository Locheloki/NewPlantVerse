<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UserDailyActivity Model
 * 
 * Tracks daily attendance and activity for each user.
 * Used to determine if user maintained their daily streak based on login/activity.
 */
class UserDailyActivity extends Model
{
    protected $table = 'user_daily_activity';

    protected $fillable = [
        'user_id',
        'activity_date',
        'logged_in',
        'care_logs_count',
        'visited_plants',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'logged_in' => 'boolean',
        'visited_plants' => 'boolean',
    ];

    /**
     * Get the user this activity belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user had minimum daily activity
     * Minimum = logged in AND (visited plants OR logged care tasks)
     */
    public function hasMinimumActivity(): bool
    {
        return $this->logged_in && ($this->visited_plants || $this->care_logs_count > 0);
    }

    /**
     * Get or create today's activity record for a user
     */
    public static function getOrCreateToday($userId)
    {
        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'activity_date' => now()->toDateString(),
            ],
            [
                'logged_in' => false,
                'care_logs_count' => 0,
                'visited_plants' => false,
            ]
        );
    }

    /**
     * Mark login for today
     */
    public static function recordLogin($userId)
    {
        $activity = self::getOrCreateToday($userId);
        $activity->update(['logged_in' => true]);
        return $activity;
    }

    /**
     * Mark care log activity
     */
    public static function recordCareLog($userId)
    {
        $activity = self::getOrCreateToday($userId);
        $activity->increment('care_logs_count');
        return $activity;
    }

    /**
     * Mark plant visit
     */
    public static function recordPlantVisit($userId)
    {
        $activity = self::getOrCreateToday($userId);
        $activity->update(['visited_plants' => true]);
        return $activity;
    }
}
