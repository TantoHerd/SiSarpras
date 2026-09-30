<?php
// app/Enums/RoleEnum.php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case PETUGAS_SARPRAS = 'petugas_sarpras';
    case KEPALA_SEKOLAH = 'kepala_sekolah';
    case GURU = 'guru';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::PETUGAS_SARPRAS => 'Petugas Sarpras',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::GURU => 'Guru/Staf',
        };
    }
}