<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus constraint lama
        DB::statement("ALTER TABLE stock_requests DROP CONSTRAINT IF EXISTS chk_stock_request_requester");
        
        // 2. Tambahkan constraint baru yang menerima 'siswa'
        DB::statement("
            ALTER TABLE stock_requests 
            ADD CONSTRAINT chk_stock_request_requester 
            CHECK (requester_type IN ('guru', 'staf', 'petugas', 'siswa'))
        ");
    }

    public function down(): void
    {
        // Kembalikan ke constraint awal jika di-rollback
        DB::statement("ALTER TABLE stock_requests DROP CONSTRAINT IF EXISTS chk_stock_request_requester");
        DB::statement("
            ALTER TABLE stock_requests 
            ADD CONSTRAINT chk_stock_request_requester 
            CHECK (requester_type IN ('guru', 'staf', 'petugas'))
        ");
    }
};