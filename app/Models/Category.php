<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // ← tambah default_tracking_mode
    protected $fillable = [
        'name',
        'description',
        'icon',
        'default_tracking_mode',
        'allow_student_loan',
    ];

    protected $casts = [
        'allow_student_loan' => 'boolean',
    ];

    // Relations
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Scope
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'ILIKE', "%{$term}%")
                     ->orWhere('description', 'ILIKE', "%{$term}%");
    }

    // Accessor
    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }

    // ← BARU: label untuk tracking mode
    public function getTrackingModeLabelAttribute(): string
    {
        return match ($this->default_tracking_mode) {
            'per_unit'  => 'Per Unit',
            'per_batch' => 'Per Batch',
            default     => 'Per Unit',
        };
    }

    public function scopeStudentLoanable($query)
    {
        return $query->where('allow_student_loan', true);
    }
}