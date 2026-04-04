<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add Vacation Mode to Users Table
 * 
 * Allows users to pause plant care requirements during times when they're away.
 * When is_on_vacation is true, plants are exempt from neglect checks.
 * The vacation_ends_at timestamp enables automatic reset when vacation expires.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vacation mode flag - defaults to false (not on vacation)
            $table->boolean('is_on_vacation')->default(false)->after('pvt_balance');
            
            // When vacation ends - nullable, only populated when is_on_vacation = true
            $table->timestamp('vacation_ends_at')->nullable()->after('is_on_vacation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_on_vacation', 'vacation_ends_at']);
        });
    }
};
