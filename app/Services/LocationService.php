<?php
// app/Services/LocationService.php

namespace App\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LocationService
{
    public function __construct(
        protected LocationRepository $repository
    ) {}

    /**
     * Daftar lokasi
     */
    public function getLocations(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Semua lokasi untuk dropdown
     */
    public function getAllForDropdown()
    {
        return $this->repository->all();
    }

    /**
     * Lokasi yang bisa jadi parent (kecuali diri sendiri)
     */
    public function getAvailableParents(?int $exceptId = null)
    {
        return $this->repository->availableAsParent($exceptId);
    }

    /**
     * Detail lokasi
     */
    public function getLocation(int $id): ?Location
    {
        return $this->repository->findById($id);
    }

    /**
     * Buat lokasi baru
     */
    public function createLocation(array $data): Location
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'])) {
            throw ValidationException::withMessages([
                'name' => 'Nama lokasi sudah ada. Gunakan nama lain.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            return $this->repository->create([
                'name'        => $data['name'],
                'floor'       => $data['floor'] ?? null,
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
            ]);
        });
    }

    /**
     * Update lokasi
     */
    public function updateLocation(Location $location, array $data): Location
    {
        // Validasi duplikat nama
        if ($this->repository->isNameExists($data['name'], $location->id)) {
            throw ValidationException::withMessages([
                'name' => 'Nama lokasi sudah ada. Gunakan nama lain.',
            ]);
        }

        // Cegah circular reference (parent = diri sendiri)
        if (isset($data['parent_id']) && $data['parent_id'] == $location->id) {
            throw ValidationException::withMessages([
                'parent_id' => 'Lokasi tidak dapat menjadi parent untuk dirinya sendiri.',
            ]);
        }

        return DB::transaction(function () use ($location, $data) {
            return $this->repository->update($location, [
                'name'        => $data['name'],
                'floor'       => $data['floor'] ?? null,
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
            ]);
        });
    }

    /**
     * Hapus lokasi (soft delete)
     * 
     * Cek apakah lokasi masih dipakai barang
     */
    public function deleteLocation(Location $location): bool
    {
        // Cek apakah lokasi punya items
        if ($this->repository->hasItems($location)) {
            $count = $this->repository->countItems($location);
            throw ValidationException::withMessages([
                'location' => "Tidak dapat menghapus lokasi ini karena masih digunakan oleh {$count} barang. Pindahkan barang ke lokasi lain terlebih dahulu.",
            ]);
        }

        return DB::transaction(function () use ($location) {
            return $this->repository->delete($location);
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