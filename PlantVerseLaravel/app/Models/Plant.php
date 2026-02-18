<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plant extends Model
{
    /**
     * Always return care_consistency as an integer for blade/css safety
     */
    public function getCareConsistencyIntAttribute()
    {
        if (is_null($this->care_consistency) || !is_numeric($this->care_consistency)) {
            return 0;
        }
        return (int) round($this->care_consistency);
    }
    protected $fillable = [
        'user_id',
        'name',
        'species',
        'photo_url',
        'care_consistency',
        'is_neglected',
    ];

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
}
