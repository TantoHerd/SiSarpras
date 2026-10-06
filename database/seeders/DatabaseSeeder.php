<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Urutan penting! Role dulu baru User
            RoleSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
            SupplierSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            FundingSourceSeeder::class,
            StudentSeeder::class,
        ]);
    }
}