<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            
            $table->string('opname_number', 30)->unique()
                  ->comment('Auto: SO-YYYY-MM-NNN');
            
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            
            $table->string('status', 20)->default('draft')
                  ->comment('draft / completed / cancelled');
            
            // Started
            $table->foreignId('started_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('started_at')->useCurrent();
            
            // Completed
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            
            $table->text('note')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('location_id');
            $table->index('created_at');
        });

        DB::statement("
            ALTER TABLE stock_opnames 
            ADD CONSTRAINT chk_stock_opname_status 
            CHECK (status IN ('draft', 'completed', 'cancelled'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};