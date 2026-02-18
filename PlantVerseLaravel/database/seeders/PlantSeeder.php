<?php

namespace Database\Seeders;

use App\Models\Plant;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        $plants = [
            [
                'user_id' => 1,
                'name' => 'Monstera Deliciosa',
                'species' => 'Monstera deliciosa',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Monstera',
                'care_consistency' => 90,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Pothos',
                'species' => 'Epipremnum aureum',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Pothos',
                'care_consistency' => 75,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Snake Plant',
                'species' => 'Sansevieria trifasciata',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Snake+Plant',
                'care_consistency' => 95,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Fiddle Leaf Fig',
                'species' => 'Ficus lyrata',
                'photo_url' => 'https://via.placeholder.com/300x300?text=Fiddle+Leaf+Fig',
                'care_consistency' => 65,
                'is_neglected' => true,
            ],
            [
                'user_id' => 1,
                'name' => 'ZZ Plant',
                'species' => 'Zamioculcas zamiifolia',
                'photo_url' => 'https://via.placeholder.com/300x300?text=ZZ+Plant',
                'care_consistency' => 92,
                'is_neglected' => false,
            ],
        ];

        foreach ($plants as $plant) {
            Plant::create($plant);
        }
    }
}
