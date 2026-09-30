<?php
// app/Services/CategoryService.php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $repository
    ) {}

    /**
     * Daftar kategori
     */
    public function getCategories(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Semua kategori untuk dropdown
     */
    public function getAllForDropdown()
    {
        return $this->repository->all();
    }

    /**
     * Detail kategori
     */
    public function getCategory(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    /**
     * Buat kategori baru
     */
    public function createCategory(array $data): Category
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'])) {
            throw ValidationException::withMessages([
                'name' => 'Nama kategori sudah ada. Gunakan nama lain.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            return $this->repository->create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'icon'        => $data['icon'] ?? 'fa-boxes',
            ]);
        });
    }

    /**
     * Update kategori
     */
    public function updateCategory(Category $category, array $data): Category
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'], $category->id)) {
            throw ValidationException::withMessages([
                'name' => 'Nama kategori sudah ada. Gunakan nama lain.',
            ]);
        }

        return DB::transaction(function () use ($category, $data) {
            return $this->repository->update($category, [
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'icon'        => $data['icon'] ?? 'fa-boxes',
            ]);
        });
    }

    /**
     * Hapus kategori (soft delete)
     * 
     * Cek dulu apakah kategori masih dipakai barang
     */
    public function deleteCategory(Category $category): bool
    {
        // Cek apakah kategori punya items
        if ($this->repository->hasItems($category)) {
            $count = $this->repository->countItems($category);
            throw ValidationException::withMessages([
                'category' => "Tidak dapat menghapus kategori ini karena masih digunakan oleh {$count} barang. Pindahkan barang ke kategori lain terlebih dahulu.",
            ]);
        }

        return DB::transaction(function () use ($category) {
            return $this->repository->delete($category);
        });
    }

    /**
     * Statistik
     */
    public function getStats(): array
    {
        return $this->repository->getStats();
    }
}