<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'warranty_reminder_days'],
            [
                'group_name'  => 'preference',
                'value'       => '30', // Default 30 hari (1 bulan)
                'type'        => 'integer',
                'description' => 'Pengingat garansi (dalam hari sebelum berakhir)',
                'is_public'   => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'warranty_reminder_days')->delete();
    }
};