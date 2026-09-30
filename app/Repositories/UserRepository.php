<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Daftar user dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with('role')
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('email', 'ILIKE', "%{$term}%")
                        ->orWhere('nip', 'ILIKE', "%{$term}%");
                });
            })
            ->when(!empty($filters['role_id']), fn($q) => $q->where('role_id', $filters['role_id']))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Cari user by ID
     */
    public function findById(int $id): ?User
    {
        return User::with('role')->find($id);
    }

    /**
     * Cari user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Simpan user baru
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Soft delete user
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Statistik user
     */
    public function getStats(): array
    {
        return [
            'total'     => User::count(),
            'active'    => User::where('is_active', true)->count(),
            'inactive'  => User::where('is_active', false)->count(),
        ];
    }

    /**
     * Hitung user per role
     */
    public function countByRole(int $roleId): int
    {
        return User::where('role_id', $roleId)->count();
    }

    /**
     * Cek duplikat email (kecuali user sendiri)
     */
    public function isEmailExists(string $email, ?int $exceptId = null): bool
    {
        return User::where('email', $email)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Cek duplikat NIP (kecuali user sendiri)
     */
    public function isNipExists(?string $nip, ?int $exceptId = null): bool
    {
        if (empty($nip)) return false;
        
        return User::where('nip', $nip)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }
}