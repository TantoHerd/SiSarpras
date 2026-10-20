<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop constraint FK lama (karena akan diubah jadi nullable)
        DB::statement("ALTER TABLE stock_requests DROP CONSTRAINT IF EXISTS stock_requests_requester_id_foreign");

        // 2. Tambah kolom snapshot (nullable dulu — supaya bisa backfill)
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->string('requester_name', 100)->nullable()->after('requester_id');
            $table->string('requester_nis_nip', 50)->nullable()->after('requester_name');
            $table->string('requester_phone', 20)->nullable()->after('requester_nis_nip');
            $table->string('source', 20)->default('internal')->after('requester_type');
        });

        // 3. Backfill data lama dari tabel users
        DB::statement("
            UPDATE stock_requests sr 
            SET 
                requester_name = COALESCE(u.name, 'Unknown'),
                requester_nis_nip = COALESCE(u.nip, '-'),
                requester_phone = u.phone
            FROM users u 
            WHERE sr.requester_id = u.id
        ");

        // 4. Ubah requester_id jadi nullable
        DB::statement("ALTER TABLE stock_requests ALTER COLUMN requester_id DROP NOT NULL");

        // 5. Tambah FK baru (nullOnDelete)
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->foreign('requester_id')->references('id')->on('users')->nullOnDelete();
        });

        // 6. Ubah kolom snapshot jadi NOT NULL (sekarang sudah terisi)
        DB::statement("ALTER TABLE stock_requests ALTER COLUMN requester_name SET NOT NULL");
        DB::statement("ALTER TABLE stock_requests ALTER COLUMN requester_nis_nip SET NOT NULL");

        // 7. Check constraint untuk source
        DB::statement("ALTER TABLE stock_requests ADD CONSTRAINT chk_stock_request_source CHECK (source IN ('internal', 'portal'))");
    }

    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropForeign(['requester_id']);
            $table->dropColumn(['requester_name', 'requester_nis_nip', 'requester_phone', 'source']);
        });

        DB::statement("ALTER TABLE stock_requests DROP CONSTRAINT IF EXISTS chk_stock_request_source");
    }
};