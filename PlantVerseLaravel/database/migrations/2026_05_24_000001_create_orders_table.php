<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');

            // Delivery information
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('street_address');
            $table->string('city');
            $table->string('state_province');
            $table->string('postal_code');
            $table->string('country');

            // Order status
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('tracking_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
