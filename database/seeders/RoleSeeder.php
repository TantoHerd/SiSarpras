<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrator sistem', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'petugas_sarpras', 'description' => 'Petugas Sarana & Prasarana', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kepala_sekolah', 'description' => 'Kepala Sekolah (Read Only)', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'guru', 'description' => 'Guru & Staf (Peminjam)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}