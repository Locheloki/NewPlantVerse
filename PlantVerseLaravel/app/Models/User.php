<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'pvt_balance',
        'on_time_care_percentage',
    ];

    protected $casts = [
        'pvt_balance' => 'integer',
        'on_time_care_percentage' => 'integer',
    ];

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }
}
