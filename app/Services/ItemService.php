<?php
// app/Services/ItemService.php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemHistory;
use App\Repositories\ItemRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;

class ItemService
{
    public function __construct(
        protected ItemRepository $repository
    ) {}

    /**
     * Ambil daftar item (dengan filter)
     */
    public function getItems(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Ambil detail item
     */
    public function getItem(int $id): ?Item
    {
        return $this->repository->findById($id);
    }

    /**
     * Generate kode barang otomatis
     * Format: KATEGORI-TAHUN-BULAN-URUTAN
     * Contoh: ELEK-2026-09-001
     */
    public function generateCode(int $categoryId): string
    {
        $category = \App\Models\Category::findOrFail($categoryId);
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 4));
        
        $year = now()->format('Y');
        $month = now()->format('m');
        
        // Cari nomor urut terakhir di bulan ini
        $lastItem = Item::withTrashed()
            ->where('code', 'LIKE', "{$prefix}-{$year}-{$month}-%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->code, -3);
            $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $sequence = '001';
        }

        return "{$prefix}-{$year}-{$month}-{$sequence}";
    }

    /**
     * Buat item baru
     */
    public function createItem(array $data, ?UploadedFile $image = null): Item
    {
        return DB::transaction(function () use ($data, $image) {
            $data['code'] = $this->generateCode($data['category_id']);

            if ($image) {
                $data['image'] = $this->uploadImage($image);
            }

            // ============ AUTO-SET STATUS BERDASARKAN KONDISI ============
            $condition = $data['condition'] ?? 'baik';
            
            if ($condition === 'rusak_berat') {
                $data['status'] = ItemStatusEnum::TIDAK_AKTIF->value;
            } else {
                // Default: tersedia
                $data['status'] = ItemStatusEnum::TERSEDIA->value;
            }

            $data['created_by'] = Auth::id();

            $item = $this->repository->create($data);

            $this->logHistory($item, 'created', null, $item->toArray());

            return $item;
        });
    }

    /**
     * Update item
     */
    public function updateItem(Item $item, array $data, ?UploadedFile $image = null): Item
    {
        return DB::transaction(function () use ($item, $data, $image) {
            $oldValues = $item->toArray();

            // Cek apakah user minta hapus gambar
            $removeImage = request()->input('remove_image') === '1';

            // Upload gambar baru jika ada
            if ($image) {
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }
                $data['image'] = $this->uploadImage($image);
            } 
            elseif ($removeImage) {
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }
                $data['image'] = null;
            }

            // ============ AUTO-UPDATE STATUS BERDASARKAN KONDISI ============
            // Jangan override status manual kalau user memang mengisi status
            if (isset($data['condition']) && !isset($data['status'])) {
                $condition = $data['condition'];
                
                // Kalau kondisi diubah jadi "Rusak Berat" → status otomatis "Tidak Aktif"
                if ($condition === 'rusak_berat' || $condition === ItemConditionEnum::RUSAK_BERAT->value) {
                    // Hanya update kalau status bukan "Dipinjam" (biar tidak konflik)
                    if ($item->status !== ItemStatusEnum::DIPINJAM) {
                        $data['status'] = ItemStatusEnum::TIDAK_AKTIF->value;
                    }
                }
                // Kalau kondisi diubah jadi "Baik" atau "Rusak Ringan" dan statusnya "Tidak Aktif" → jadi "Tersedia"
                elseif (in_array($condition, ['baik', 'rusak_ringan']) 
                        || in_array($condition, [ItemConditionEnum::BAIK->value, ItemConditionEnum::RUSAK_RINGAN->value])) {
                    if ($item->status === ItemStatusEnum::TIDAK_AKTIF) {
                        $data['status'] = ItemStatusEnum::TERSEDIA->value;
                    }
                }
            }

            $data['updated_by'] = Auth::id();

            $updated = $this->repository->update($item, $data);

            // Catat history
            $this->logHistory($updated, 'updated', $oldValues, $updated->toArray());

            return $updated;
        });
    }

    /**
     * Hapus item (soft delete)
     */
    public function deleteItem(Item $item): bool
    {
        return DB::transaction(function () use ($item) {
            $oldValues = $item->toArray();
            $result = $this->repository->delete($item);
            
            $this->logHistory($item, 'deleted', $oldValues, null);
            
            return $result;
        });
    }

    /**
     * Upload gambar
     */
    protected function uploadImage(UploadedFile $image): string
    {
        return $image->store('items', 'public');
    }

    /**
     * Catat riwayat perubahan
     */
    protected function logHistory(Item $item, string $action, ?array $old, ?array $new): void
    {
        ItemHistory::create([
            'item_id'    => $item->id,
            'user_id'    => Auth::id(),
            'action'     => $action,
            'old_value'  => $old,
            'new_value'  => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}