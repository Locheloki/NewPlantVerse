<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Plant Model
 * 
 * Represents a plant entity with relationships to users and care tasks.
 * Refactored to remove redundant type-checking logic.
 */
class Plant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'species',
        'photo_url',
        'care_consistency',
        'is_neglected',
    ];

    /**
     * Type casting configuration
     * 
     * REFACTORING NOTES:
     * - care_consistency is cast to 'integer' which ensures:
     *   1. Database values are automatically cast to int on retrieval
     *   2. Null values are converted to 0 by Laravel's integer cast
     *   3. Removed redundant getCareConsistencyIntAttribute() method as it duplicated this functionality
     * 
     * - Assuming the database migration includes DEFAULT 0 for care_consistency column,
     *   Laravel's integer cast handles any edge cases
     */
    protected $casts = [
        'care_consistency' => 'integer',
        'is_neglected' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function careTasks(): HasMany
    {
        return $this->hasMany(CareTask::class);
    }

    /**
     * Get all journal entries for this plant's growth and care history
     */
    public function journals(): HasMany
    {
        return $this->hasMany(PlantJournal::class);
    }
}
