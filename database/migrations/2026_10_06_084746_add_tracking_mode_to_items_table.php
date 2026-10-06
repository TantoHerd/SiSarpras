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
            $table->string('tracking_mode', 20)
                  ->nullable()
                  ->after('quantity')
                  ->comment('Override kategori. Null = pakai default kategori');
        });

        // Check constraint PostgreSQL (nullable OK)
        DB::statement("
            ALTER TABLE items 
            ADD CONSTRAINT chk_items_tracking_mode 
            CHECK (tracking_mode IS NULL OR tracking_mode IN ('per_unit', 'per_batch'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE items DROP CONSTRAINT IF EXISTS chk_items_tracking_mode");
        
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('tracking_mode');
        });
    }
};