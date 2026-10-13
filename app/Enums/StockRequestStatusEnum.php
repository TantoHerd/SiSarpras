<?php
// app/Enums/StockRequestStatusEnum.php

namespace App\Enums;

enum StockRequestStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING  => 'Menunggu Review',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING  => 'badge-warning',
            self::APPROVED => 'badge-success',
            self::REJECTED => 'badge-critical',
        };
    }
}