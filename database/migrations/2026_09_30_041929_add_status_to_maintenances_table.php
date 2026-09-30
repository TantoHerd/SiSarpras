<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Status maintenance: 'in_progress', 'completed'
            $table->string('status', 20)->default('completed')->after('type');
            
            // Field untuk tracking penyelesaian
            $table->timestamp('completed_at')->nullable()->after('next_maintenance_date');
            $table->foreignId('completed_by')->nullable()->after('completed_at')
                  ->constrained('users')->onDelete('set null');
            $table->text('completion_note')->nullable()->after('completed_by');
        });

        // Set status default untuk data yang sudah ada:
        // - Rutin → completed
        // - Perbaikan → completed (asumsi sudah selesai karena ini data lama)
        DB::statement("UPDATE maintenances SET status = 'completed' WHERE status IS NULL OR status = ''");
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['status', 'completed_at', 'completed_by', 'completion_note']);
        });
    }
};