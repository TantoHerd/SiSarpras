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
    protected const TOTAL_COLUMNS = 'P';  // ← FIX: Dari O menjadi P (karena ada kolom Satuan)

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

    protected function buildReport(Worksheet $sheet): void
    {
        $this->buildHeader($sheet);
        $this->buildTableHeader($sheet);

        $lastDataRow = $this->buildTableData($sheet);
        $totalRow = $this->buildTotalRow($sheet, $lastDataRow);

        $this->buildSignature($sheet, $totalRow);

        $sheet->freezePane('A' . (self::HEADER_ROW + 1));
        $sheet->setAutoFilter('A' . self::HEADER_ROW . ':' . self::TOTAL_COLUMNS . self::HEADER_ROW);
    }

    protected function buildHeader(Worksheet $sheet): void
    {
        // LOGO
        $logoFile = setting('school_logo', 'logo-default.png');
        $logoPath = storage_path('app/public/' . $logoFile);
        $logoExists = $logoFile && $logoFile !== 'logo-default.png' && file_exists($logoPath);

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
            $sheet->mergeCells('A2:A4');
            $sheet->setCellValue('A2', strtoupper(substr(setting('school_name', 'S'), 0, 1)));
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['bold' => true, 'size' => 28, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0064E0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        // NAMA SEKOLAH (Merge B2:P2)
        $sheet->mergeCells('B2:P2');
        $sheet->setCellValue('B2', strtoupper(setting('school_name', 'NAMA SEKOLAH')));
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ALAMAT (Merge B3:P3)
        $sheet->mergeCells('B3:P3');
        $sheet->setCellValue('B3', setting('school_address', 'Alamat Sekolah'));
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // KONTAK (Merge B4:P4)
        $contact = [];
        if (setting('school_phone')) $contact[] = 'Telp: ' . setting('school_phone');
        if (setting('school_email')) $contact[] = 'Email: ' . setting('school_email');
        if (setting('school_npsn'))  $contact[] = 'NPSN: ' . setting('school_npsn');

        $sheet->mergeCells('B4:P4');
        $sheet->setCellValue('B4', implode(' | ', $contact));
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // GARIS PEMISAH (A5:P5)
        $sheet->getRowDimension(5)->setRowHeight(4);
        $sheet->getStyle('A5:P5')->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0A1317']]],
        ]);

        // JUDUL (A6:P6)
        $sheet->getRowDimension(6)->setRowHeight(24);
        $sheet->mergeCells('A6:P6');
        $sheet->setCellValue('A6', 'LAPORAN INVENTARIS BARANG');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // SUB-JUDUL (A7:P7)
        $sheet->mergeCells('A7:P7');
        $subtitle = 'Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB';
        if (!empty($this->filters['category_id']) || !empty($this->filters['location_id'])
            || !empty($this->filters['condition']) || !empty($this->filters['status'])
            || !empty($this->filters['funding_source_id'])) {
            $subtitle = 'Dengan filter diterapkan — ' . $subtitle;
        }
        $sheet->setCellValue('A7', $subtitle);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // RINGKASAN (A8:P8)
        $sheet->getRowDimension(8)->setRowHeight(28);
        $sheet->mergeCells('A8:P8');

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
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F4F7']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE3E9']]],
        ]);
    }

    protected function buildTableHeader(Worksheet $sheet): void
    {
        $headers = [
            'No', 'Kode', 'Nama Barang', 'Kategori', 'Lokasi',
            'Sumber Dana', 'Merk', 'Tipe', 'Serial Number', 'Tahun',
            'Kondisi', 'Status', 'Jumlah', 'Satuan', 'Harga Beli', 'Total Harga'
        ];

        $row = self::HEADER_ROW;
        $sheet->getRowDimension($row)->setRowHeight(30);

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);
    }

    protected function buildTableData(Worksheet $sheet): int
    {
        $row = self::HEADER_ROW + 1;

        if ($this->items->count() === 0) {
            $sheet->mergeCells("A{$row}:P{$row}");
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
            $sheet->setCellValue("F{$row}", $item->fundingSource->code ?? '-');
            $sheet->setCellValue("G{$row}", $item->brand ?? '-');
            $sheet->setCellValue("H{$row}", $item->type ?? '-');
            $sheet->setCellValue("I{$row}", $item->serial_number ?? '-');
            $sheet->setCellValue("J{$row}", $item->purchase_year ?? '-');
            $sheet->setCellValue("K{$row}", $item->condition->label());
            $sheet->setCellValue("L{$row}", $item->status->label());
            $sheet->setCellValue("M{$row}", $item->quantity);
            $sheet->setCellValue("N{$row}", $item->unit->name ?? '-'); // Satuan
            $sheet->setCellValue("O{$row}", (float) $item->price); // Harga
            $sheet->setCellValue("P{$row}", (float) ($item->price * $item->quantity)); // Total
            $row++;
        }

        $endDataRow = $row - 1;

        $sheet->getStyle("A{$startDataRow}:P{$endDataRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE3E9']]],
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Alignment per kolom
        $sheet->getStyle("A{$startDataRow}:A{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M{$startDataRow}:N{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jumlah & Satuan
        $sheet->getStyle("O{$startDataRow}:P{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Harga & Total

        // Format angka
        $sheet->getStyle("O{$startDataRow}:P{$endDataRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Zebra striping
        for ($i = $startDataRow; $i <= $endDataRow; $i++) {
            if (($i - $startDataRow) % 2 === 1) {
                $sheet->getStyle("A{$i}:P{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        return $endDataRow;
    }

    protected function buildTotalRow(Worksheet $sheet, int $lastDataRow): int
    {
        $totalRow = $lastDataRow + 1;
        $sheet->getRowDimension($totalRow)->setRowHeight(30);

        // Label — merge A sampai N
        $sheet->mergeCells("A{$totalRow}:N{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL NILAI ASET');
        $sheet->getStyle("A{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Nilai — merge O:P
        $sheet->mergeCells("O{$totalRow}:P{$totalRow}");
        $sheet->setCellValue("O{$totalRow}", (float) $this->summary['total_value']);
        $sheet->getStyle("O{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("O{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle("A{$totalRow}:P{$totalRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A1317']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);

        return $totalRow;
    }

    protected function buildSignature(Worksheet $sheet, int $totalRow): void
    {
        $startRow = $totalRow + 3;

        // Kota & tanggal — merge L:P
        $sheet->mergeCells("L{$startRow}:P{$startRow}");
        $sheet->setCellValue("L{$startRow}", now()->translatedFormat('d F Y'));
        $sheet->getStyle("L{$startRow}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Jabatan
        $row = $startRow + 1;
        $sheet->mergeCells("L{$row}:P{$row}");
        $sheet->setCellValue("L{$row}", 'Kepala Sekolah,');
        $sheet->getStyle("L{$row}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 4;

        // Nama
        $sheet->mergeCells("L{$row}:P{$row}");
        $sheet->setCellValue("L{$row}", setting('headmaster_name', '_______________________'));
        $sheet->getStyle("L{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);

        // NIP
        if (setting('headmaster_nip')) {
            $row++;
            $sheet->mergeCells("L{$row}:P{$row}");
            $sheet->setCellValue("L{$row}", 'NIP. ' . setting('headmaster_nip'));
            $sheet->getStyle("L{$row}")->applyFromArray([
                'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Auto-size kolom A sampai P
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum width
        $sheet->getColumnDimension('C')->setWidth(35);  // Nama Barang
        $sheet->getColumnDimension('D')->setWidth(15);  // Kategori
        $sheet->getColumnDimension('E')->setWidth(18);  // Lokasi
        $sheet->getColumnDimension('F')->setWidth(12);  // Sumber Dana
        $sheet->getColumnDimension('O')->setWidth(15);  // Harga Beli
        $sheet->getColumnDimension('P')->setWidth(15);  // Total Harga
    }
}