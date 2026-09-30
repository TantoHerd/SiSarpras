<?php
// app/Models/Location.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'floor', 'description', 'parent_id'];

    // Relations
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    // Scope
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'ILIKE', "%{$term}%")
                     ->orWhere('floor', 'ILIKE', "%{$term}%");
    }

    // Accessor
    public function getFullNameAttribute(): string
    {
        return $this->floor 
            ? "{$this->name} ({$this->floor})" 
            : $this->name;
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }
}