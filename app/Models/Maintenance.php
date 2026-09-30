<?php
// app/Models/Maintenance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id', 'maintenance_date', 'type', 'status', 'cost',
        'description', 'technician', 'next_maintenance_date',
        'completed_at', 'completed_by', 'completion_note',
        'created_by'
    ];

    protected $casts = [
        'maintenance_date'      => 'datetime',
        'next_maintenance_date' => 'datetime',
        'completed_at'          => 'datetime',
        'cost'                  => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ==================== SCOPES ====================

    public function scopeRoutine($query)
    {
        return $query->where('type', 'rutin');
    }

    public function scopeRepair($query)
    {
        return $query->where('type', 'perbaikan');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->whereNotNull('next_maintenance_date')
                     ->whereBetween('next_maintenance_date', [now(), now()->addDays($days)]);
    }

    // ==================== HELPERS ====================

    public function isRoutine(): bool
    {
        return $this->type === 'rutin';
    }

    public function isRepair(): bool
    {
        return $this->type === 'perbaikan';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Berapa lama sudah dalam proses perbaikan (dalam hari)
     */
    public function getDaysInProgressAttribute(): int
    {
        if (!$this->isInProgress()) return 0;
        return $this->maintenance_date->diffInDays(now());
    }

    /**
     * Label status untuk tampilan
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'Sedang Diperbaiki',
            'completed'   => 'Selesai',
            default       => ucfirst($this->status),
        };
    }

    /**
     * Badge color untuk status
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'badge-warning',
            'completed'   => 'badge-success',
            default       => 'badge-neutral',
        };
    }
}