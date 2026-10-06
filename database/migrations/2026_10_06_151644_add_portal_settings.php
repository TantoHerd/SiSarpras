<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'group_name'  => 'portal',
                'key'         => 'portal_siswa_enabled',
                'value'       => 'false',
                'type'        => 'boolean',
                'description' => 'Aktifkan portal siswa untuk peminjaman',
                'is_public'   => true,
            ],
            [
                'group_name'  => 'portal',
                'key'         => 'portal_siswa_max_loans',
                'value'       => '3',
                'type'        => 'integer',
                'description' => 'Maksimal pinjaman aktif per siswa',
                'is_public'   => false,
            ],
            [
                'group_name'  => 'portal',
                'key'         => 'portal_siswa_max_days',
                'value'       => '7',
                'type'        => 'integer',
                'description' => 'Durasi pinjam siswa (hari)',
                'is_public'   => false,
            ],
            [
                'group_name'  => 'portal',
                'key'         => 'portal_siswa_welcome_text',
                'value'       => 'Selamat datang di Portal Peminjaman Siswa. Silakan isi data diri Anda untuk memulai.',
                'type'        => 'string',
                'description' => 'Teks sambutan di halaman portal',
                'is_public'   => true,
            ],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group_name', 'portal')->delete();
    }
};