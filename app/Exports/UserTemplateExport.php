<?php
// app/Exports/UserTemplateExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UserTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new UserDataSheet(),
            new UserGuideSheet(),
        ];
    }
}

/**
 * Sheet 1: Data
 */
class UserDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function title(): string
    {
        return 'Data Import';
    }

    public function headings(): array
    {
        return ['nip', 'nama', 'email', 'role', 'no_hp'];
    }

    public function array(): array
    {
        return [
            ['198001012005011001', 'Budi Santoso, S.Pd', 'budi@sekolah.sch.id', 'guru', '081234567890'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0064E0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    /**
     * Set format kolom NIP (A) dan No HP (E) menjadi Text
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set format kolom A (NIP) dan E (No HP) menjadi Text (@)
                $event->sheet->getDelegate()->getStyle('A')->getNumberFormat()->setFormatCode('@');
                $event->sheet->getDelegate()->getStyle('E')->getNumberFormat()->setFormatCode('@');
                
                // Pastikan data contoh juga di-set sebagai string (text)
                $event->sheet->getDelegate()->setCellValueExplicit('A2', '198001012005011001', DataType::TYPE_STRING);
                $event->sheet->getDelegate()->setCellValueExplicit('E2', '081234567890', DataType::TYPE_STRING);
            },
        ];
    }
}

/**
 * Sheet 2: Petunjuk
 */
class UserGuideSheet implements FromArray, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Petunjuk';
    }

    public function array(): array
    {
        return [
            ['PANDUAN IMPORT DATA USER (GURU/STAF/PETUGAS)'],
            [''],
            ['1. Baris pertama (header) JANGAN dihapus atau diubah.'],
            ['2. Data dimulai dari baris ke-2.'],
            ['3. Jika NIP atau Email sudah ada di sistem, data akan DIPERBARUI.'],
            ['4. Password default untuk user baru adalah: password123'],
            [''],
            ['FORMAT KOLOM:'],
            ['Kolom A (nip)', 'Wajib', 'Nomor Induk Pegawai (unik)'],
            ['Kolom B (nama)', 'Wajib', 'Nama lengkap dengan gelar'],
            ['Kolom C (email)', 'Wajib', 'Email aktif (unik)'],
            ['Kolom D (role)', 'Wajib', 'Pilihan: guru, petugas_sarpras, kepala_sekolah, admin'],
            ['Kolom E (no_hp)', 'Opsional', 'Nomor HP aktif'],
        ];
    }
}