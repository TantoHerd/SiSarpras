<?php
// app/Models/StockMovement.php

namespace App\Models;

use App\Enums\StockMovementTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockMovement extends Model
{
    use HasFactory;

    // Log ini immutable — tidak ada update
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'note',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'type'       => StockMovementTypeEnum::class,
        'quantity'   => 'integer',
        'stock_before' => 'integer',
        'stock_after'  => 'integer',
        'created_at' => 'datetime',
    ];

    /* ─────────── Relations ─────────── */

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return match ($this->reference_type) {
            'stock_request' => StockRequest::find($this->reference_id),
            'stock_opname'  => StockOpname::find($this->reference_id),
            default         => null,
        };
    }

    /* ─────────── Scopes ─────────── */

    public function scopeByItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /* ─────────── Helpers ─────────── */

    /**
     * Catat pergerakan stok (static helper)
     */
    public static function record(
        int $itemId,
        string $type,
        int $quantity,
        int $stockBefore,
        int $stockAfter,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null,
        ?int $userId = null
    ): self {
        return self::create([
            'item_id'        => $itemId,
            'type'           => $type,
            'quantity'       => abs($quantity),
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'note'           => $note,
            'created_by'     => $userId ?? Auth::id(),
            'created_at'     => now(),
        ]);
    }
}