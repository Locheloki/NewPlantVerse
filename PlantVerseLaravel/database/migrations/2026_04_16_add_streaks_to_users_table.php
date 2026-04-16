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
        Schema::table('users', function (Blueprint $table) {
            // Daily streak tracking
            $table->integer('daily_streak')->default(0)->after('is_on_vacation');
            $table->date('daily_streak_start_date')->nullable()->after('daily_streak');
            $table->date('last_care_date')->nullable()->after('daily_streak_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_streak', 'daily_streak_start_date', 'last_care_date']);
        });
    }
};
