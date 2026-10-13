<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('type', 20)->comment('in / out / adjustment');
            $table->integer('quantity')->comment('Selalu positif');
            $table->integer('stock_before');
            $table->integer('stock_after');
            
            // Reference (polymorphic-like, tapi pakai string + id)
            $table->string('reference_type', 50)->nullable()
                  ->comment('stock_request / stock_opname / purchase');
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('created_at')->useCurrent();

            $table->index(['item_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('type');
        });

        DB::statement("
            ALTER TABLE stock_movements 
            ADD CONSTRAINT chk_stock_movement_type 
            CHECK (type IN ('in', 'out', 'adjustment'))
        ");

        DB::statement("
            ALTER TABLE stock_movements 
            ADD CONSTRAINT chk_stock_movement_qty 
            CHECK (quantity > 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};