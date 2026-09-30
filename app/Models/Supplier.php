<?php
// app/Models/Supplier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'contact_person'
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
                     ->orWhere('contact_person', 'ILIKE', "%{$term}%")
                     ->orWhere('phone', 'ILIKE', "%{$term}%");
    }
}