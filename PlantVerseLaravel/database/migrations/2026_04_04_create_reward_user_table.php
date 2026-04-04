<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateRewardUserTable
 * 
 * Creates a pivot table to track reward ownership.
 * Allows users to own multiple rewards and tracks when they purchased each one.
 * 
 * This replaces the need for a separate "UserReward" or "PurchasedReward" model
 * and leverages Laravel's built-in belongsToMany relationship pattern.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('reward_user', function (Blueprint $table) {
            // Primary keys for the pivot relationship
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            
            // Timestamps to track when the reward was purchased
            $table->timestamps();
            
            // Ensure a user can't own the same reward twice
            $table->unique(['user_id', 'reward_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_user');
    }
};
