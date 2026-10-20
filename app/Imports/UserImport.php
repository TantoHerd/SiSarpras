<?php
// app/Imports/UserImport.php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class UserImport implements ToModel, WithHeadingRow, SkipsOnError
{
    protected int $successCount = 0;
    protected int $updateCount = 0;
    protected int $skipCount = 0;
    protected array $skipped = [];
    protected array $errors = [];

    public function model(array $row)
    {
        // Pastikan NIP dan No HP dibaca sebagai string murni
        $nip   = trim((string) ($row['nip'] ?? ''));
        $name  = trim((string) ($row['nama'] ?? ''));
        $email = trim((string) ($row['email'] ?? ''));
        $role  = trim((string) ($row['role'] ?? 'guru'));
        $phone = trim((string) ($row['no_hp'] ?? ''));

        if (empty($nip) || empty($name) || empty($email)) {
            $this->skipCount++;
            $this->skipped[] = [
                'nip' => $nip,
                'reason' => 'NIP, Nama, atau Email kosong',
            ];
            return null;
        }

        // Cari role ID berdasarkan nama
        $roleModel = Role::where('name', strtolower($role))->first();
        if (!$roleModel) {
            $this->skipCount++;
            $this->skipped[] = [
                'nip' => $nip,
                'reason' => "Role '{$role}' tidak ditemukan",
            ];
            return null;
        }

        $existing = User::where('nip', $nip)->orWhere('email', $email)->first();

        if ($existing) {
            $existing->update([
                'name' => $name,
                'email' => $email,
                'role_id' => $roleModel->id,
                'phone' => $phone ?: null,
            ]);
            $this->updateCount++;
            return null;
        }

        $this->successCount++;
        return new User([
            'nip' => $nip,
            'name' => $name,
            'email' => $email,
            'role_id' => $roleModel->id,
            'phone' => $phone ?: null,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    // Getter methods
    public function getSuccessCount(): int { return $this->successCount; }
    public function getUpdateCount(): int { return $this->updateCount; }
    public function getSkipCount(): int { return $this->skipCount; }
    public function getSkipped(): array { return $this->skipped; }
    public function getErrors(): array { return $this->errors; }
}