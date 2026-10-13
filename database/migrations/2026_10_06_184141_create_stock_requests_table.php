<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            
            $table->string('request_number', 30)->unique()
                  ->comment('Auto: SR-YYYY-MM-NNN');
            
            // Requester
            $table->foreignId('requester_id')->constrained('users')->restrictOnDelete();
            $table->string('requester_type', 20)->default('guru')
                  ->comment('guru / staf / petugas');
            
            $table->text('purpose');
            $table->string('status', 20)->default('pending')
                  ->comment('pending / approved / rejected');
            
            // Approval
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('note')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('requester_id');
            $table->index('created_at');
        });

        DB::statement("
            ALTER TABLE stock_requests 
            ADD CONSTRAINT chk_stock_request_status 
            CHECK (status IN ('pending', 'approved', 'rejected'))
        ");

        DB::statement("
            ALTER TABLE stock_requests 
            ADD CONSTRAINT chk_stock_request_requester 
            CHECK (requester_type IN ('guru', 'staf', 'petugas'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};