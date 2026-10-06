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
            // per_unit — aset unik
            ['name' => 'Elektronik',    'default_tracking_mode' => 'per_unit'],
            ['name' => 'Mebel',         'default_tracking_mode' => 'per_unit'],
            ['name' => 'Lab',           'default_tracking_mode' => 'per_unit'],
            ['name' => 'Olahraga',      'default_tracking_mode' => 'per_unit'],
            ['name' => 'Audio Visual',  'default_tracking_mode' => 'per_unit'],
            
            // per_batch — barang habis pakai / quantity banyak
            ['name' => 'ATK',                 'default_tracking_mode' => 'per_batch'],
            ['name' => 'Bangunan',            'default_tracking_mode' => 'per_batch'],
            ['name' => 'Alat Rumah Tangga',   'default_tracking_mode' => 'per_batch'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                ['default_tracking_mode' => $cat['default_tracking_mode']]
            );
        }
    }
}