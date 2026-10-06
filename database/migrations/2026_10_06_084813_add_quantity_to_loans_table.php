<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('quantity')
                  ->default(1)
                  ->after('item_id')
                  ->comment('Jumlah yang dipinjam. per_unit selalu 1, per_batch bisa >1');
        });

        DB::statement("ALTER TABLE loans ADD CONSTRAINT chk_loan_quantity CHECK (quantity >= 1)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE loans DROP CONSTRAINT IF EXISTS chk_loan_quantity");
        
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};