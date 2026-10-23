<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Header Penerimaan
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_number', 30)->unique(); // Format: RCV-YYYY-MM-NNN
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('funding_source_id')->nullable()->constrained('funding_sources')->nullOnDelete();
            $table->date('receiving_date');
            $table->string('invoice_number', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Detail Penerimaan
        Schema::create('receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained('receivings')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            
            $table->integer('quantity');
            $table->decimal('price', 15, 2)->nullable(); // Harga beli saat diterima
            $table->text('note')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receiving_items');
        Schema::dropIfExists('receivings');
    }
};