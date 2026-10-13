<?php
// app/Services/StockOpnameService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Location;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Repositories\StockOpnameRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOpnameService
{
    public function __construct(
        protected StockOpnameRepository $repository,
        protected StockMovementService $movementService
    ) {}

    /* ═══════════════════════════════════════════════════════
       QUERY METHODS
       ═══════════════════════════════════════════════════════ */

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id): StockOpname
    {
        return $this->repository->find($id);
    }

    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    /* ═══════════════════════════════════════════════════════
       BUSINESS LOGIC
       ═══════════════════════════════════════════════════════ */

    /**
     * Buka sesi opname baru untuk 1 lokasi
     */
    public function openSession(int $locationId, ?string $note = null): StockOpname
    {
        // Cek lokasi ada
        $location = Location::find($locationId);
        if (!$location) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi tidak ditemukan.',
            ]);
        }

        // Cek apakah sudah ada draft untuk lokasi ini
        if ($this->repository->hasDraftForLocation($locationId)) {
            throw ValidationException::withMessages([
                'location_id' => "Sudah ada sesi opname draft untuk lokasi ini. Selesaikan atau batalkan dulu.",
            ]);
        }

        return DB::transaction(function () use ($locationId, $note) {
            // Buat sesi
            $opname = $this->repository->create([
                'opname_number' => StockOpname::generateNumber(),
                'location_id'   => $locationId,
                'status'        => 'draft',
                'started_by'    => Auth::id(),
                'started_at'    => now(),
                'note'          => $note,
            ]);

            // Snapshot items di lokasi ini
            $items = Item::where('location_id', $locationId)
                ->consumable()
                ->get();

            foreach ($items as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'item_id'         => $item->id,
                    'system_stock'    => $item->quantity,
                    'physical_stock'  => $item->quantity, // default = system
                    'difference'      => 0,
                ]);
            }

            return $opname->fresh(['items.item', 'location']);
        });
    }

    /**
     * Update input fisik per item (dari form)
     */
    public function updatePhysicalStock(StockOpname $opname, array $items): void
    {
        if (!$opname->isDraft()) {
            throw ValidationException::withMessages([
                'status' => 'Sesi opname ini sudah selesai atau dibatalkan.',
            ]);
        }

        DB::transaction(function () use ($opname, $items) {
            foreach ($items as $itemId => $data) {
                $opnameItem = StockOpnameItem::where('stock_opname_id', $opname->id)
                    ->where('id', $itemId)
                    ->first();

                if (!$opnameItem) continue;

                $physical = (int) ($data['physical_stock'] ?? $opnameItem->system_stock);
                $diff = $physical - $opnameItem->system_stock;

                $opnameItem->update([
                    'physical_stock' => $physical,
                    'difference'     => $diff,
                    'note'           => $data['note'] ?? null,
                ]);
            }
        });
    }

    /**
     * Submit & koreksi stok
     */
    public function completeSession(StockOpname $opname): StockOpname
    {
        if (!$opname->isDraft()) {
            throw ValidationException::withMessages([
                'status' => 'Sesi opname ini sudah selesai atau dibatalkan.',
            ]);
        }

        return DB::transaction(function () use ($opname) {
            // Ambil items dengan difference != 0
            $itemsToAdjust = $opname->items()
                ->where('difference', '!=', 0)
                ->with('item')
                ->get();

            // Koreksi stok
            foreach ($itemsToAdjust as $opnameItem) {
                $item = Item::lockForUpdate()->find($opnameItem->item_id);
                
                if (!$item) continue;

                $stockBefore = $item->quantity;
                $stockAfter  = $opnameItem->physical_stock;

                // Update stok item
                $item->update(['quantity' => $stockAfter]);

                // Log movement
                $this->movementService->record(
                    item: $item,
                    type: 'adjustment',
                    quantity: abs($opnameItem->difference),
                    note: "Koreksi opname #{$opname->opname_number}: {$stockBefore} → {$stockAfter}",
                    referenceType: 'stock_opname',
                    referenceId: $opname->id,
                    userId: Auth::id(),
                    stockBefore: $stockBefore,
                    stockAfter: $stockAfter,
                );
            }

            // Update status sesi
            return $this->repository->update($opname, [
                'status'       => 'completed',
                'completed_by' => Auth::id(),
                'completed_at' => now(),
            ]);
        });
    }

    /**
     * Batalkan sesi
     */
    public function cancelSession(StockOpname $opname): StockOpname
    {
        if (!$opname->isDraft()) {
            throw ValidationException::withMessages([
                'status' => 'Sesi opname ini sudah selesai atau dibatalkan.',
            ]);
        }

        return $this->repository->update($opname, [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Hapus sesi (hanya draft)
     */
    public function deleteSession(StockOpname $opname): void
    {
        if (!$opname->isDraft()) {
            throw ValidationException::withMessages([
                'status' => 'Hanya sesi draft yang bisa dihapus.',
            ]);
        }

        $opname->items()->delete();
        $this->repository->delete($opname);
    }
}