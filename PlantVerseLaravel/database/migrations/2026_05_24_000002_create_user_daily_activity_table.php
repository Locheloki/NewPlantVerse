<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_daily_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('activity_date');
            $table->boolean('logged_in')->default(false); // User accessed dashboard/logged in
            $table->integer('care_logs_count')->default(0); // Number of care tasks logged
            $table->boolean('visited_plants')->default(false); // Checked on plants
            $table->timestamps();

            // Unique constraint: one record per user per day
            $table->unique(['user_id', 'activity_date']);

            // Indexes for queries
            $table->index('user_id');
            $table->index('activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_daily_activity');
    }
};
