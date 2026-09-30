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
            ['name' => 'Elektronik', 'description' => 'Barang elektronik dan perangkat digital', 'icon' => 'fa-laptop'],
            ['name' => 'Mebel', 'description' => 'Perabotan dan furnitur', 'icon' => 'fa-chair'],
            ['name' => 'ATK', 'description' => 'Alat Tulis Kantor', 'icon' => 'fa-pen'],
            ['name' => 'Laboratorium', 'description' => 'Alat dan bahan laboratorium', 'icon' => 'fa-flask'],
            ['name' => 'Olahraga', 'description' => 'Peralatan olahraga', 'icon' => 'fa-futbol'],
            ['name' => 'Audio Visual', 'description' => 'Perangkat audio dan visual', 'icon' => 'fa-video'],
            ['name' => 'Bangunan', 'description' => 'Peralatan dan material bangunan', 'icon' => 'fa-building'],
            ['name' => 'Lainnya', 'description' => 'Kategori barang lainnya', 'icon' => 'fa-boxes'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}