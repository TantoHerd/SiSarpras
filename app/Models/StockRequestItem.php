<?php
// app/Models/StockRequestItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_request_id',
        'item_id',
        'quantity_requested',
        'quantity_approved',
        'note',
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_approved'  => 'integer',
    ];

    /* ─────────── Relations ─────────── */

    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /* ─────────── Accessors ─────────── */

    public function getEffectiveQuantityAttribute(): int
    {
        return $this->quantity_approved ?? $this->quantity_requested;
    }
}