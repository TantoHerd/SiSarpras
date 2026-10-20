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
            $item = Item::lockForUpdate()->find($data['item_id']);

            if (!$item) {
                throw ValidationException::withMessages(['item_id' => 'Barang tidak ditemukan.']);
            }

            $quantity = (int) $data['quantity'];
            
            if ($item->quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak mencukupi. Tersedia: {$item->quantity}."
                ]);
            }

            if ($item->quantity - $quantity < $item->min_stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok akan berada di bawah batas minimum (min: {$item->min_stock})."
                ]);
            }

            $requestNumber = StockRequest::generateNumber();

            // Tentukan requester_type yang valid
            $type = $data['identity_type'] === 'student' ? 'siswa' : 'guru';
            
            // Jika database Anda hanya menerima 'guru', 'staf', 'petugas' (tanpa 'siswa')
            // dan Anda ingin menyimpannya sebagai 'guru' atau 'staf', sesuaikan:
            if ($data['identity_type'] === 'student') {
                $requesterType = 'siswa'; // Jika constraint DB menerima 'siswa'
            } else {
                $requesterType = 'guru'; // Atau 'staf' jika ada di DB
            }

            $stockRequest = StockRequest::create([
                'request_number'    => $requestNumber,
                'requester_id'      => null,
                'requester_name'    => $data['identity_name'],
                'requester_nis_nip' => $data['identity_nis_nip'],
                'requester_phone'   => $data['identity_phone'] ?? null,
                
                // ← PERBAIKAN: Gunakan tipe yang sesuai, jangan hardcode 'guru'
                'requester_type'    => $data['identity_type'] === 'student' ? 'siswa' : 'guru', 
                
                'source'            => 'portal',
                'purpose'           => $data['purpose'],
                'status'            => 'approved',
                'processed_at'      => now(),
            ]);

            StockRequestItem::create([
                'stock_request_id'   => $stockRequest->id,
                'item_id'            => $item->id,
                'quantity_requested' => $quantity,
                'quantity_approved'  => $quantity,
            ]);

            $stockBefore = $item->quantity;
            $stockAfter = $stockBefore - $quantity;
            
            $item->update(['quantity' => $stockAfter]);

            // Simpan log movement. Pastikan userId null aman.
            $this->movementService->record(
                item: $item,
                type: 'out',
                quantity: $quantity,
                note: "Permintaan BHP via Portal oleh {$data['identity_name']}",
                referenceType: 'stock_request',
                referenceId: $stockRequest->id,
                userId: 1, // ← Trik: Isi dengan ID Admin (1) atau NULL jika DB mengizinkan
                stockBefore: $stockBefore,
                stockAfter: $stockAfter,
            );

            return $stockRequest;
        });
    }
}