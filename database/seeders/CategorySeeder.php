<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // per_unit + asset
            ['name' => 'Elektronik',         'default_tracking_mode' => 'per_unit',  'default_item_type' => 'asset'],
            ['name' => 'Mebel',              'default_tracking_mode' => 'per_unit',  'default_item_type' => 'asset'],
            ['name' => 'Lab',                'default_tracking_mode' => 'per_unit',  'default_item_type' => 'asset'],
            ['name' => 'Olahraga',           'default_tracking_mode' => 'per_unit',  'default_item_type' => 'asset'],
            ['name' => 'Audio Visual',       'default_tracking_mode' => 'per_unit',  'default_item_type' => 'asset'],
            
            // per_batch + consumable (BHP)
            ['name' => 'ATK',                'default_tracking_mode' => 'per_batch', 'default_item_type' => 'consumable'],
            ['name' => 'Bahan Lab',          'default_tracking_mode' => 'per_batch', 'default_item_type' => 'consumable'],
            ['name' => 'Alat Rumah Tangga',  'default_tracking_mode' => 'per_batch', 'default_item_type' => 'consumable'],
            ['name' => 'Bangunan',           'default_tracking_mode' => 'per_batch', 'default_item_type' => 'asset'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'default_tracking_mode' => $cat['default_tracking_mode'],
                    'default_item_type'     => $cat['default_item_type'],
                ]
            );
        }
    }
}