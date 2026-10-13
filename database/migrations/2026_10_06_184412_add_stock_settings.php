<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'group_name'  => 'stock',
                'key'         => 'stock_low_threshold',
                'value'       => '5',
                'type'        => 'integer',
                'description' => 'Default batas minimal stok (kalau item tidak set min_stock)',
                'is_public'   => false,
            ],
            [
                'group_name'  => 'stock',
                'key'         => 'stock_request_max_items',
                'value'       => '5',
                'type'        => 'integer',
                'description' => 'Maksimal jenis barang per permintaan BHP',
                'is_public'   => false,
            ],
            [
                'group_name'  => 'stock',
                'key'         => 'stock_request_max_qty',
                'value'       => '10',
                'type'        => 'integer',
                'description' => 'Maksimal qty per barang dalam permintaan BHP',
                'is_public'   => false,
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
        DB::table('settings')->where('group_name', 'stock')->delete();
    }
};