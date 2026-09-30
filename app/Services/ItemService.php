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
            // Generate kode otomatis
            $data['code'] = $this->generateCode($data['category_id']);

            // Upload gambar jika ada
            if ($image) {
                $data['image'] = $this->uploadImage($image);
            }

            // Set created_by
            $data['created_by'] = Auth::id();

            // Simpan
            $item = $this->repository->create($data);

            // Catat history
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

            // Deteksi remove_image dari request (bisa string "1" atau boolean true)
            $removeImage = request()->input('remove_image') == '1' 
                        || request()->input('remove_image') === true;

            Log::info('Update item', [
                'item_id' => $item->id,
                'has_image_upload' => $image ? true : false,
                'remove_image_flag' => $removeImage,
                'old_image' => $item->image,
            ]);

            // CASE 1: Upload gambar baru
            if ($image) {
                // Hapus gambar lama
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }
                $data['image'] = $this->uploadImage($image);
            } 
            // CASE 2: Hapus gambar tanpa upload baru
            elseif ($removeImage) {
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }
                $data['image'] = null;
            }
            // CASE 3: Tidak ada perubahan gambar — biarkan seperti semula
            // (tidak perlu set $data['image'])

            $data['updated_by'] = Auth::id();

            $updated = $this->repository->update($item, $data);

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