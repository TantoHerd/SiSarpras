<?php
// app/Exports/ItemTemplateExport.php

namespace App\Exports;

use App\Models\Category;
use App\Models\Location;
use App\Models\Unit;
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

class ItemTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [ 
            new ItemDataSheet(), 
            new ItemGuideSheet() 
        ];
    }
}

class ItemDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function title(): string { return 'Data Import'; }
    
    public function headings(): array {
        return [
            'nama_barang', 'kategori', 'lokasi', 'satuan', 'tipe_barang', 
            'jumlah', 'harga', 'tahun_perolehan', 'kondisi', 'supplier', 'sumber_dana', 'min_stok'
        ];
    }

    public function array(): array {
        return [
            ['Laptop Asus', 'Elektronik', 'Lab Komputer', 'Unit', 'Aset', '1', '15000000', '2026', 'Baik', 'PT. Maju', 'BOS', ''],
            ['Kertas HVS', 'ATK', 'Gudang', 'Rim', 'Habis Pakai', '50', '50000', '2026', 'Baik', '', 'BOS', '10'],
        ];
    }

    public function styles(Worksheet $sheet) {
        return [ 
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0064E0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ] 
        ];
    }

    public function registerEvents(): array {
        return [ 
            AfterSheet::class => function(AfterSheet $event) {
                // Set format kolom Text agar angka 0 di depan (jika ada) tidak hilang
                $event->sheet->getDelegate()->getStyle('A1:L100')->getNumberFormat()->setFormatCode('@'); 
            }
        ];
    }
}

class ItemGuideSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string { return 'Petunjuk'; }

    public function array(): array
    {
        // Ambil data referensi dari database
        $categories = Category::orderBy('name')->pluck('name')->toArray();
        $locations = Location::orderBy('name')->pluck('name')->toArray();
        $units = Unit::orderBy('name')->pluck('name')->toArray();

        $catStr = count($categories) > 0 ? implode(', ', $categories) : 'Belum ada data kategori';
        $locStr = count($locations) > 0 ? implode(', ', $locations) : 'Belum ada data lokasi';
        $unitStr = count($units) > 0 ? implode(', ', $units) : 'Belum ada data satuan';

        return [
            ['PANDUAN IMPORT DATA BARANG'],
            [''],
            ['1. Baris pertama (header) JANGAN dihapus atau diubah.'],
            ['2. Data dimulai dari baris ke-2.'],
            ['3. Kode Barang akan dibuat OTOMATIS oleh sistem.'],
            ['4. Tipe Barang: isi "Aset" atau "Habis Pakai".'],
            ['5. Kondisi: isi "Baik", "Rusak Ringan", atau "Rusak Berat".'],
            [''],
            ['------------------------------------------------------------------------'],
            ['DAFTAR REFERENSI (WAJIB SAMA PERSIS)'],
            ['------------------------------------------------------------------------'],
            [''],
            ['KATEGORI YANG TERSEDIA:', $catStr],
            [''],
            ['LOKASI YANG TERSEDIA:', $locStr],
            [''],
            ['SATUAN YANG TERSEDIA:', $unitStr],
            [''],
            ['SUMBER DANA:', 'Isi dengan KODE (contoh: BOS, BOPD, BANPROV, LAINNYA)'],
            [''],
            ['CATATAN PENTING:'],
            ['Pastikan ejaan Kategori, Lokasi, dan Satuan sama persis (termasuk huruf besar/kecil)'],
            ['dengan daftar referensi di atas. Jika tidak sama, data barang akan dilewati (skip).'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0064E0']]],
            9 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0A1317']]],
        ];
    }
}