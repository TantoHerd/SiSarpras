<?php
// app/Models/StockOpname.php

namespace App\Models;

use App\Enums\StockOpnameStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opname_number',
        'location_id',
        'status',
        'started_by',
        'started_at',
        'completed_by',
        'completed_at',
        'note',
    ];

    protected $casts = [
        'status'       => StockOpnameStatusEnum::class,
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    /* ─────────── Relations ─────────── */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function starter()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /* ─────────── Scopes ─────────── */

    public function scopeDraft($query)
    {
        return $query->where('status', StockOpnameStatusEnum::DRAFT->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', StockOpnameStatusEnum::COMPLETED->value);
    }

    /* ─────────── Accessors ─────────── */

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof StockOpnameStatusEnum
            ? $this->status->label()
            : (string) $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status instanceof StockOpnameStatusEnum
            ? $this->status->badge()
            : 'badge-neutral';
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    public function getTotalDifferenceAttribute(): int
    {
        return $this->items()->sum('difference');
    }

    public function getTotalDifferenceAbsAttribute(): int
    {
        return $this->items()->sum(DB::raw('ABS(difference)'));
    }

    /* ─────────── Helpers ─────────── */

    public function isDraft(): bool
    {
        return $this->status === StockOpnameStatusEnum::DRAFT;
    }

    public function isCompleted(): bool
    {
        return $this->status === StockOpnameStatusEnum::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === StockOpnameStatusEnum::CANCELLED;
    }

    /**
     * Generate nomor opname: SO-YYYY-MM-NNN
     */
    public static function generateNumber(): string
    {
        $prefix = 'SO-' . now()->format('Y-m');
        
        $last = self::withTrashed()
            ->where('opname_number', 'LIKE', "{$prefix}-%")
            ->orderByDesc('opname_number')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->opname_number, -3);
            $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next = '001';
        }

        return "{$prefix}-{$next}";
    }
}