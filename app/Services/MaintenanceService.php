<?php
// app/Services/MaintenanceService.php

namespace App\Services;

use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\Maintenance;
use App\Repositories\ItemRepository;
use App\Repositories\MaintenanceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceService
{
    public function __construct(
        protected MaintenanceRepository $repository,
        protected ItemRepository $itemRepository
    ) {}

    /**
     * Daftar maintenance
     */
    public function getMaintenances(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Detail maintenance
     */
    public function getMaintenance(int $id): ?Maintenance
    {
        return $this->repository->findById($id);
    }

    /**
     * Buat maintenance baru
     */
    public function createMaintenance(array $data): Maintenance
    {
        $item = Item::find($data['item_id']);
        if (!$item) {
            throw ValidationException::withMessages([
                'item_id' => 'Barang tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($data, $item) {
            $oldItemValues = $item->toArray();

            // Tentukan status maintenance
            $status = 'completed';
            
            // Kalau perbaikan & user pilih "belum selesai" → status "in_progress"
            if ($data['type'] === 'perbaikan' && !empty($data['is_completed']) === false) {
                $status = 'in_progress';
            }
            
            // Kalau user set "langsung selesai" → status completed
            if ($data['type'] === 'perbaikan' && !empty($data['is_completed'])) {
                $status = 'completed';
            }

            // Simpan maintenance
            $maintenance = $this->repository->create([
                'item_id'               => $data['item_id'],
                'maintenance_date'      => $data['maintenance_date'] ?? now(),
                'type'                  => $data['type'],
                'status'                => $status,
                'cost'                  => $data['cost'] ?? 0,
                'description'           => $data['description'] ?? null,
                'technician'            => $data['technician'] ?? null,
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                'created_by'            => Auth::id(),
                'completed_at'          => $status === 'completed' ? now() : null,
                'completed_by'          => $status === 'completed' ? Auth::id() : null,
            ]);

            // ============ LOGIKA AUTO-UPDATE BARANG ============
            $itemUpdate = [];

            if ($data['type'] === 'perbaikan') {
                if ($status === 'in_progress') {
                    // Perbaikan baru dimulai → barang status "perbaikan"
                    $itemUpdate['status'] = ItemStatusEnum::PERBAIKAN->value;
                    // Kondisi TIDAK diubah (tetap rusak)
                } else {
                    // Perbaikan langsung selesai
                    $itemUpdate['condition'] = ItemConditionEnum::BAIK->value;
                    
                    if ($item->status !== ItemStatusEnum::DIPINJAM) {
                        $itemUpdate['status'] = ItemStatusEnum::TERSEDIA->value;
                    }
                }
            }
            // Rutin: tidak ada perubahan

            // Update item jika ada perubahan
            if (!empty($itemUpdate)) {
                $itemUpdate['updated_by'] = Auth::id();
                $this->itemRepository->update($item, $itemUpdate);

                // Log ke item_histories
                ItemHistory::create([
                    'item_id'    => $item->id,
                    'user_id'    => Auth::id(),
                    'action'     => 'repaired',
                    'old_value'  => $oldItemValues,
                    'new_value'  => array_merge($oldItemValues, $itemUpdate, [
                        'maintenance_id'   => $maintenance->id,
                        'maintenance_type' => $data['type'],
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return $maintenance;
        });
    }

    /**
     * Tandai perbaikan sebagai selesai
     */
    public function markAsCompleted(Maintenance $maintenance, array $data = []): Maintenance
    {
        // Cek apakah masih in_progress
        if (!$maintenance->isInProgress()) {
            throw ValidationException::withMessages([
                'maintenance' => 'Perawatan ini sudah selesai sebelumnya.',
            ]);
        }

        // Cek hanya untuk perbaikan
        if ($maintenance->type !== 'perbaikan') {
            throw ValidationException::withMessages([
                'maintenance' => 'Hanya perbaikan yang bisa ditandai selesai.',
            ]);
        }

        return DB::transaction(function () use ($maintenance, $data) {
            $item = $maintenance->item;
            $oldItemValues = $item->toArray();

            // Update maintenance
            $updateData = [
                'status'          => 'completed',
                'completed_at'    => now(),
                'completed_by'    => Auth::id(),
                'completion_note' => $data['completion_note'] ?? null,
            ];

            // Update biaya kalau ada perubahan
            if (isset($data['final_cost']) && $data['final_cost'] !== null && $data['final_cost'] !== '') {
                $updateData['cost'] = $data['final_cost'];
            }

            $updated = $this->repository->update($maintenance, $updateData);

            // Update item: kondisi "baik", status "tersedia"
            $itemUpdate = [
                'condition' => ItemConditionEnum::BAIK->value,
            ];

            if ($item->status !== ItemStatusEnum::DIPINJAM) {
                $itemUpdate['status'] = ItemStatusEnum::TERSEDIA->value;
            }

            $itemUpdate['updated_by'] = Auth::id();
            $this->itemRepository->update($item, $itemUpdate);

            // Log ke item_histories
            ItemHistory::create([
                'item_id'    => $item->id,
                'user_id'    => Auth::id(),
                'action'     => 'repaired',
                'old_value'  => $oldItemValues,
                'new_value'  => array_merge($oldItemValues, $itemUpdate, [
                    'maintenance_id'   => $maintenance->id,
                    'maintenance_type' => 'perbaikan',
                    'completion_note'  => $data['completion_note'] ?? null,
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $updated;
        });
    }

    /**
     * Ambil maintenance yang sedang dalam proses
     */
    public function getInProgress(int $limit = 10)
    {
        return Maintenance::with(['item', 'item.location', 'creator'])
            ->inProgress()
            ->orderBy('maintenance_date')
            ->take($limit)
            ->get();
    }

    /**
     * Update maintenance
     */
    public function updateMaintenance(Maintenance $maintenance, array $data): Maintenance
    {
        return DB::transaction(function () use ($maintenance, $data) {
            return $this->repository->update($maintenance, [
                'maintenance_date'      => $data['maintenance_date'] ?? $maintenance->maintenance_date,
                'type'                  => $data['type'],
                'cost'                  => $data['cost'] ?? 0,
                'description'           => $data['description'] ?? null,
                'technician'            => $data['technician'] ?? null,
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
            ]);
        });
    }

    /**
     * Hapus maintenance (soft delete)
     */
    public function deleteMaintenance(Maintenance $maintenance): bool
    {
        return DB::transaction(function () use ($maintenance) {
            return $this->repository->delete($maintenance);
        });
    }

    /**
     * Ambil maintenance per item
     */
    public function getByItem(int $itemId, int $limit = 10)
    {
        return $this->repository->getByItem($itemId, $limit);
    }

    /**
     * Ambil jadwal maintenance mendekati jatuh tempo
     */
    public function getUpcoming(int $days = 7, int $limit = 5)
    {
        return $this->repository->getUpcoming($days, $limit);
    }

    /**
     * Statistik
     */
    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    /**
     * Statistik per item
     */
    public function getStatsByItem(int $itemId): array
    {
        return $this->repository->getStatsByItem($itemId);
    }
}