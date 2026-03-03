<?php

namespace App\Models;

// 1. Remove this line if it exists:
// use Illuminate\Database\Eloquent\Model;

// 2. Add these lines:
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 3. Change "extends Model" to "extends Authenticatable"
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

    // Any existing relationships you have (like plants) go here:
    public function plants()
    {
        return $this->hasMany(Plant::class);
    }
}
