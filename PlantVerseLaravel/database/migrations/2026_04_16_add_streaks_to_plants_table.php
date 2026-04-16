<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            // Per-plant care streak tracking
            $table->integer('care_streak')->default(0)->after('is_neglected');
            $table->timestamp('streak_started_at')->nullable()->after('care_streak');
            $table->timestamp('last_care_completed_at')->nullable()->after('streak_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn(['care_streak', 'streak_started_at', 'last_care_completed_at']);
        });
    }
};
