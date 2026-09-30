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
        'item_id', 'maintenance_date', 'type', 'cost',
        'description', 'technician', 'next_maintenance_date', 'created_by'
    ];

    protected $casts = [
        'maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'cost' => 'decimal:2',
    ];

    // Relations
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeRoutine($query)
    {
        return $query->where('type', 'rutin');
    }

    public function scopeRepair($query)
    {
        return $query->where('type', 'perbaikan');
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->whereNotNull('next_maintenance_date')
                     ->whereBetween('next_maintenance_date', [now(), now()->addDays($days)]);
    }

    // Helper
    public function isRoutine(): bool
    {
        return $this->type === 'rutin';
    }

    public function isRepair(): bool
    {
        return $this->type === 'perbaikan';
    }
}