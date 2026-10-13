<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('default_item_type', 20)
                  ->default('asset')
                  ->after('allow_student_loan')
                  ->comment('Default type untuk item baru di kategori ini');
        });

        DB::statement("
            ALTER TABLE categories 
            ADD CONSTRAINT chk_categories_default_item_type 
            CHECK (default_item_type IN ('asset', 'consumable'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE categories DROP CONSTRAINT IF EXISTS chk_categories_default_item_type");

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('default_item_type');
        });
    }
};