<?php
namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unit', 'symbol' => 'unit'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Rim', 'symbol' => 'rim'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Set', 'symbol' => 'set'],
            ['name' => 'Lusin', 'symbol' => 'lusin'],
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Buah', 'symbol' => 'buah'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['name' => $unit['name']], $unit);
        }
    }
}