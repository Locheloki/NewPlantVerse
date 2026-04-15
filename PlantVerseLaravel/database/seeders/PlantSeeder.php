<?php

namespace Database\Seeders;

use App\Models\Plant;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account plants - Tropical fruits and vegetables showcase
        $plants = [
            // VEGETABLES
            [
                'user_id' => 2, // Admin user
                'name' => 'My Tomato Plant',
                'species' => 'Solanum lycopersicum',
                'photo_url' => 'https://images.unsplash.com/photo-1592841494900-b0228403c894?w=400&h=400&fit=crop',
                'care_consistency' => 100,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Spicy Chili',
                'species' => 'Capsicum annuum',
                'photo_url' => 'https://images.unsplash.com/photo-1599599810694-51e5d9ac76e4?w=400&h=400&fit=crop',
                'care_consistency' => 92,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Purple Eggplant',
                'species' => 'Solanum melongena',
                'photo_url' => 'https://images.unsplash.com/photo-1597619437933-e81a70ea4e83?w=400&h=400&fit=crop',
                'care_consistency' => 45,
                'is_neglected' => true,
            ],
            [
                'user_id' => 2,
                'name' => 'Fresh Okra',
                'species' => 'Abelmoschus esculentus',
                'photo_url' => 'https://images.unsplash.com/photo-1621071644090-70ee9a0db326?w=400&h=400&fit=crop',
                'care_consistency' => 68,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Kangkong Garden',
                'species' => 'Ipomoea aquatica',
                'photo_url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=400&fit=crop',
                'care_consistency' => 85,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Pechay Bunch',
                'species' => 'Brassica rapa',
                'photo_url' => 'https://images.unsplash.com/photo-1591621990835-cbf51c670dfb?w=400&h=400&fit=crop',
                'care_consistency' => 32,
                'is_neglected' => true,
            ],
            [
                'user_id' => 2,
                'name' => 'String Beans Tower',
                'species' => 'Phaseolus vulgaris',
                'photo_url' => 'https://images.unsplash.com/photo-1584622614875-3a56c1b89fdb?w=400&h=400&fit=crop',
                'care_consistency' => 78,
                'is_neglected' => false,
            ],

            // FRUITS
            [
                'user_id' => 2,
                'name' => 'Papaya Tree',
                'species' => 'Carica papaya',
                'photo_url' => 'https://images.unsplash.com/photo-1599599810694-51e5d9ac76e4?w=400&h=400&fit=crop',
                'care_consistency' => 55,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Banana Cluster',
                'species' => 'Musa acuminata',
                'photo_url' => 'https://images.unsplash.com/photo-1528761312658-3c1f00be9eb7?w=400&h=400&fit=crop',
                'care_consistency' => 20,
                'is_neglected' => true,
            ],
            [
                'user_id' => 2,
                'name' => 'Calamansi Tree',
                'species' => 'Citrus microcarpa',
                'photo_url' => 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&h=400&fit=crop',
                'care_consistency' => 95,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Sweet Watermelon',
                'species' => 'Citrullus lanatus',
                'photo_url' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=400&fit=crop',
                'care_consistency' => 72,
                'is_neglected' => false,
            ],
            [
                'user_id' => 2,
                'name' => 'Squash Garden',
                'species' => 'Cucurbita maxima',
                'photo_url' => 'https://images.unsplash.com/photo-1542274604-71dd0a7f8bb4?w=400&h=400&fit=crop',
                'care_consistency' => 80,
                'is_neglected' => false,
            ],

            // Keep demo user plants
            [
                'user_id' => 1,
                'name' => 'Monstera Deliciosa',
                'species' => 'Monstera deliciosa',
                'photo_url' => 'https://images.unsplash.com/photo-1582794543139-c93bdf5ba6b5?w=400&h=400&fit=crop',
                'care_consistency' => 90,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Pothos',
                'species' => 'Epipremnum aureum',
                'photo_url' => 'https://images.unsplash.com/photo-1611980154055-d4a8f4041931?w=400&h=400&fit=crop',
                'care_consistency' => 75,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Snake Plant',
                'species' => 'Sansevieria trifasciata',
                'photo_url' => 'https://images.unsplash.com/photo-1585707032514-b3400ca199e7?w=400&h=400&fit=crop',
                'care_consistency' => 95,
                'is_neglected' => false,
            ],
            [
                'user_id' => 1,
                'name' => 'Fiddle Leaf Fig',
                'species' => 'Ficus lyrata',
                'photo_url' => 'https://images.unsplash.com/photo-1615521371857-35312e2ef57d?w=400&h=400&fit=crop',
                'care_consistency' => 65,
                'is_neglected' => true,
            ],
            [
                'user_id' => 1,
                'name' => 'ZZ Plant',
                'species' => 'Zamioculcas zamiifolia',
                'photo_url' => 'https://images.unsplash.com/photo-1577720643272-265f434b3b1f?w=400&h=400&fit=crop',
                'care_consistency' => 92,
                'is_neglected' => false,
            ],
        ];

        foreach ($plants as $plant) {
            Plant::create($plant);
        }
    }
}
