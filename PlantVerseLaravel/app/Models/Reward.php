<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Reward Model
 * 
 * Represents a reward that users can purchase with their PVT balance.
 * Tracks which users own each reward through a many-to-many relationship.
 */
class Reward extends Model
{
    protected $fillable = [
        'title',
        'description',
        'pvt_cost',
        'icon',
        'image_hint',
    ];

    protected $casts = [
        'pvt_cost' => 'integer',
    ];

    /**
     * Get all users who own this reward
     * 
     * REFACTORED: Added belongsToMany relationship to track reward ownership.
     * Allows querying which users have purchased a specific reward.
     * Uses the reward_user pivot table to manage the relationship.
     * 
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reward_user');
    }
}
