<?php
// app/Services/PortalBhpService.php

namespace App\Services;

use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PortalBhpService
{
    public function __construct(
        protected StockMovementService $movementService
    ) {}

    /**
     * Proses permintaan BHP dari portal (tanpa approval)
     * Langsung potong stok.
     */
    public function createRequest(array $data): StockRequest
    {
        return DB::transaction(function () use ($data) {
            $requestNumber = StockRequest::generateNumber();
            $type = $data['identity_type'] === 'student' ? 'siswa' : 'guru';

            // 1. Buat Header
            $stockRequest = StockRequest::create([
                'request_number'    => $requestNumber,
                'requester_id'      => null,
                'requester_name'    => $data['identity_name'],
                'requester_nis_nip' => $data['identity_nis_nip'],
                'requester_phone'   => $data['identity_phone'] ?? null,
                'requester_type'    => $type,
                'source'            => 'portal',
                'purpose'           => $data['purpose'],
                'status'            => 'approved',
                'processed_at'      => now(),
            ]);

            // 2. Loop Items (Multi-item)
            foreach ($data['items'] as $itemData) {
                $item = Item::lockForUpdate()->find($itemData['item_id']);

                if (!$item) {
                    throw ValidationException::withMessages(['items' => 'Barang tidak ditemukan.']);
                }

                $quantity = (int) $itemData['quantity'];
                
                if ($item->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok \"{$item->name}\" tidak mencukupi. Tersedia: {$item->quantity}."
                    ]);
                }

                if ($item->quantity - $quantity < $item->min_stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stok \"{$item->name}\" akan di bawah minimum (min: {$item->min_stock})."
                    ]);
                }

                // Simpan detail
                StockRequestItem::create([
                    'stock_request_id'   => $stockRequest->id,
                    'item_id'            => $item->id,
                    'quantity_requested' => $quantity,
                    'quantity_approved'  => $quantity,
                ]);

                // Potong Stok
                $stockBefore = $item->quantity;
                $stockAfter = $stockBefore - $quantity;
                $item->update(['quantity' => $stockAfter]);

                // Log Movement
                $this->movementService->record(
                    item: $item,
                    type: 'out',
                    quantity: $quantity,
                    note: "Permintaan BHP via Portal oleh {$data['identity_name']}",
                    referenceType: 'stock_request',
                    referenceId: $stockRequest->id,
                    userId: 1, // Guest fallback
                    stockBefore: $stockBefore,
                    stockAfter: $stockAfter,
                );
            }

            return $stockRequest;
        });
    }
}