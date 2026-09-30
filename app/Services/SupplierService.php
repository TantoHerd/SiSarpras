<?php
// app/Services/SupplierService.php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierService
{
    public function __construct(
        protected SupplierRepository $repository
    ) {}

    /**
     * Daftar supplier
     */
    public function getSuppliers(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Semua supplier untuk dropdown
     */
    public function getAllForDropdown()
    {
        return $this->repository->all();
    }

    /**
     * Detail supplier
     */
    public function getSupplier(int $id): ?Supplier
    {
        return $this->repository->findById($id);
    }

    /**
     * Buat supplier baru
     */
    public function createSupplier(array $data): Supplier
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'])) {
            throw ValidationException::withMessages([
                'name' => 'Nama supplier sudah ada. Gunakan nama lain.',
            ]);
        }

        // Validasi duplikat email (kalau diisi)
        if (!empty($data['email']) && $this->repository->isEmailExists($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan supplier lain.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            return $this->repository->create([
                'name'           => $data['name'],
                'phone'          => $data['phone'] ?? null,
                'email'          => $data['email'] ?? null,
                'address'        => $data['address'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
            ]);
        });
    }

    /**
     * Update supplier
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'], $supplier->id)) {
            throw ValidationException::withMessages([
                'name' => 'Nama supplier sudah ada. Gunakan nama lain.',
            ]);
        }

        // Validasi duplikat email
        if (!empty($data['email']) && $this->repository->isEmailExists($data['email'], $supplier->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan supplier lain.',
            ]);
        }

        return DB::transaction(function () use ($supplier, $data) {
            return $this->repository->update($supplier, [
                'name'           => $data['name'],
                'phone'          => $data['phone'] ?? null,
                'email'          => $data['email'] ?? null,
                'address'        => $data['address'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
            ]);
        });
    }

    /**
     * Hapus supplier (soft delete)
     * 
     * Cek apakah supplier masih dipakai barang
     */
    public function deleteSupplier(Supplier $supplier): bool
    {
        if ($this->repository->hasItems($supplier)) {
            $count = $this->repository->countItems($supplier);
            throw ValidationException::withMessages([
                'supplier' => "Tidak dapat menghapus supplier ini karena masih terkait dengan {$count} barang. Ubah supplier barang terlebih dahulu.",
            ]);
        }

        return DB::transaction(function () use ($supplier) {
            return $this->repository->delete($supplier);
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