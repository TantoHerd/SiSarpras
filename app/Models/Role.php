<?php
// app/Models/Role.php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper
    public function isAdmin(): bool
    {
        return $this->name === RoleEnum::ADMIN->value;
    }

    public function isPetugas(): bool
    {
        return $this->name === RoleEnum::PETUGAS_SARPRAS->value;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->name === RoleEnum::KEPALA_SEKOLAH->value;
    }

    public function isGuru(): bool
    {
        return $this->name === RoleEnum::GURU->value;
    }
}