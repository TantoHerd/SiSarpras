<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon'];

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
}