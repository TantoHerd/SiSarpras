<?php
// database/seeders/SettingSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // School
            ['school', 'school_name', 'SMA Negeri 1 Contoh', 'string', 'Nama lengkap sekolah', true],
            ['school', 'school_address', 'Jl. Pendidikan No. 1, Kota Contoh', 'string', 'Alamat sekolah', true],
            ['school', 'school_phone', '(021) 1234567', 'string', 'Telepon sekolah', true],
            ['school', 'school_email', 'info@sekolah.sch.id', 'string', 'Email sekolah', true],
            ['school', 'school_logo', 'logo-default.png', 'file', 'Logo sekolah', true],
            ['school', 'school_npsn', '12345678', 'string', 'NPSN', false],
            ['school', 'school_website', 'https://sekolah.sch.id', 'string', 'Website', true],
            
            // Preference
            ['preference', 'app_name', 'Sistem Informasi Sarpras', 'string', 'Nama aplikasi', true],
            ['preference', 'app_short_name', 'SISARPRAS', 'string', 'Singkatan aplikasi', true],
            ['preference', 'app_version', '1.0.0', 'string', 'Versi aplikasi', true],
            ['preference', 'timezone', 'Asia/Jakarta', 'string', 'Zona waktu', false],
            ['preference', 'date_format', 'd-m-Y', 'string', 'Format tanggal', true],
            ['preference', 'currency_symbol', 'Rp', 'string', 'Simbol mata uang', true],
            ['preference', 'currency_position', 'before', 'string', 'Posisi mata uang', true],
            
            // Loan
            ['loan', 'max_loan_days', '7', 'integer', 'Maksimal hari peminjaman', false],
            ['loan', 'is_fine_active', 'false', 'boolean', 'Aktifkan denda', false],
            ['loan', 'fine_per_day', '0', 'integer', 'Denda per hari', false],
            ['loan', 'max_loan_per_user', '5', 'integer', 'Maksimal pinjam per user', false],
            
            // Notification
            ['notification', 'low_stock_threshold', '3', 'integer', 'Batas stok minimal', false],
            ['notification', 'maintenance_reminder_days', '7', 'integer', 'Pengingat maintenance', false],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->insert([
                'group_name' => $s[0],
                'key' => $s[1],
                'value' => $s[2],
                'type' => $s[3],
                'description' => $s[4],
                'is_public' => $s[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}