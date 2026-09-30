<?php
// app/Enums/ItemStatusEnum.php

namespace App\Enums;

enum ItemStatusEnum: string
{
    case TERSEDIA = 'tersedia';
    case DIPINJAM = 'dipinjam';
    case PERBAIKAN = 'perbaikan';
    case TIDAK_AKTIF = 'tidak_aktif';

    public function label(): string
    {
        return match($this) {
            self::TERSEDIA => 'Tersedia',
            self::DIPINJAM => 'Dipinjam',
            self::PERBAIKAN => 'Dalam Perbaikan',
            self::TIDAK_AKTIF => 'Tidak Aktif',
        };
    }
}