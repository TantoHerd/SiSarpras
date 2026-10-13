<?php
// app/Services/StockRequestService.php

namespace App\Services;

use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Repositories\StockRequestRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockRequestService
{
    public function __construct(
        protected StockRequestRepository $repository,
        protected StockMovementService $movementService,
        protected SettingService $settingService
    ) {}

    /* ═══════════════════════════════════════════════════════
       QUERY METHODS
       ═══════════════════════════════════════════════════════ */

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id): StockRequest
    {
        return $this->repository->find($id);
    }

    public function forUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->forUser($userId, $perPage);
    }

    public function countPending(): int
    {
        return $this->repository->countPending();
    }

    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    /* ═══════════════════════════════════════════════════════
       BUSINESS LOGIC
       ═══════════════════════════════════════════════════════ */

    /**
     * Buat permintaan BHP baru
     * 
     * @param array $data [
     *   'purpose' => string,
     *   'requester_type' => 'guru'|'staf'|'petugas',
     *   'items' => [
     *     ['item_id' => int, 'quantity' => int, 'note' => ?string],
     *     ...
     *   ]
     * ]
     */
    public function createRequest(array $data): StockRequest
    {
        return DB::transaction(function () use ($data) {
            // Validasi max items
            $maxItems = $this->settingService->getTyped('stock_request_max_items', 5);
            if (count($data['items']) > $maxItems) {
                throw ValidationException::withMessages([
                    'items' => "Maksimal {$maxItems} jenis barang per permintaan.",
                ]);
            }

            // Validasi max qty
            $maxQty = $this->settingService->getTyped('stock_request_max_qty', 10);
            foreach ($data['items'] as $item) {
                if ($item['quantity'] > $maxQty) {
                    throw ValidationException::withMessages([
                        'items' => "Maksimal {$maxQty} unit per barang.",
                    ]);
                }
            }

            // Generate nomor
            $requestNumber = StockRequest::generateNumber();

            // Buat header
            $stockRequest = $this->repository->create([
                'request_number' => $requestNumber,
                'requester_id'   => Auth::id(),
                'requester_type' => $data['requester_type'] ?? 'guru',
                'purpose'        => $data['purpose'],
                'status'         => 'pending',
            ]);

            // Buat items
            foreach ($data['items'] as $item) {
                // Validasi item ada & consumable
                $itemModel = Item::find($item['item_id']);
                
                if (!$itemModel) {
                    throw ValidationException::withMessages([
                        'items' => "Barang dengan ID {$item['item_id']} tidak ditemukan.",
                    ]);
                }

                if (!$itemModel->isConsumable()) {
                    throw ValidationException::withMessages([
                        'items' => "Barang \"{$itemModel->name}\" bukan barang habis pakai.",
                    ]);
                }

                StockRequestItem::create([
                    'stock_request_id'   => $stockRequest->id,
                    'item_id'            => $item['item_id'],
                    'quantity_requested' => $item['quantity'],
                    'note'               => $item['note'] ?? null,
                ]);
            }

            return $stockRequest->fresh(['items.item']);
        });
    }

    /**
     * Approve permintaan → decrement stok + log movement
     */
    public function approveRequest(StockRequest $stockRequest, int $processedBy): StockRequest
    {
        if (!$stockRequest->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'Permintaan ini sudah diproses sebelumnya.',
            ]);
        }

        return DB::transaction(function () use ($stockRequest, $processedBy) {
            // Lock dan validasi stok semua item dulu
            $itemsToProcess = [];

            foreach ($stockRequest->items as $reqItem) {
                $item = Item::lockForUpdate()->find($reqItem->item_id);
                
                if (!$item) {
                    throw ValidationException::withMessages([
                        'items' => "Barang \"{$reqItem->item->name}\" tidak ditemukan.",
                    ]);
                }

                if ($item->quantity < $reqItem->quantity_requested) {
                    throw ValidationException::withMessages([
                        'items' => "Stok \"{$item->name}\" tidak mencukupi. Tersedia: {$item->quantity}, diminta: {$reqItem->quantity_requested}.",
                    ]);
                }

                $itemsToProcess[] = [
                    'item'          => $item,
                    'quantity'      => $reqItem->quantity_requested,
                    'stock_before'  => $item->quantity,
                    'stock_after'   => $item->quantity - $reqItem->quantity_requested,
                ];
            }

            // Update stok semua item
            foreach ($itemsToProcess as $process) {
                $process['item']->update([
                    'quantity' => $process['stock_after'],
                ]);
            }

            // Log movements
            $this->movementService->recordBulk(
                records: array_map(fn($p) => [
                    'item'         => $p['item'],
                    'quantity'     => $p['quantity'],
                    'stock_before' => $p['stock_before'],
                    'stock_after'  => $p['stock_after'],
                    'note'         => "Permintaan BHP #{$stockRequest->request_number}",
                ], $itemsToProcess),
                type: 'out',
                referenceType: 'stock_request',
                referenceId: $stockRequest->id,
                userId: $processedBy,
            );

            // Update stock request status
            return $this->repository->update($stockRequest, [
                'status'       => 'approved',
                'processed_by' => $processedBy,
                'processed_at' => now(),
            ]);
        });
    }

    /**
     * Reject permintaan
     */
    public function rejectRequest(StockRequest $stockRequest, int $processedBy, string $reason): StockRequest
    {
        if (!$stockRequest->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'Permintaan ini sudah diproses sebelumnya.',
            ]);
        }

        return $this->repository->update($stockRequest, [
            'status'           => 'rejected',
            'processed_by'     => $processedBy,
            'processed_at'     => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Delete permintaan (hanya yang pending)
     */
    public function deleteRequest(StockRequest $stockRequest): void
    {
        if (!$stockRequest->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'Hanya permintaan pending yang bisa dihapus.',
            ]);
        }

        // Delete items dulu (cascade)
        $stockRequest->items()->delete();
        $this->repository->delete($stockRequest);
    }
}