<?php
// database/seeders/LocationSeeder.php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Ruang Kelas 1A', 'floor' => 'Lantai 1', 'description' => 'Ruang kelas 1A'],
            ['name' => 'Ruang Kelas 1B', 'floor' => 'Lantai 1', 'description' => 'Ruang kelas 1B'],
            ['name' => 'Ruang Kelas 2A', 'floor' => 'Lantai 1', 'description' => 'Ruang kelas 2A'],
            ['name' => 'Laboratorium Komputer', 'floor' => 'Lantai 2', 'description' => 'Lab komputer'],
            ['name' => 'Laboratorium IPA', 'floor' => 'Lantai 2', 'description' => 'Lab IPA'],
            ['name' => 'Perpustakaan', 'floor' => 'Lantai 1', 'description' => 'Ruang perpustakaan'],
            ['name' => 'Ruang Guru', 'floor' => 'Lantai 1', 'description' => 'Ruang guru & staf'],
            ['name' => 'Ruang Kepala Sekolah', 'floor' => 'Lantai 1', 'description' => 'Ruang kepala sekolah'],
            ['name' => 'Gudang Utama', 'floor' => 'Lantai 1', 'description' => 'Gudang penyimpanan utama'],
            ['name' => 'Aula Serbaguna', 'floor' => 'Lantai 2', 'description' => 'Aula untuk kegiatan'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}