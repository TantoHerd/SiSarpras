<?php
// app/Repositories/ItemRepository.php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository
{
    /**
     * Ambil semua item dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Item::query()
            ->with(['category', 'location', 'supplier', 'fundingSource'])
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $term = $filters['search'];
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('code', 'ILIKE', "%{$term}%")
                        ->orWhere('brand', 'ILIKE', "%{$term}%")
                        ->orWhere('serial_number', 'ILIKE', "%{$term}%");
                });
            })
            ->when(!empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(!empty($filters['location_id']), fn($q) => $q->where('location_id', $filters['location_id']))
            ->when($filters['funding_source_id'] ?? null, function ($q) use ($filters) {
                $q->where('funding_source_id', $filters['funding_source_id']);
            })
            
            // ← BARU: filter tracking mode (dengan fallback ke kategori)
            ->when(!empty($filters['tracking_mode']), function ($q) use ($filters) {
                $mode = $filters['tracking_mode'];
                
                $q->where(function ($sub) use ($mode) {
                    // Override di item
                    $sub->where('tracking_mode', $mode)
                        // ATAU fallback ke kategori
                        ->orWhere(function ($s) use ($mode) {
                            $s->whereNull('tracking_mode')
                            ->whereHas('category', fn($c) => $c->where('default_tracking_mode', $mode));
                        });
                });
            })
            
            ->when(!empty($filters['condition']), fn($q) => $q->where('condition', $filters['condition']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Cari item berdasarkan ID
     */
    public function findById(int $id): ?Item
    {
        return Item::with(['category', 'location', 'supplier', 'fundingSource', 'creator'])
            ->find($id);
    }

    /**
     * Simpan item baru
     */
    public function create(array $data): Item
    {
        return Item::create($data);
    }

    /**
     * Update item
     */
    public function update(Item $item, array $data): Item
    {
        // Pastikan image null benar-benar ter-set
        // (Ini penting karena fill() kadang mengabaikan null jika field tidak fillable)
        $item->fill($data);
        
        // Force set image jika ada di data (termasuk null)
        if (array_key_exists('image', $data)) {
            $item->image = $data['image'];
        }
        
        $item->save();
        
        return $item->fresh();
    }

    /**
     * Hapus item (soft delete)
     */
    public function delete(Item $item): bool
    {
        return $item->delete();
    }

    /**
     * Restore item yang dihapus
     */
    public function restore(int $id): bool
    {
        return Item::withTrashed()->findOrFail($id)->restore();
    }

    /**
     * Statistik untuk dashboard
     */
    public function getStats(): array
    {
        return [
            'total'     => Item::count(),
            'available' => Item::where('status', 'tersedia')->count(),
            'borrowed'  => Item::where('status', 'dipinjam')->count(),
            'damaged'   => Item::where('condition', 'rusak_berat')->count(),
        ];
    }
}