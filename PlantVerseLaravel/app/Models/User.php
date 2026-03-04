<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pvt_balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Existing relationships
    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    /**
     * Subtle Admin Check
     */
    public function isAdmin(): bool
    {
        return $this->email === 'admin@admin.com';
    }
}
