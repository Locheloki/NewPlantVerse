<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'progress',
        'target',
        'is_completed',
    ];

    protected $casts = [
        'progress' => 'integer',
        'target' => 'integer',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
