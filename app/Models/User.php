<?php
// app/Models/User.php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'name', 'email', 'password', 'nip', 
        'phone', 'avatar', 'is_active', 'last_login_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    public function processedLoans()
    {
        return $this->hasMany(Loan::class, 'processed_by');
    }

    // Helper Methods
    public function hasRole(RoleEnum $role): bool
    {
        return $this->role->name === $role->value;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN);
    }

    public function isPetugas(): bool
    {
        return $this->hasRole(RoleEnum::PETUGAS_SARPRAS);
    }

    public function isKepalaSekolah(): bool
    {
        return $this->hasRole(RoleEnum::KEPALA_SEKOLAH);
    }

    public function isGuru(): bool
    {
        return $this->hasRole(RoleEnum::GURU);
    }
}