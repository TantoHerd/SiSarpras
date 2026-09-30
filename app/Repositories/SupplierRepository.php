<?php
// app/Repositories/SupplierRepository.php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository
{
    /**
     * Daftar supplier dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::query()
            ->withCount('items')
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('contact_person', 'ILIKE', "%{$term}%")
                        ->orWhere('phone', 'ILIKE', "%{$term}%")
                        ->orWhere('email', 'ILIKE', "%{$term}%");
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
     * Cari supplier by ID
     */
    public function findById(int $id): ?Supplier
    {
        return Supplier::withCount('items')->find($id);
    }

    /**
     * Semua supplier (untuk dropdown)
     */
    public function all()
    {
        return Supplier::orderBy('name')->get();
    }

    /**
     * Simpan supplier baru
     */
    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    /**
     * Update supplier
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    /**
     * Soft delete supplier
     */
    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }

    /**
     * Statistik supplier
     */
    public function getStats(): array
    {
        return [
            'total'      => Supplier::count(),
            'with_items' => Supplier::has('items')->count(),
            'empty'      => Supplier::doesntHave('items')->count(),
        ];
    }

    /**
     * Cek duplikat nama
     */
    public function isNameExists(string $name, ?int $exceptId = null): bool
    {
        return Supplier::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Cek duplikat email
     */
    public function isEmailExists(?string $email, ?int $exceptId = null): bool
    {
        if (empty($email)) return false;

        return Supplier::whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Cek apakah supplier punya items
     */
    public function hasItems(Supplier $supplier): bool
    {
        return $supplier->items()->exists();
    }

    /**
     * Hitung items per supplier
     */
    public function countItems(Supplier $supplier): int
    {
        return $supplier->items()->count();
    }
}