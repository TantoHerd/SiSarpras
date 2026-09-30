<?php
// app/Repositories/CategoryRepository.php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    /**
     * Daftar kategori dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('items')
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'ILIKE', "%{$term}%")
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
     * Cari kategori by ID
     */
    public function findById(int $id): ?Category
    {
        return Category::withCount('items')->find($id);
    }

    /**
     * Semua kategori (untuk dropdown)
     */
    public function all()
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Simpan kategori baru
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update kategori
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Soft delete kategori
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Statistik kategori
     */
    public function getStats(): array
    {
        return [
            'total'    => Category::count(),
            'with_items' => Category::has('items')->count(),
            'empty'    => Category::doesntHave('items')->count(),
        ];
    }

    /**
     * Cek duplikat nama
     */
    public function isNameExists(string $name, ?int $exceptId = null): bool
    {
        return Category::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Cek apakah kategori punya items
     */
    public function hasItems(Category $category): bool
    {
        return $category->items()->exists();
    }

    /**
     * Hitung items per kategori
     */
    public function countItems(Category $category): int
    {
        return $category->items()->count();
    }
}