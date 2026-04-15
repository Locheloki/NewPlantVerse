<?php

namespace Database\Seeders;

use App\Models\CareTask;
use Illuminate\Database\Seeder;

class CareTaskSeeder extends Seeder
{
    public function run(): void
    {
        $careTasks = [
            // ===== ADMIN ACCOUNT - TROPICAL FRUITS & VEGETABLES (User 2) =====

            // 1. Tomato (User 2, Plant 1)
            ['plant_id' => 1, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(12)],
            ['plant_id' => 1, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(2)],
            ['plant_id' => 1, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(10)],

            // 2. Chili (User 2, Plant 2)
            ['plant_id' => 2, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(18)],
            ['plant_id' => 2, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(4)],
            ['plant_id' => 2, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(12)],

            // 3. Eggplant (User 2, Plant 3)
            ['plant_id' => 3, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subDays(1)],
            ['plant_id' => 3, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(8)],
            ['plant_id' => 3, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(5)],

            // 4. Okra (User 2, Plant 4)
            ['plant_id' => 4, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(20)],
            ['plant_id' => 4, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(1)],
            ['plant_id' => 4, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(8)],

            // 5. Kangkong (User 2, Plant 5)
            ['plant_id' => 5, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(10)],
            ['plant_id' => 5, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(3)],
            ['plant_id' => 5, 'type' => 'Fertilize', 'frequency_days' => 21, 'last_completed' => now()->subDays(14)],

            // 6. Pechay (User 2, Plant 6)
            ['plant_id' => 6, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(14)],
            ['plant_id' => 6, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(5)],
            ['plant_id' => 6, 'type' => 'Fertilize', 'frequency_days' => 21, 'last_completed' => now()->subDays(16)],

            // 7. String Beans (User 2, Plant 7)
            ['plant_id' => 7, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(22)],
            ['plant_id' => 7, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(6)],
            ['plant_id' => 7, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(11)],

            // 8. Papaya (User 2, Plant 8)
            ['plant_id' => 8, 'type' => 'Water', 'frequency_days' => 2, 'last_completed' => now()->subDays(1)],
            ['plant_id' => 8, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(7)],
            ['plant_id' => 8, 'type' => 'Fertilize', 'frequency_days' => 21, 'last_completed' => now()->subDays(18)],

            // 9. Banana (User 2, Plant 9)
            ['plant_id' => 9, 'type' => 'Water', 'frequency_days' => 3, 'last_completed' => now()->subDays(2)],
            ['plant_id' => 9, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(9)],
            ['plant_id' => 9, 'type' => 'Fertilize', 'frequency_days' => 21, 'last_completed' => now()->subDays(20)],

            // 10. Calamansi (User 2, Plant 10)
            ['plant_id' => 10, 'type' => 'Water', 'frequency_days' => 3, 'last_completed' => now()->subDays(1)],
            ['plant_id' => 10, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(2)],
            ['plant_id' => 10, 'type' => 'Fertilize', 'frequency_days' => 21, 'last_completed' => now()->subDays(12)],

            // 11. Watermelon (User 2, Plant 11)
            ['plant_id' => 11, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subHours(16)],
            ['plant_id' => 11, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(11)],
            ['plant_id' => 11, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(9)],

            // 12. Squash (User 2, Plant 12)
            ['plant_id' => 12, 'type' => 'Water', 'frequency_days' => 1, 'last_completed' => now()->subDays(1)],
            ['plant_id' => 12, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(13)],
            ['plant_id' => 12, 'type' => 'Fertilize', 'frequency_days' => 14, 'last_completed' => now()->subDays(6)],

            // ===== DEMO USER ACCOUNT (User 1) =====

            // 13. Monstera Deliciosa (User 1, Plant 13)
            ['plant_id' => 13, 'type' => 'Water', 'frequency_days' => 7, 'last_completed' => now()->subDays(5)],
            ['plant_id' => 13, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(2)],
            ['plant_id' => 13, 'type' => 'Fertilize', 'frequency_days' => 30, 'last_completed' => now()->subDays(15)],

            // 14. Pothos (User 1, Plant 14)
            ['plant_id' => 14, 'type' => 'Water', 'frequency_days' => 5, 'last_completed' => now()->subDays(3)],
            ['plant_id' => 14, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(3)],
            ['plant_id' => 14, 'type' => 'Fertilize', 'frequency_days' => 30, 'last_completed' => now()->subDays(20)],

            // 15. Snake Plant (User 1, Plant 15)
            ['plant_id' => 15, 'type' => 'Water', 'frequency_days' => 14, 'last_completed' => now()->subDays(10)],
            ['plant_id' => 15, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(1)],
            ['plant_id' => 15, 'type' => 'Fertilize', 'frequency_days' => 60, 'last_completed' => now()->subDays(45)],

            // 16. Fiddle Leaf Fig (User 1, Plant 16)
            ['plant_id' => 16, 'type' => 'Water', 'frequency_days' => 7, 'last_completed' => now()->subDays(12)],
            ['plant_id' => 16, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subDays(2)],
            ['plant_id' => 16, 'type' => 'Fertilize', 'frequency_days' => 30, 'last_completed' => now()->subDays(35)],

            // 17. ZZ Plant (User 1, Plant 17)
            ['plant_id' => 17, 'type' => 'Water', 'frequency_days' => 14, 'last_completed' => now()->subDays(8)],
            ['plant_id' => 17, 'type' => 'Sunlight', 'frequency_days' => 1, 'last_completed' => now()->subHours(4)],
            ['plant_id' => 17, 'type' => 'Fertilize', 'frequency_days' => 60, 'last_completed' => now()->subDays(40)],
        ];

        foreach ($careTasks as $task) {
            CareTask::create($task);
        }
    }
}
