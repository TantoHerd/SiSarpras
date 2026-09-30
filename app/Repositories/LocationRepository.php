<?php
// app/Repositories/LocationRepository.php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationRepository
{
    /**
     * Daftar lokasi dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Location::query()
            ->with(['parent'])
            ->withCount(['items', 'children'])
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('floor', 'ILIKE', "%{$term}%")
                        ->orWhere('description', 'ILIKE', "%{$term}%");
                });
            })
            ->when(isset($filters['has_items']) && $filters['has_items'] !== '', function ($q) use ($filters) {
                if ($filters['has_items'] === '1') {
                    $q->has('items');
                } else {
                    $q->doesntHave('items');
                }
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Cari lokasi by ID
     */
    public function findById(int $id): ?Location
    {
        return Location::with(['parent', 'children'])
            ->withCount('items')
            ->find($id);
    }

    /**
     * Semua lokasi (untuk dropdown)
     */
    public function all()
    {
        return Location::orderBy('name')->get();
    }

    /**
     * Lokasi yang tidak punya items & siap jadi parent
     */
    public function availableAsParent(?int $exceptId = null)
    {
        return Location::orderBy('name')
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->get();
    }

    /**
     * Simpan lokasi baru
     */
    public function create(array $data): Location
    {
        return Location::create($data);
    }

    /**
     * Update lokasi
     */
    public function update(Location $location, array $data): Location
    {
        $location->update($data);
        return $location->fresh();
    }

    /**
     * Soft delete lokasi
     */
    public function delete(Location $location): bool
    {
        return $location->delete();
    }

    /**
     * Statistik lokasi
     */
    public function getStats(): array
    {
        return [
            'total'       => Location::count(),
            'with_items'  => Location::has('items')->count(),
            'empty'       => Location::doesntHave('items')->count(),
            'with_children' => Location::has('children')->count(),
        ];
    }

    /**
     * Cek duplikat nama
     */
    public function isNameExists(string $name, ?int $exceptId = null): bool
    {
        return Location::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Cek apakah lokasi punya items
     */
    public function hasItems(Location $location): bool
    {
        return $location->items()->exists();
    }

    /**
     * Cek apakah lokasi punya children
     */
    public function hasChildren(Location $location): bool
    {
        return $location->children()->exists();
    }

    /**
     * Hitung items per lokasi
     */
    public function countItems(Location $location): int
    {
        return $location->items()->count();
    }
}