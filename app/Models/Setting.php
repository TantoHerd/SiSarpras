<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group_name', 'key', 'value', 'type', 
        'description', 'is_public', 'validation_rules'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'validation_rules' => 'array',
    ];

    // Accessor untuk konversi nilai sesuai tipe
    public function getTypedValueAttribute()
    {
        return match($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    // Scope
    public function scopeGroup($query, $group)
    {
        return $query->where('group_name', $group);
    }
}