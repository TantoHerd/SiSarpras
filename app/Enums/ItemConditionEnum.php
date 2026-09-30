<?php
// app/Enums/ItemConditionEnum.php

namespace App\Enums;

enum ItemConditionEnum: string
{
    case BAIK = 'baik';
    case RUSAK_RINGAN = 'rusak_ringan';
    case RUSAK_BERAT = 'rusak_berat';

    public function label(): string
    {
        return match($this) {
            self::BAIK => 'Baik',
            self::RUSAK_RINGAN => 'Rusak Ringan',
            self::RUSAK_BERAT => 'Rusak Berat',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::BAIK => 'success',
            self::RUSAK_RINGAN => 'warning',
            self::RUSAK_BERAT => 'danger',
        };
    }
}