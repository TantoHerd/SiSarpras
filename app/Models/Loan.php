<?php
// app/Models/Loan.php

namespace App\Models;

use App\Enums\LoanStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id', 'borrower_id', 'processed_by',
        'loan_date', 'due_date', 'return_date',
        'status', 'purpose', 'fine_amount', 'is_fine_paid'
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'fine_amount' => 'decimal:2',
        'is_fine_paid' => 'boolean',
        'status' => LoanStatusEnum::class,
    ];

    // Relations
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            LoanStatusEnum::DIPINJAM->value,
            LoanStatusEnum::TERLAMBAT->value
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', LoanStatusEnum::DIPINJAM->value)
                     ->where('due_date', '<', now());
    }

    public function scopeByBorrower($query, $userId)
    {
        return $query->where('borrower_id', $userId);
    }

    // Helper Methods
    public function isOverdue(): bool
    {
        return $this->status !== LoanStatusEnum::DIKEMBALIKAN
            && $this->due_date < now();
    }

    public function isReturned(): bool
    {
        return $this->status === LoanStatusEnum::DIKEMBALIKAN;
    }

    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) return 0;
        return $this->due_date->diffInDays(now());
    }

    public function calculateFine(int $finePerDay = 0): float
    {
        if ($finePerDay <= 0 || !$this->isOverdue()) return 0;
        return $this->daysOverdue() * $finePerDay;
    }
}