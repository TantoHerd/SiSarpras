<?php
// app/Exports/StudentTemplateExport.php

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

class StudentTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new StudentDataSheet(),
            new StudentGuideSheet(),
        ];
    }
}

/**
 * Sheet 1: Data
 */
class StudentDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function title(): string
    {
        return 'Data Import';
    }

    public function headings(): array
    {
        return ['nis', 'nama', 'kelas', 'no_hp'];
    }

    public function array(): array
    {
        return [
            ['2024001', 'Ahmad Fauzi', 'X IPA 1', '081234567890'],
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
     * Set format kolom NIS (A) dan No HP (D) menjadi Text
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set format kolom A (NIS) dan D (No HP) menjadi Text (@)
                $event->sheet->getDelegate()->getStyle('A')->getNumberFormat()->setFormatCode('@');
                $event->sheet->getDelegate()->getStyle('D')->getNumberFormat()->setFormatCode('@');
                
                // Pastikan data contoh juga di-set sebagai string (text)
                $event->sheet->getDelegate()->setCellValueExplicit('A2', '2024001', DataType::TYPE_STRING);
                $event->sheet->getDelegate()->setCellValueExplicit('D2', '081234567890', DataType::TYPE_STRING);
            },
        ];
    }
}

/**
 * Sheet 2: Petunjuk
 */
class StudentGuideSheet implements FromArray, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Petunjuk';
    }

    public function array(): array
    {
        return [
            ['PANDUAN IMPORT DATA SISWA'],
            [''],
            ['1. Baris pertama (header) JANGAN dihapus atau diubah.'],
            ['2. Data dimulai dari baris ke-2.'],
            ['3. Jika NIS sudah ada di sistem, data akan DIPERBARUI.'],
            ['4. Siswa bisa menggunakan Portal Peminjaman setelah data masuk.'],
            [''],
            ['FORMAT KOLOM:'],
            ['Kolom A (nis)', 'Wajib', 'Nomor Induk Siswa (unik, tidak boleh sama)'],
            ['Kolom B (nama)', 'Wajib', 'Nama lengkap siswa'],
            ['Kolom C (kelas)', 'Wajib', 'Contoh: X IPA 1, XI IPS 2'],
            ['Kolom D (no_hp)', 'Opsional', 'Nomor HP aktif (untuk verifikasi portal)'],
        ];
    }
}