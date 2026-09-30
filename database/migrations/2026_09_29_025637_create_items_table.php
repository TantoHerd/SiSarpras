<?php
// database/migrations/xxxx_create_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            
            // Spesifikasi
            $table->string('brand', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('serial_number', 100)->unique()->nullable();
            $table->integer('purchase_year')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            
            // Status & Kondisi
            $table->string('condition', 20)->default('baik');
            $table->string('status', 20)->default('tersedia');
            $table->integer('quantity')->default(1);
            
            // Media
            $table->string('image')->nullable();
            $table->string('barcode')->unique()->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('category_id');
            $table->index('location_id');
            $table->index('status');
            $table->index('condition');
        });

        // Check Constraints (PostgreSQL)
        DB::statement("ALTER TABLE items ADD CONSTRAINT chk_condition CHECK (condition IN ('baik', 'rusak_ringan', 'rusak_berat'))");
        DB::statement("ALTER TABLE items ADD CONSTRAINT chk_status CHECK (status IN ('tersedia', 'dipinjam', 'perbaikan', 'tidak_aktif'))");
        DB::statement("ALTER TABLE items ADD CONSTRAINT chk_quantity CHECK (quantity > 0)");
        DB::statement("ALTER TABLE items ADD CONSTRAINT chk_price CHECK (price >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};