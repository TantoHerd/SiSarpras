<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID role
        $adminRole = Role::where('name', 'admin')->first()->id;
        $petugasRole = Role::where('name', 'petugas_sarpras')->first()->id;
        $kepsekRole = Role::where('name', 'kepala_sekolah')->first()->id;
        $guruRole = Role::where('name', 'guru')->first()->id;

        $users = [
            [
                'role_id' => $adminRole,
                'name' => 'Admin Sistem',
                'email' => 'admin@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'nip' => 'ADM001',
                'phone' => '081234567890',
                'is_active' => true,
            ],
            [
                'role_id' => $petugasRole,
                'name' => 'Petugas Sarpras',
                'email' => 'sarpras@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'nip' => 'SAR001',
                'phone' => '081234567891',
                'is_active' => true,
            ],
            [
                'role_id' => $kepsekRole,
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'nip' => 'KEP001',
                'phone' => '081234567892',
                'is_active' => true,
            ],
            [
                'role_id' => $guruRole,
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'budi@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'nip' => 'GUR001',
                'phone' => '081234567893',
                'is_active' => true,
            ],
            [
                'role_id' => $guruRole,
                'name' => 'Siti Aminah, S.Pd',
                'email' => 'siti@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'nip' => 'GUR002',
                'phone' => '081234567894',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}