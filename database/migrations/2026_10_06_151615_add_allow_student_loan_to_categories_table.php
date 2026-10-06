<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('allow_student_loan')
                  ->default(false)
                  ->after('default_tracking_mode')
                  ->comment('Apakah barang di kategori ini boleh dipinjam siswa via portal');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('allow_student_loan');
        });
    }
};