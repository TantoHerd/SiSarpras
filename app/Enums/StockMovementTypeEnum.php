<?php
// app/Enums/StockMovementTypeEnum.php

namespace App\Enums;

enum StockMovementTypeEnum: string
{
    case IN = 'in';
    case OUT = 'out';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::IN         => 'Masuk',
            self::OUT        => 'Keluar',
            self::ADJUSTMENT => 'Penyesuaian',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::IN         => 'badge-success',
            self::OUT        => 'badge-critical',
            self::ADJUSTMENT => 'badge-warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IN         => 'fa-arrow-down',
            self::OUT        => 'fa-arrow-up',
            self::ADJUSTMENT => 'fa-balance-scale',
        };
    }
}