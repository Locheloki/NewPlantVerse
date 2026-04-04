<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PlantJournal Model
 * 
 * Represents a single journal entry documenting a plant's growth and care progress.
 * Each journal entry can contain an optional photo and text note.
 * Deleted when the associated plant is deleted (cascading delete).
 */
class PlantJournal extends Model
{
    protected $table = 'plant_journals';

    protected $fillable = [
        'plant_id',
        'photo_url',
        'note',
    ];

    /**
     * Get the plant this journal entry belongs to
     */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }
}
