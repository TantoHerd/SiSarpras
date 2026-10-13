<?php
// app/Models/StockRequest.php

namespace App\Models;

use App\Enums\StockRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_number',
        'requester_id',
        'requester_type',
        'purpose',
        'status',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'note',
    ];

    protected $casts = [
        'status'       => StockRequestStatusEnum::class,
        'processed_at' => 'datetime',
    ];

    /* ─────────── Relations ─────────── */

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(StockRequestItem::class);
    }

    /* ─────────── Scopes ─────────── */

    public function scopePending($query)
    {
        return $query->where('status', StockRequestStatusEnum::PENDING->value);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', StockRequestStatusEnum::APPROVED->value);
    }

    public function scopeByRequester($query, int $userId)
    {
        return $query->where('requester_id', $userId);
    }

    /* ─────────── Accessors ─────────── */

    public function getStatusLabelAttribute(): string
    {
        return $this->status instanceof StockRequestStatusEnum
            ? $this->status->label()
            : (string) $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status instanceof StockRequestStatusEnum
            ? $this->status->badge()
            : 'badge-neutral';
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    /* ─────────── Helpers ─────────── */

    public function isPending(): bool
    {
        return $this->status === StockRequestStatusEnum::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === StockRequestStatusEnum::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === StockRequestStatusEnum::REJECTED;
    }

    /**
     * Generate nomor permintaan: SR-YYYY-MM-NNN
     */
    public static function generateNumber(): string
    {
        $prefix = 'SR-' . now()->format('Y-m');
        
        $last = self::withTrashed()
            ->where('request_number', 'LIKE', "{$prefix}-%")
            ->orderByDesc('request_number')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->request_number, -3);
            $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next = '001';
        }

        return "{$prefix}-{$next}";
    }
}