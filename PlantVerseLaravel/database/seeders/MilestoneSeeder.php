<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            // ===== ADMIN ACCOUNT MILESTONES (User 2) =====
            [
                'user_id' => 2,
                'title' => 'Tropical Gardener',
                'description' => 'Own all 12 tropical fruits and vegetables',
                'progress' => 12,
                'target' => 12,
                'is_completed' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'Daily Champion',
                'description' => 'Maintain 30-day care streak',
                'progress' => 28,
                'target' => 30,
                'is_completed' => false,
            ],
            [
                'user_id' => 2,
                'title' => 'Green Master',
                'description' => 'Achieve 95%+ care consistency',
                'progress' => 95,
                'target' => 95,
                'is_completed' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'PVT Millionaire',
                'description' => 'Earn 1000 PVT',
                'progress' => 950,
                'target' => 1000,
                'is_completed' => false,
            ],
            [
                'user_id' => 2,
                'title' => 'Harvest Time',
                'description' => 'Have 5+ vegetables in your collection',
                'progress' => 7,
                'target' => 5,
                'is_completed' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'Fruit Lover',
                'description' => 'Have 5+ fruits in your collection',
                'progress' => 5,
                'target' => 5,
                'is_completed' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'Perfect Month',
                'description' => 'Maintain 100% care consistency for 7 days',
                'progress' => 7,
                'target' => 7,
                'is_completed' => true,
            ],

            // ===== DEMO USER ACCOUNT MILESTONES (User 1) =====
            [
                'user_id' => 1,
                'title' => 'First Plant',
                'description' => 'Own your first plant',
                'progress' => 5,
                'target' => 1,
                'is_completed' => true,
            ],
            [
                'user_id' => 1,
                'title' => 'Plant Collector',
                'description' => 'Own 5 plants',
                'progress' => 5,
                'target' => 5,
                'is_completed' => true,
            ],
            [
                'user_id' => 1,
                'title' => 'Perfect Care',
                'description' => 'Maintain 100% care consistency for 7 days',
                'progress' => 3,
                'target' => 7,
                'is_completed' => false,
            ],
            [
                'user_id' => 1,
                'title' => 'Plant Parent',
                'description' => 'Care for 10 plants successfully',
                'progress' => 5,
                'target' => 10,
                'is_completed' => false,
            ],
            [
                'user_id' => 1,
                'title' => 'Green Thumb',
                'description' => 'Earn 500 PVT',
                'progress' => 450,
                'target' => 500,
                'is_completed' => false,
            ],
        ];

        foreach ($milestones as $milestone) {
            Milestone::create($milestone);
        }
    }
}
