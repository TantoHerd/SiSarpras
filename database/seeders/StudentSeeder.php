<?php
// database/seeders/StudentSeeder.php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['nis' => '2024001', 'name' => 'Ahmad Fauzi',        'class' => 'X IPA 1',   'phone' => '081234567890'],
            ['nis' => '2024002', 'name' => 'Siti Nurhaliza',     'class' => 'X IPA 1',   'phone' => '081234567891'],
            ['nis' => '2024003', 'name' => 'Budi Santoso',       'class' => 'X IPA 2',   'phone' => '081234567892'],
            ['nis' => '2024004', 'name' => 'Dewi Lestari',       'class' => 'X IPA 2',   'phone' => '081234567893'],
            ['nis' => '2024005', 'name' => 'Eko Prasetyo',       'class' => 'XI IPA 1',  'phone' => '081234567894'],
            ['nis' => '2024006', 'name' => 'Fitri Handayani',    'class' => 'XI IPA 1',  'phone' => '081234567895'],
            ['nis' => '2024007', 'name' => 'Gilang Ramadhan',    'class' => 'XI IPS 1',  'phone' => '081234567896'],
            ['nis' => '2024008', 'name' => 'Hana Permata',       'class' => 'XI IPS 1',  'phone' => '081234567897'],
            ['nis' => '2024009', 'name' => 'Irfan Maulana',      'class' => 'XII IPA 1', 'phone' => '081234567898'],
            ['nis' => '2024010', 'name' => 'Jihan Aulia',        'class' => 'XII IPA 1', 'phone' => '081234567899'],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['nis' => $student['nis']],
                array_merge($student, ['is_active' => true])
            );
        }
    }
}