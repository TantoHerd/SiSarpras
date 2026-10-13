<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('item_type', 20)
                  ->default('asset')
                  ->after('tracking_mode')
                  ->comment('asset = aset tetap, consumable = barang habis pakai');
            
            $table->integer('min_stock')
                  ->nullable()
                  ->after('item_type')
                  ->comment('Batas minimal stok untuk notifikasi (khusus consumable)');
        });

        // Check constraint
        DB::statement("
            ALTER TABLE items 
            ADD CONSTRAINT chk_items_item_type 
            CHECK (item_type IN ('asset', 'consumable'))
        ");

        DB::statement("
            ALTER TABLE items 
            ADD CONSTRAINT chk_items_min_stock 
            CHECK (min_stock IS NULL OR min_stock >= 0)
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE items DROP CONSTRAINT IF EXISTS chk_items_item_type");
        DB::statement("ALTER TABLE items DROP CONSTRAINT IF EXISTS chk_items_min_stock");

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'min_stock']);
        });
    }
};