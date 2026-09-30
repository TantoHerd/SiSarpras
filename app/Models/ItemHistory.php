<?php
// app/Models/ItemHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'user_id', 'action',
        'old_value', 'new_value', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    // Relations
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope
    public function scopeByItem($query, $itemId)
    {
        return $query->where('item_id', $itemId)->latest();
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    // Helper
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'restored' => 'Dipulihkan',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'repaired' => 'Diperbaiki',
            default => $this->action,
        };
    }
}