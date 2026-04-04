<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 * 
 * Seeds initial demo and admin users for the PlantVerse application.
 * REFACTORED: Admin user now uses the is_admin column instead of relying on email check.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user (regular user)
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@plantverse.com',
            'password' => Hash::make('password'),
            'pvt_balance' => 450,
            'on_time_care_percentage' => 85,
            'is_admin' => false,
        ]);

        // Admin user - granted admin privileges via is_admin column
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'pvt_balance' => 9999,
            'on_time_care_percentage' => 100,
            'is_admin' => true,
        ]);
    }
}
