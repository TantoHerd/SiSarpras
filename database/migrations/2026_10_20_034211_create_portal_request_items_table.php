<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_request_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('portal_request_id')->constrained('portal_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            
            $table->integer('quantity')->default(1);
            $table->text('note')->nullable();
            
            $table->timestamps();
        });

        DB::statement("ALTER TABLE portal_request_items ADD CONSTRAINT chk_portal_item_qty CHECK (quantity >= 1)");
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_request_items');
    }
};