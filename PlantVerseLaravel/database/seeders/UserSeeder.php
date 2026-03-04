<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@plantverse.com',
            'password' => Hash::make('password'),
            'pvt_balance' => 450,
            'on_time_care_percentage' => 85,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'pvt_balance' => 9999,
            'on_time_care_percentage' => 100,
        ]);
    }
}
