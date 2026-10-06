<?php
// app/Exports/StudentTemplateExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentTemplateExport implements 
    FromArray, 
    WithHeadings, 
    WithStyles, 
    WithColumnWidths
{
    public function headings(): array
    {
        return [
            'nis',
            'nama',
            'kelas',
            'no_hp',
        ];
    }

    public function array(): array
    {
        return [
            ['2024001', 'Ahmad Fauzi',      'X IPA 1', '081234567890'],
            ['2024002', 'Siti Nurhaliza',   'X IPA 1', '081234567891'],
            ['2024003', 'Budi Santoso',     'X IPA 2', '081234567892'],
            ['',        '',                 '',        ''],
            ['',        'Isi NIS siswa',    '',        ''],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,   // nis
            'B' => 30,   // nama
            'C' => 15,   // kelas
            'D' => 20,   // no_hp
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0064E0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}