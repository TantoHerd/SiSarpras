<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_requests', function (Blueprint $table) {
            // Tambahkan kolom request_number jika belum ada
            if (!Schema::hasColumn('portal_requests', 'request_number')) {
                $table->string('request_number', 30)->nullable()->unique()->after('id');
            }
            
            // Pastikan foreignId student_id nullable (untuk guru)
            $table->foreignId('student_id')->nullable()->change();
            
            // Ubah item_id jadi nullable (karena sekarang multi-item)
            if (Schema::hasColumn('portal_requests', 'item_id')) {
                $table->foreignId('item_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // ...
    }
};