<?php
// app/Exports/InventoryReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithEvents, WithTitle
{
    protected $items;
    protected $summary;
    protected $filters;

    // Konstanta layout
    protected const HEADER_ROW = 9;
    protected const TOTAL_COLUMNS = 'N';

    public function __construct($items, $summary, $filters = [])
    {
        $this->items = $items;
        $this->summary = $summary;
        $this->filters = $filters;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Laporan Inventaris';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->buildReport($event->sheet->getDelegate());
            },
        ];
    }

    /**
     * Build laporan lengkap
     */
    protected function buildReport(Worksheet $sheet): void
    {
        $this->buildHeader($sheet);
        $this->buildTableHeader($sheet);

        $lastDataRow = $this->buildTableData($sheet);
        $totalRow = $this->buildTotalRow($sheet, $lastDataRow);

        $this->buildSignature($sheet, $totalRow);

        // Freeze pane di bawah header tabel
        $sheet->freezePane('A' . (self::HEADER_ROW + 1));

        // Auto-filter di header
        $sheet->setAutoFilter('A' . self::HEADER_ROW . ':' . self::TOTAL_COLUMNS . self::HEADER_ROW);
    }

    /**
     * Kop surat — logo, nama, alamat, kontak
     */
    protected function buildHeader(Worksheet $sheet): void
    {
        // ============ LOGO (jika ada) ============
        $logoFile = setting('school_logo', 'logo-default.png');
        $logoPath = storage_path('app/public/' . $logoFile);
        $logoExists = $logoFile
            && $logoFile !== 'logo-default.png'
            && file_exists($logoPath);

        // Set tinggi baris untuk kop
        $sheet->getRowDimension(2)->setRowHeight(28);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(16);

        if ($logoExists) {
            $drawing = new Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setPath($logoPath);
            $drawing->setHeight(55);
            $drawing->setCoordinates('A2');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        } else {
            // Fallback: inisial sekolah
            $sheet->mergeCells('A2:A4');
            $sheet->setCellValue('A2', strtoupper(substr(setting('school_name', 'S'), 0, 1)));
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['bold' => true, 'size' => 28, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0064E0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // ============ NAMA SEKOLAH (baris 2) ============
        $sheet->mergeCells('B2:N2');
        $sheet->setCellValue('B2', strtoupper(setting('school_name', 'NAMA SEKOLAH')));
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0A1317']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ============ ALAMAT (baris 3) ============
        $sheet->mergeCells('B3:N3');
        $sheet->setCellValue('B3', setting('school_address', 'Alamat Sekolah'));
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ============ KONTAK (baris 4) ============
        $contact = [];
        if (setting('school_phone')) $contact[] = 'Telp: ' . setting('school_phone');
        if (setting('school_email')) $contact[] = 'Email: ' . setting('school_email');
        if (setting('school_npsn'))  $contact[] = 'NPSN: ' . setting('school_npsn');

        $sheet->mergeCells('B4:N4');
        $sheet->setCellValue('B4', implode(' | ', $contact));
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ============ GARIS PEMISAH KOP (baris 5) ============
        $sheet->getRowDimension(5)->setRowHeight(4);
        $sheet->getStyle('A5:N5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);

        // ============ JUDUL LAPORAN (baris 6) ============
        $sheet->getRowDimension(6)->setRowHeight(24);
        $sheet->mergeCells('A6:N6');
        $sheet->setCellValue('A6', 'LAPORAN INVENTARIS BARANG');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0A1317']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ============ SUB-JUDUL (baris 7) ============
        $sheet->mergeCells('A7:N7');
        $subtitle = 'Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB';
        if (!empty($this->filters['category_id']) || !empty($this->filters['location_id'])
            || !empty($this->filters['condition']) || !empty($this->filters['status'])) {
            $subtitle = 'Dengan filter diterapkan — ' . $subtitle;
        }
        $sheet->setCellValue('A7', $subtitle);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ============ RINGKASAN (baris 8) ============
        $sheet->getRowDimension(8)->setRowHeight(28);
        $sheet->mergeCells('A8:N8');

        $summaryText = sprintf(
            'Total Item: %s   |   Quantity: %s   |   Nilai Aset: %s   |   Baik: %s   |   Rusak: %s',
            number_format($this->summary['total_items']),
            number_format($this->summary['total_quantity']),
            format_currency($this->summary['total_value']),
            $this->summary['by_condition']['baik'],
            $this->summary['by_condition']['rusak_ringan'] + $this->summary['by_condition']['rusak_berat']
        );
        $sheet->setCellValue('A8', $summaryText);
        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['size' => 10, 'bold' => true, 'color' => ['rgb' => '0A1317']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F4F7'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE3E9'],
                ],
            ],
        ]);
    }

    /**
     * Header tabel
     */
    protected function buildTableHeader(Worksheet $sheet): void
    {
        $headers = [
            'No', 'Kode', 'Nama Barang', 'Kategori', 'Lokasi',
            'Merk', 'Tipe', 'Serial Number', 'Tahun',
            'Kondisi', 'Status', 'Jumlah', 'Harga Beli', 'Total Harga'
        ];

        $row = self::HEADER_ROW;
        $sheet->getRowDimension($row)->setRowHeight(30);

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A1317'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);
    }

    /**
     * Data tabel — return last data row
     */
    protected function buildTableData(Worksheet $sheet): int
    {
        $row = self::HEADER_ROW + 1;

        if ($this->items->count() === 0) {
            $sheet->mergeCells("A{$row}:N{$row}");
            $sheet->setCellValue("A{$row}", 'Tidak ada data barang untuk filter yang dipilih.');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '8595A4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(40);

            return $row;
        }

        $no = 1;
        $startDataRow = $row;

        foreach ($this->items as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $item->code);
            $sheet->setCellValue("C{$row}", $item->name);
            $sheet->setCellValue("D{$row}", $item->category->name ?? '-');
            $sheet->setCellValue("E{$row}", $item->location->name ?? '-');
            $sheet->setCellValue("F{$row}", $item->brand ?? '-');
            $sheet->setCellValue("G{$row}", $item->type ?? '-');
            $sheet->setCellValue("H{$row}", $item->serial_number ?? '-');
            $sheet->setCellValue("I{$row}", $item->purchase_year ?? '-');
            $sheet->setCellValue("J{$row}", $item->condition->label());
            $sheet->setCellValue("K{$row}", $item->status->label());
            $sheet->setCellValue("L{$row}", $item->quantity);
            $sheet->setCellValue("M{$row}", (float) $item->price);
            $sheet->setCellValue("N{$row}", (float) ($item->price * $item->quantity));

            $row++;
        }

        $endDataRow = $row - 1;

        // Style body
        $sheet->getStyle("A{$startDataRow}:N{$endDataRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE3E9'],
                ],
            ],
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Alignment per kolom
        $sheet->getStyle("A{$startDataRow}:A{$endDataRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$startDataRow}:L{$endDataRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M{$startDataRow}:N{$endDataRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format angka
        $sheet->getStyle("M{$startDataRow}:N{$endDataRow}")
            ->getNumberFormat()->setFormatCode('#,##0');

        // Zebra striping
        for ($i = $startDataRow; $i <= $endDataRow; $i++) {
            if (($i - $startDataRow) % 2 === 1) {
                $sheet->getStyle("A{$i}:N{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        return $endDataRow;
    }

    /**
     * Baris total — return total row number
     */
    protected function buildTotalRow(Worksheet $sheet, int $lastDataRow): int
    {
        $totalRow = $lastDataRow + 1;
        $sheet->getRowDimension($totalRow)->setRowHeight(30);

        // Label
        $sheet->mergeCells("A{$totalRow}:L{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL NILAI ASET');
        $sheet->getStyle("A{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Nilai
        $sheet->mergeCells("M{$totalRow}:N{$totalRow}");
        $sheet->setCellValue("M{$totalRow}", (float) $this->summary['total_value']);
        $sheet->getStyle("M{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle("M{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Background hitam + border
        $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A1317'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);

        return $totalRow;
    }

    /**
     * Tanda tangan
     */
    protected function buildSignature(Worksheet $sheet, int $totalRow): void
    {
        $startRow = $totalRow + 3;

        // Kota & tanggal
        $sheet->mergeCells("J{$startRow}:N{$startRow}");
        $sheet->setCellValue("J{$startRow}", now()->translatedFormat('d F Y'));
        $sheet->getStyle("J{$startRow}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Jabatan
        $row = $startRow + 1;
        $sheet->mergeCells("J{$row}:N{$row}");
        $sheet->setCellValue("J{$row}", 'Kepala Sekolah,');
        $sheet->getStyle("J{$row}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Space untuk tanda tangan
        $row += 4;

        // Nama
        $sheet->mergeCells("J{$row}:N{$row}");
        $sheet->setCellValue("J{$row}", setting('headmaster_name', '_______________________'));
        $sheet->getStyle("J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);

        // NIP
        if (setting('headmaster_nip')) {
            $row++;
            $sheet->mergeCells("J{$row}:N{$row}");
            $sheet->setCellValue("J{$row}", 'NIP. ' . setting('headmaster_nip'));
            $sheet->getStyle("J{$row}")->applyFromArray([
                'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Auto-size kolom
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum width untuk kolom tertentu
        $sheet->getColumnDimension('C')->setWidth(35);  // Nama Barang
        $sheet->getColumnDimension('D')->setWidth(15);  // Kategori
        $sheet->getColumnDimension('E')->setWidth(18);  // Lokasi
        $sheet->getColumnDimension('M')->setWidth(15);  // Harga Beli
        $sheet->getColumnDimension('N')->setWidth(15);  // Total Harga
    }
}