<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareTask extends Model
{
    protected $fillable = [
        'plant_id',
        'type',
        'frequency_days',
        'last_completed',
    ];

    protected $casts = [
        'frequency_days' => 'integer',
        'last_completed' => 'datetime',
    ];

    /**
     * Check if this task is ready to be completed
     */
    public function isReadyForCompletion()
    {
        $daysSinceCompleted = $this->last_completed->diffInDays(now());
        return $daysSinceCompleted >= $this->frequency_days;
    }

    /**
     * Get days remaining until task can be completed
     */
    public function daysUntilReady()
    {
        $daysSinceCompleted = $this->last_completed->diffInDays(now());
        $remaining = $this->frequency_days - $daysSinceCompleted;
        return max(0, $remaining);
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }
}
