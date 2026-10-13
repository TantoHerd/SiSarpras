<?php
// app/Models/StockOpnameItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'item_id',
        'system_stock',
        'physical_stock',
        'difference',
        'note',
    ];

    protected $casts = [
        'system_stock'   => 'integer',
        'physical_stock' => 'integer',
        'difference'     => 'integer',
    ];

    /* ─────────── Relations ─────────── */

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /* ─────────── Accessors ─────────── */

    public function getDifferenceLabelAttribute(): string
    {
        if ($this->difference === 0) {
            return 'Sesuai';
        }
        
        return $this->difference > 0 
            ? "+{$this->difference}" 
            : (string) $this->difference;
    }

    public function getDifferenceBadgeAttribute(): string
    {
        return match (true) {
            $this->difference === 0  => 'badge-success',
            $this->difference > 0    => 'badge-info',
            default                  => 'badge-critical',
        };
    }
}