<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ─────────── Relations ─────────── */

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

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
            $q->where('code', 'ILIKE', "%{$term}%")
              ->orWhere('name', 'ILIKE', "%{$term}%");
        });
    }

    /* ─────────── Accessors ─────────── */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}