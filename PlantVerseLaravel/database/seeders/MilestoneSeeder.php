<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
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
