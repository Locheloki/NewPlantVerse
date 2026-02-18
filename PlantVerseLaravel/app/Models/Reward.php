<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
