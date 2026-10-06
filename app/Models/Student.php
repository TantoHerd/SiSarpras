<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'name',
        'class',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ─────────── Scopes ─────────── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('nis', 'ILIKE', "%{$term}%")
              ->orWhere('name', 'ILIKE', "%{$term}%")
              ->orWhere('class', 'ILIKE', "%{$term}%");
        });
    }

    public function scopeByClass($query, ?string $class)
    {
        if (blank($class)) {
            return $query;
        }

        return $query->where('class', $class);
    }

    /* ─────────── Accessors ─────────── */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->nis} - {$this->name} ({$this->class})";
    }
}