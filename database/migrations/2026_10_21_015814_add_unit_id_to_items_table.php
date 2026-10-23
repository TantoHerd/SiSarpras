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
            // Hapus kolom string 'unit' yang kita buat sebelumnya
            if (Schema::hasColumn('items', 'unit')) {
                $table->dropColumn('unit');
            }
            
            // Tambah FK unit_id (nullable agar data lama aman)
            $table->foreignId('unit_id')->nullable()->after('quantity')->constrained('units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->string('unit', 20)->default('unit')->after('quantity');
        });
    }
};