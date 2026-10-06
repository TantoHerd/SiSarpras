<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('funding_source_id')
                  ->nullable()
                  ->after('supplier_id')
                  ->constrained('funding_sources')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['funding_source_id']);
            $table->dropColumn('funding_source_id');
        });
    }
};