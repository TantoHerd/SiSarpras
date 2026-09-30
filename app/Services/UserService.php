<?php
// app/Services/UserService.php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Daftar user
     */
    public function getUsers(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Detail user
     */
    public function getUser(int $id): ?User
    {
        return $this->repository->findById($id);
    }

    /**
     * Buat user baru dengan password auto-generate
     * 
     * @return array{user: User, plain_password: string}
     */
    public function createUser(array $data): array
    {
        // Validasi duplikat
        if ($this->repository->isEmailExists($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan user lain.',
            ]);
        }

        if (!empty($data['nip']) && $this->repository->isNipExists($data['nip'])) {
            throw ValidationException::withMessages([
                'nip' => 'NIP sudah digunakan user lain.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            // Generate password
            $plainPassword = $this->generatePassword($data['name']);

            $user = $this->repository->create([
                'role_id'           => $data['role_id'],
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make($plainPassword),
                'nip'               => $data['nip'] ?? null,
                'phone'             => $data['phone'] ?? null,
                'is_active'         => $data['is_active'] ?? true,
                'email_verified_at' => now(), // Auto-verified karena sistem internal
            ]);

            return [
                'user' => $user,
                'plain_password' => $plainPassword,
            ];
        });
    }

    /**
     * Update user (tanpa password)
     */
    public function updateUser(User $user, array $data): User
    {
        // Validasi duplikat
        if (isset($data['email']) && $this->repository->isEmailExists($data['email'], $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan user lain.',
            ]);
        }

        if (!empty($data['nip']) && $this->repository->isNipExists($data['nip'], $user->id)) {
            throw ValidationException::withMessages([
                'nip' => 'NIP sudah digunakan user lain.',
            ]);
        }

        return DB::transaction(function () use ($user, $data) {
            // Hapus password dari data (tidak boleh diubah via sini)
            unset($data['password']);

            return $this->repository->update($user, $data);
        });
    }

    /**
     * Reset password user dengan generate baru
     * 
     * @return array{user: User, plain_password: string}
     */
    public function resetPassword(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $plainPassword = $this->generatePassword($user->name);

            $updated = $this->repository->update($user, [
                'password' => Hash::make($plainPassword),
            ]);

            return [
                'user' => $updated,
                'plain_password' => $plainPassword,
            ];
        });
    }

    /**
     * Toggle status aktif user
     */
    public function toggleActive(User $user): User
    {
        return $this->repository->update($user, [
            'is_active' => !$user->is_active,
        ]);
    }

    /**
     * Hapus user (soft delete)
     */
    public function deleteUser(User $user): bool
    {
        if ($user->id === Auth::id()) {   // ← Sekarang pakai Auth::id()
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);
        }

        return DB::transaction(function () use ($user) {
            return $this->repository->delete($user);
        });
    }

    /**
     * Generate password dari nama user
     * Format: {FirstName}@{Year}
     * Contoh: Budi@2026
     */
    protected function generatePassword(string $name): string
    {
        // Ambil nama depan (kata pertama)
        $firstName = explode(' ', trim($name))[0];
        
        // Kapitalisasi
        $firstName = ucfirst(strtolower($firstName));
        
        // Buang karakter non-alfanumerik
        $firstName = preg_replace('/[^A-Za-z0-9]/', '', $firstName);
        
        // Tambahkan tahun
        $year = now()->format('Y');
        
        return "{$firstName}@{$year}";
    }

    /**
     * Statistik user
     */
    public function getStats(): array
    {
        return $this->repository->getStats();
    }
}