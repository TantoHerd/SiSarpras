<?php
// app/Imports/ItemImport.php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\FundingSource;
use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ItemImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    protected int $successCount = 0;
    protected int $skipCount = 0;
    protected array $skipped = [];
    protected array $errors = [];

    public function model(array $row)
    {
        $name = trim((string) ($row['nama_barang'] ?? ''));
        if (empty($name) && empty($row['kategori']) && empty($row['lokasi'])) {
            return null; // Jangan hitung sebagai skip
        }
        $categoryName = trim((string) ($row['kategori'] ?? ''));
        $locationName = trim((string) ($row['lokasi'] ?? ''));
        $unitName = trim((string) ($row['satuan'] ?? ''));

        // 1. Cek field wajib
        if (empty($name) || empty($categoryName) || empty($locationName) || empty($unitName)) {
            $this->skipCount++;
            $this->skipped[] = ['baris' => $name ?: '-', 'alasan' => 'Nama, Kategori, Lokasi, atau Satuan kosong'];
            return null;
        }

        // 2. Cari relasi (Case Insensitive)
        $category = Category::where('name', 'ILIKE', $categoryName)->first();
        $location = Location::where('name', 'ILIKE', $locationName)->first();
        $unit = Unit::where('name', 'ILIKE', $unitName)->first();
        
        $supplierName = trim((string) ($row['supplier'] ?? ''));
        $supplier = $supplierName ? Supplier::where('name', 'ILIKE', $supplierName)->first() : null;
        
        $fundingCode = trim((string) ($row['sumber_dana'] ?? ''));
        $funding = $fundingCode ? FundingSource::where('code', 'ILIKE', $fundingCode)->first() : null;

        // 3. Validasi apakah data referensi ditemukan
        $missing = [];
        if (!$category) $missing[] = "Kategori '{$categoryName}'";
        if (!$location) $missing[] = "Lokasi '{$locationName}'";
        if (!$unit) $missing[] = "Satuan '{$unitName}'";
        if ($supplierName && !$supplier) $missing[] = "Supplier '{$supplierName}'";
        if ($fundingCode && !$funding) $missing[] = "Sumber Dana '{$fundingCode}'";

        if (!empty($missing)) {
            $this->skipCount++;
            $this->skipped[] = [
                'baris' => $name,
                'alasan' => 'Tidak ditemukan di sistem: ' . implode(', ', $missing)
            ];
            return null;
        }

        // 4. Proses Data
        $itemTypeStr = strtolower(trim((string) ($row['tipe_barang'] ?? 'aset')));
        $itemType = in_array($itemTypeStr, ['habis pakai', 'bhp', 'consumable']) ? 'consumable' : 'asset';

        $conditionStr = strtolower(trim((string) ($row['kondisi'] ?? 'baik')));
        $conditionEnum = match($conditionStr) {
            'rusak ringan' => ItemConditionEnum::RUSAK_RINGAN->value,
            'rusak berat'  => ItemConditionEnum::RUSAK_BERAT->value,
            default        => ItemConditionEnum::BAIK->value,
        };

        $quantity = (int) ($row['jumlah'] ?? 1);
        if ($quantity < 1) $quantity = 1;

        // Generate Kode Otomatis
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 4));
        $year = date('Y');
        $month = date('m');
        $lastItem = Item::where('code', 'LIKE', "{$prefix}-{$year}-{$month}-%")->orderBy('code', 'desc')->first();
        $sequence = $lastItem ? str_pad((int) substr($lastItem->code, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        $code = "{$prefix}-{$year}-{$month}-{$sequence}";

        $this->successCount++;
        return new Item([
            'code' => $code,
            'name' => $name,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'supplier_id' => $supplier?->id,
            'funding_source_id' => $funding?->id,
            'unit_id' => $unit->id,
            'item_type' => $itemType,
            'quantity' => $quantity,
            'condition' => $conditionEnum,
            'status' => ItemStatusEnum::TERSEDIA->value,
            'price' => (float) ($row['harga'] ?? 0),
            'purchase_year' => $row['tahun_perolehan'] ?? date('Y'),
            'min_stock' => ($itemType === 'consumable') ? (int) ($row['min_stok'] ?? 0) : null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function onError(Throwable $e) { $this->errors[] = $e->getMessage(); }
    public function onFailure(Failure ...$failures) {
        foreach ($failures as $failure) { $this->errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors()); }
    }

    public function getSuccessCount(): int { return $this->successCount; }
    public function getSkipCount(): int { return $this->skipCount; }
    public function getSkipped(): array { return $this->skipped; }
    public function getErrors(): array { return $this->errors; }
}