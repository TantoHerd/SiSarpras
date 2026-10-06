<?php
// app/Models/PortalRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'nis',
        'student_name',
        'student_class',
        'student_phone',
        'item_id',
        'quantity',
        'purpose',
        'loan_date',
        'due_date',
        'status',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'loan_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'loan_date'    => 'date',
        'due_date'     => 'date',
        'processed_at' => 'datetime',
        'quantity'     => 'integer',
    ];

    /* ─────────── Relations ─────────── */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /* ─────────── Scopes ─────────── */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByNis($query, string $nis)
    {
        return $query->where('nis', $nis);
    }

    /* ─────────── Accessors ─────────── */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'expired'  => 'Kedaluwarsa',
            default    => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-critical',
            'expired'  => 'badge-neutral',
            default    => 'badge-neutral',
        };
    }

    /* ─────────── Helpers ─────────── */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}