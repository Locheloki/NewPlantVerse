<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@plantverse.com',
            'pvt_balance' => 450,
            'on_time_care_percentage' => 85,
        ]);
    }
}
