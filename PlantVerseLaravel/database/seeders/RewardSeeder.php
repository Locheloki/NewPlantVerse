<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'title' => 'Premium Soil',
                'description' => 'High-quality potting soil for optimal plant growth',
                'pvt_cost' => 150,
                'icon' => '🌱',
                'image_hint' => 'soil_bag',
            ],
            [
                'title' => 'Plant Spray Bottle',
                'description' => 'Decorative spray bottle for misting plants',
                'pvt_cost' => 100,
                'icon' => '💧',
                'image_hint' => 'spray_bottle',
            ],
            [
                'title' => 'Ceramic Pot',
                'description' => 'Beautiful handcrafted ceramic pot',
                'pvt_cost' => 200,
                'icon' => '🏺',
                'image_hint' => 'ceramic_pot',
            ],
            [
                'title' => 'Plant Fertilizer Set',
                'description' => 'Complete set of plant fertilizers for all seasons',
                'pvt_cost' => 250,
                'icon' => '🍃',
                'image_hint' => 'fertilizer_set',
            ],
            [
                'title' => 'Plant Stand',
                'description' => 'Modern plant stand for displaying multiple plants',
                'pvt_cost' => 300,
                'icon' => '🎨',
                'image_hint' => 'plant_stand',
            ],
            [
                'title' => 'Watering Can',
                'description' => 'Ergonomic watering can with precision nozzle',
                'pvt_cost' => 120,
                'icon' => '🚿',
                'image_hint' => 'watering_can',
            ],
            [
                'title' => 'Plant Light',
                'description' => 'LED grow light for indoor plants',
                'pvt_cost' => 350,
                'icon' => '💡',
                'image_hint' => 'grow_light',
            ],
            [
                'title' => 'Care Guide Bundle',
                'description' => 'Digital guide with care tips for 50 plant species',
                'pvt_cost' => 75,
                'icon' => '📚',
                'image_hint' => 'care_guide',
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
}
