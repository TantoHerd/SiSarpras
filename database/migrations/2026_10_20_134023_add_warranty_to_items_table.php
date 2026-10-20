<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Nullable karena BHP (consumable) tidak butuh garansi
            $table->date('warranty_start_date')->nullable()->after('purchase_year');
            $table->integer('warranty_duration_months')->nullable()->after('warranty_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['warranty_start_date', 'warranty_duration_months']);
        });
    }
};