<?php
// app/Services/ReceivingService.php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Unit;
use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceivingService
{
    public function __construct(
        protected StockMovementService $movementService
    ) {}

    /**
     * Simpan penerimaan barang (Hybrid: Existing & New Item)
     */
        public function createReceiving(array $data): Receiving
    {
        return DB::transaction(function () use ($data) {
            // 1. Buat Header Penerimaan
            $receiving = Receiving::create([
                'receiving_number'  => Receiving::generateNumber(),
                'supplier_id'       => $data['supplier_id'] ?? null,
                'funding_source_id' => $data['funding_source_id'] ?? null,
                'receiving_date'    => $data['receiving_date'],
                'invoice_number'    => $data['invoice_number'] ?? null,
                'note'              => $data['note'] ?? null,
                'created_by'        => Auth::id(),
            ]);

            // 2. Loop Items
            foreach ($data['items'] as $itemData) {
                $itemId = $itemData['item_id'] ?? null;
                $isNewItem = empty($itemId);

                if ($isNewItem) {
                    // --- LOGIKA BARANG BARU ---
                    if (empty($itemData['name']) || empty($itemData['category_id']) || empty($itemData['unit_id'])) {
                        throw ValidationException::withMessages([
                            'items' => 'Data barang baru (Nama, Kategori, Satuan) harus lengkap.'
                        ]);
                    }

                    $category = Category::find($itemData['category_id']);
                    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 4));
                    $year = date('Y');
                    $month = date('m');
                    $lastItem = Item::where('code', 'LIKE', "{$prefix}-{$year}-{$month}-%")->orderBy('code', 'desc')->first();
                    $sequence = $lastItem ? str_pad((int) substr($lastItem->code, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';

                    $item = Item::create([
                        'code'          => "{$prefix}-{$year}-{$month}-{$sequence}",
                        'name'          => $itemData['name'],
                        'category_id'   => $itemData['category_id'],
                        'location_id'   => $itemData['location_id'] ?? \App\Models\Location::first()?->id,
                        'supplier_id'   => $data['supplier_id'] ?? null,
                        'funding_source_id' => $data['funding_source_id'] ?? null,
                        'unit_id'       => $itemData['unit_id'],
                        'item_type'     => $itemData['item_type'] ?? 'asset',
                        'quantity'      => (int) $itemData['quantity'], // ← LANGSUNG ISI
                        'price'         => $itemData['price'] ?? 0,
                        'condition'     => ItemConditionEnum::BAIK->value,
                        'status'        => ItemStatusEnum::TERSEDIA->value,
                        'created_by'    => Auth::id(),
                        'updated_by'    => Auth::id(),
                    ]);
                    $itemId = $item->id;
                } else {
                    // --- LOGIKA BARANG EXISTING ---
                    $item = Item::lockForUpdate()->find($itemId);
                    if (!$item) {
                        throw ValidationException::withMessages(['items' => 'Barang tidak ditemukan.']);
                    }
                }

                $quantity = (int) $itemData['quantity'];
                $stockBefore = $isNewItem ? 0 : $item->quantity;
                $stockAfter = $stockBefore + $quantity;

                // 3. Simpan Detail Penerimaan
                ReceivingItem::create([
                    'receiving_id' => $receiving->id,
                    'item_id'      => $item->id,
                    'quantity'     => $quantity,
                    'price'        => $itemData['price'] ?? $item->price,
                    'note'         => $itemData['note'] ?? null,
                ]);

                // 4. Update Stok Barang (HANYA JIKA BARANG EXISTING)
                if (!$isNewItem) {
                    $item->update([
                        'quantity' => $stockAfter,
                        'price'    => !empty($itemData['price']) ? $itemData['price'] : $item->price,
                    ]);
                } else {
                    if (!empty($itemData['price'])) {
                        $item->update(['price' => $itemData['price']]);
                    }
                }

                // 5. Catat Log Pergerakan Stok
                $this->movementService->record(
                    item: $item,
                    type: 'in',
                    quantity: $quantity,
                    note: "Penerimaan Barang #{$receiving->receiving_number}",
                    referenceType: 'receiving',
                    referenceId: $receiving->id,
                    userId: Auth::id(),
                    stockBefore: $stockBefore,
                    stockAfter: $stockAfter,
                );
            }

            return $receiving;
        });
    }
}