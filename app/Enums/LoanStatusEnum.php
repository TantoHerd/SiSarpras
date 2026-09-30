<?php
// app/Enums/LoanStatusEnum.php

namespace App\Enums;

enum LoanStatusEnum: string
{
    case DIPINJAM = 'dipinjam';
    case TERLAMBAT = 'terlambat';
    case DIKEMBALIKAN = 'dikembalikan';

    public function label(): string
    {
        return match($this) {
            self::DIPINJAM => 'Dipinjam',
            self::TERLAMBAT => 'Terlambat',
            self::DIKEMBALIKAN => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DIPINJAM => 'info',
            self::TERLAMBAT => 'danger',
            self::DIKEMBALIKAN => 'success',
        };
    }
}