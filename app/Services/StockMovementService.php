<?php
// app/Services/StockMovementService.php

namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;
use App\Repositories\StockMovementRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class StockMovementService
{
    public function __construct(
        protected StockMovementRepository $repository
    ) {}

    /* ═══════════════════════════════════════════════════════
       QUERY METHODS
       ═══════════════════════════════════════════════════════ */

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function forItem(int $itemId, int $limit = 20): Collection
    {
        return $this->repository->forItem($itemId, $limit);
    }

    public function find(int $id): StockMovement
    {
        return $this->repository->find($id);
    }

    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    public function getRecent(int $limit = 5): Collection
    {
        return $this->repository->recent($limit);
    }

    /* ═══════════════════════════════════════════════════════
       RECORD METHODS — CORE LOGIC
       ═══════════════════════════════════════════════════════ */

    /**
     * Catat pergerakan stok masuk
     * (misal: pembelian, penambahan manual)
     */
    public function recordIn(
        Item $item,
        int $quantity,
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): StockMovement {
        return $this->record(
            item: $item,
            type: 'in',
            quantity: $quantity,
            note: $note,
            referenceType: $referenceType,
            referenceId: $referenceId,
            userId: $userId,
        );
    }

    /**
     * Catat pergerakan stok keluar
     * (misal: permintaan BHP, penggunaan)
     */
    public function recordOut(
        Item $item,
        int $quantity,
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): StockMovement {
        return $this->record(
            item: $item,
            type: 'out',
            quantity: $quantity,
            note: $note,
            referenceType: $referenceType,
            referenceId: $referenceId,
            userId: $userId,
        );
    }

    /**
     * Catat penyesuaian stok (dari opname)
     * quantity bisa + atau -
     */
    public function recordAdjustment(
        Item $item,
        int $difference,
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): StockMovement {
        // Untuk adjustment, quantity selalu positif (abs), tapi arah dari stock_before/after
        $type = $difference >= 0 ? 'in' : 'out';
        
        // Tapi kita simpan sebagai adjustment supaya jelas
        return $this->record(
            item: $item,
            type: 'adjustment',
            quantity: abs($difference),
            note: $note ?? "Penyesuaian stok: " . ($difference > 0 ? "+{$difference}" : $difference),
            referenceType: $referenceType,
            referenceId: $referenceId,
            userId: $userId,
        );
    }

    /**
     * Core method — catat 1 pergerakan stok
     * 
     * Note: method ini TIDAK mengubah stok item.
     * Caller harus update stok item SEBELUM panggil method ini.
     */
    public function record(
        Item $item,
        string $type,
        int $quantity,
        ?string $note = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null,
        ?int $stockBefore = null,
        ?int $stockAfter = null
    ): StockMovement {
        // Ambil snapshot stok
        $stockBefore = $stockBefore ?? $item->quantity;
        
        // Hitung stok after berdasarkan tipe
        if ($stockAfter === null) {
            $stockAfter = match ($type) {
                'in'         => $stockBefore + $quantity,
                'out'        => $stockBefore - $quantity,
                'adjustment' => $stockBefore + $quantity, // bisa + atau - dari caller
                default      => $stockBefore,
            };
        }

        return $this->repository->create([
            'item_id'        => $item->id,
            'type'           => $type,
            'quantity'       => abs($quantity),
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'note'           => $note,
            'created_by'     => $userId ?? Auth::id(),
            'created_at'     => now(),
        ]);
    }

    /**
     * Bulk record — untuk multiple items (misal approve stock request)
     * 
     * @param array $records Array of ['item' => Item, 'quantity' => int, 'note' => string]
     */
    public function recordBulk(
        array $records,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): array {
        $movements = [];

        foreach ($records as $record) {
            $movements[] = $this->record(
                item: $record['item'],
                type: $type,
                quantity: $record['quantity'],
                note: $record['note'] ?? null,
                referenceType: $referenceType,
                referenceId: $referenceId,
                userId: $userId,
                stockBefore: $record['stock_before'] ?? null,
                stockAfter: $record['stock_after'] ?? null,
            );
        }

        return $movements;
    }
}