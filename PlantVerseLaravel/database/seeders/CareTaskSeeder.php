<?php

namespace Database\Seeders;

use App\Models\CareTask;
use Illuminate\Database\Seeder;

class CareTaskSeeder extends Seeder
{
    public function run(): void
    {
        $careTasks = [
            // Monstera Deliciosa (Plant 1)
            [
                'plant_id' => 1,
                'type' => 'Water',
                'frequency_days' => 7,
                'last_completed' => now()->subDays(5),
            ],
            [
                'plant_id' => 1,
                'type' => 'Sunlight',
                'frequency_days' => 1,
                'last_completed' => now()->subHours(2),
            ],
            [
                'plant_id' => 1,
                'type' => 'Fertilize',
                'frequency_days' => 30,
                'last_completed' => now()->subDays(15),
            ],
            // Pothos (Plant 2)
            [
                'plant_id' => 2,
                'type' => 'Water',
                'frequency_days' => 5,
                'last_completed' => now()->subDays(3),
            ],
            [
                'plant_id' => 2,
                'type' => 'Sunlight',
                'frequency_days' => 1,
                'last_completed' => now()->subHours(3),
            ],
            [
                'plant_id' => 2,
                'type' => 'Fertilize',
                'frequency_days' => 30,
                'last_completed' => now()->subDays(20),
            ],
            // Snake Plant (Plant 3)
            [
                'plant_id' => 3,
                'type' => 'Water',
                'frequency_days' => 14,
                'last_completed' => now()->subDays(10),
            ],
            [
                'plant_id' => 3,
                'type' => 'Sunlight',
                'frequency_days' => 1,
                'last_completed' => now()->subHours(1),
            ],
            [
                'plant_id' => 3,
                'type' => 'Fertilize',
                'frequency_days' => 60,
                'last_completed' => now()->subDays(45),
            ],
            // Fiddle Leaf Fig (Plant 4)
            [
                'plant_id' => 4,
                'type' => 'Water',
                'frequency_days' => 7,
                'last_completed' => now()->subDays(12),
            ],
            [
                'plant_id' => 4,
                'type' => 'Sunlight',
                'frequency_days' => 1,
                'last_completed' => now()->subDays(2),
            ],
            [
                'plant_id' => 4,
                'type' => 'Fertilize',
                'frequency_days' => 30,
                'last_completed' => now()->subDays(35),
            ],
            // ZZ Plant (Plant 5)
            [
                'plant_id' => 5,
                'type' => 'Water',
                'frequency_days' => 14,
                'last_completed' => now()->subDays(8),
            ],
            [
                'plant_id' => 5,
                'type' => 'Sunlight',
                'frequency_days' => 1,
                'last_completed' => now()->subHours(4),
            ],
            [
                'plant_id' => 5,
                'type' => 'Fertilize',
                'frequency_days' => 60,
                'last_completed' => now()->subDays(40),
            ],
        ];

        foreach ($careTasks as $task) {
            CareTask::create($task);
        }
    }
}
