<?php
// app/Enums/StockOpnameStatusEnum.php

namespace App\Enums;

enum StockOpnameStatusEnum: string
{
    case DRAFT = 'draft';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::DRAFT     => 'badge-warning',
            self::COMPLETED => 'badge-success',
            self::CANCELLED => 'badge-neutral',
        };
    }
}