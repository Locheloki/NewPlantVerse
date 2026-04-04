<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->integer('pvt_balance')->default(0);
            $table->integer('on_time_care_percentage')->default(0);
            /**
             * is_admin column
             * 
             * REFACTORED: Added boolean column to replace hardcoded email-based admin checks.
             * Allows flexible admin user management without code changes.
             * Default false ensures users are not admins unless explicitly set.
             */
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
