<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_request_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('stock_request_id')->constrained('stock_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            
            $table->integer('quantity_requested');
            $table->integer('quantity_approved')->nullable()
                  ->comment('Bisa beda dari requested (dikurangi petugas)');
            
            $table->text('note')->nullable();
            
            $table->timestamps();

            $table->index('stock_request_id');
            $table->index('item_id');
        });

        DB::statement("
            ALTER TABLE stock_request_items 
            ADD CONSTRAINT chk_stock_request_item_qty_req 
            CHECK (quantity_requested > 0)
        ");

        DB::statement("
            ALTER TABLE stock_request_items 
            ADD CONSTRAINT chk_stock_request_item_qty_app 
            CHECK (quantity_approved IS NULL OR quantity_approved >= 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_request_items');
    }
};