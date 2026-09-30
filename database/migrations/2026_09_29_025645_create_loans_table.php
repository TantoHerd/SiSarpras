<?php
// database/migrations/xxxx_create_loans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');
            $table->foreignId('borrower_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            
            $table->timestamp('loan_date')->useCurrent();
            $table->timestamp('due_date');
            $table->timestamp('return_date')->nullable();
            $table->string('status', 20)->default('dipinjam');
            $table->text('purpose')->nullable();
            
            // Denda
            $table->decimal('fine_amount', 15, 2)->default(0);
            $table->boolean('is_fine_paid')->default(false);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('item_id');
            $table->index('borrower_id');
            $table->index('status');
        });

        DB::statement("ALTER TABLE loans ADD CONSTRAINT chk_loan_status CHECK (status IN ('dipinjam', 'terlambat', 'dikembalikan'))");
        DB::statement("ALTER TABLE loans ADD CONSTRAINT chk_fine_amount CHECK (fine_amount >= 0)");
        DB::statement("ALTER TABLE loans ADD CONSTRAINT chk_return_date CHECK (return_date IS NULL OR return_date >= loan_date)");
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};