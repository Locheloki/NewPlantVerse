<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Represents a user in the PlantVerse application with relationships to plants and other entities.
 * Refactored to use database-backed admin checks instead of hardcoded email validation.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pvt_balance',
        'is_admin',
        'on_time_care_percentage',
        'is_on_vacation',
        'vacation_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_on_vacation' => 'boolean',
            'vacation_ends_at' => 'datetime',
        ];
    }

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    /**
     * Get all rewards owned by this user
     * 
     * REFACTORED: Added belongsToMany relationship to track purchased rewards.
     * This allows users to own multiple rewards and vice versa.
     * Uses the reward_user pivot table to track the relationship.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'reward_user');
    }

    /**
     * Check if the user is an admin
     * 
     * REFACTORED: Removed hardcoded email check (admin@admin.com).
     * Now uses the is_admin database column for more flexible admin management.
     * This allows admins to be added/removed without code changes, and supports
     * multiple admin accounts.
     * 
     * @return bool True if user is an admin, false otherwise
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
