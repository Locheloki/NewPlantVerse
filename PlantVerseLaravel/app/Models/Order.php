<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Model
 * 
 * Represents a reward order/checkout with delivery information.
 * Tracks the delivery status of redeemed rewards from user redemption to delivery.
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'full_name',
        'email',
        'phone',
        'street_address',
        'city',
        'state_province',
        'postal_code',
        'country',
        'status',
        'tracking_number',
        'notes',
    ];

    /**
     * Get the user who placed this order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reward being ordered
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get formatted address for display
     */
    public function getFormattedAddress(): string
    {
        return "{$this->street_address}, {$this->city}, {$this->state_province} {$this->postal_code}, {$this->country}";
    }

    /**
     * Check if order has been shipped
     */
    public function isShipped(): bool
    {
        return in_array($this->status, ['shipped', 'delivered']);
    }

    /**
     * Check if order has been delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }
}
