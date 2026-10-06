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
            $table->string('default_tracking_mode', 20)
                  ->default('per_unit')
                  ->after('icon');
        });

        // Check constraint PostgreSQL
        DB::statement("
            ALTER TABLE categories 
            ADD CONSTRAINT chk_categories_tracking_mode 
            CHECK (default_tracking_mode IN ('per_unit', 'per_batch'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE categories DROP CONSTRAINT IF EXISTS chk_categories_tracking_mode");
        
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('default_tracking_mode');
        });
    }
};