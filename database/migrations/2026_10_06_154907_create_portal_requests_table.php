<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_requests', function (Blueprint $table) {
            $table->id();
            
            // Identitas siswa (snapshot — kalau siswa dihapus, data tetap ada)
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('nis', 20);
            $table->string('student_name', 100);
            $table->string('student_class', 20);
            $table->string('student_phone', 20)->nullable();
            
            // Item yang diminta
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            
            // Detail permintaan
            $table->text('purpose');
            $table->date('loan_date');
            $table->date('due_date');
            
            // Status workflow
            $table->string('status', 20)->default('pending');
            // pending / approved / rejected / expired
            
            // Approval
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            
            // Meta
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('nis');
            $table->index('created_at');
        });

        // Check constraint PostgreSQL
        DB::statement("
            ALTER TABLE portal_requests 
            ADD CONSTRAINT chk_portal_status 
            CHECK (status IN ('pending', 'approved', 'rejected', 'expired'))
        ");

        DB::statement("
            ALTER TABLE portal_requests 
            ADD CONSTRAINT chk_portal_quantity 
            CHECK (quantity >= 1)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_requests');
    }
};