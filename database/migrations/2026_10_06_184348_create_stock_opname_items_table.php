<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            
            $table->integer('system_stock')->comment('Snapshot stok sistem saat opname mulai');
            $table->integer('physical_stock')->default(0)->comment('Input petugas (stok fisik)');
            $table->integer('difference')->default(0)->comment('physical - system, bisa + atau -');
            
            $table->text('note')->nullable();
            
            $table->timestamps();

            $table->unique(['stock_opname_id', 'item_id']);
            $table->index('stock_opname_id');
            $table->index('item_id');
        });

        DB::statement("
            ALTER TABLE stock_opname_items 
            ADD CONSTRAINT chk_stock_opname_physical 
            CHECK (physical_stock >= 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};