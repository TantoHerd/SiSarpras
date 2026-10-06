<?php
// app/Exports/FundingSourceExport.php

namespace App\Exports;

use App\Models\FundingSource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FundingSourceExport implements FromCollection, WithEvents, WithTitle
{
    protected const HEADER_ROW = 9;
    protected const TOTAL_COLUMNS = 'F';

    public function __construct(
        protected array $filters = []
    ) {}

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Master Sumber Dana';
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
        $this->buildSignature($sheet, $lastDataRow);

        $sheet->freezePane('A' . (self::HEADER_ROW + 1));
        $sheet->setAutoFilter('A' . self::HEADER_ROW . ':' . self::TOTAL_COLUMNS . self::HEADER_ROW);
    }

    protected function buildHeader(Worksheet $sheet): void
    {
        // ============ LOGO ============
        $logoFile = setting('school_logo', 'logo-default.png');
        $logoPath = storage_path('app/public/' . $logoFile);
        $logoExists = $logoFile
            && $logoFile !== 'logo-default.png'
            && file_exists($logoPath);

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

        // Nama Sekolah
        $sheet->mergeCells('B2:F2');
        $sheet->setCellValue('B2', strtoupper(setting('school_name', 'NAMA SEKOLAH')));
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0A1317']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alamat
        $sheet->mergeCells('B3:F3');
        $sheet->setCellValue('B3', setting('school_address', 'Alamat Sekolah'));
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Kontak
        $contact = [];
        if (setting('school_phone')) $contact[] = 'Telp: ' . setting('school_phone');
        if (setting('school_email')) $contact[] = 'Email: ' . setting('school_email');
        if (setting('school_npsn'))  $contact[] = 'NPSN: ' . setting('school_npsn');

        $sheet->mergeCells('B4:F4');
        $sheet->setCellValue('B4', implode(' | ', $contact));
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Garis kop
        $sheet->getRowDimension(5)->setRowHeight(4);
        $sheet->getStyle('A5:F5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);

        // Judul
        $sheet->getRowDimension(6)->setRowHeight(24);
        $sheet->mergeCells('A6:F6');
        $sheet->setCellValue('A6', 'MASTER DATA SUMBER DANA');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0A1317']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Sub-judul
        $sheet->mergeCells('A7:F7');
        $subtitle = 'Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB';
        if (!empty($this->filters['search']) || isset($this->filters['is_active'])) {
            $subtitle = 'Dengan filter diterapkan — ' . $subtitle;
        }
        $sheet->setCellValue('A7', $subtitle);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Ringkasan
        $sheet->getRowDimension(8)->setRowHeight(28);
        $sheet->mergeCells('A8:F8');

        $query = FundingSource::query()
            ->when(!empty($this->filters['search']), fn($q) => $q->search($this->filters['search']))
            ->when(isset($this->filters['is_active']) && $this->filters['is_active'] !== '', 
                fn($q) => $q->where('is_active', (bool) $this->filters['is_active']));

        $total = (clone $query)->count();
        $active = (clone $query)->where('is_active', true)->count();

        $summaryText = sprintf(
            'Total: %s   |   Aktif: %s   |   Nonaktif: %s',
            number_format($total),
            number_format($active),
            number_format($total - $active)
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

    protected function buildTableHeader(Worksheet $sheet): void
    {
        $headers = ['No', 'Kode', 'Nama', 'Deskripsi', 'Status', 'Jumlah Barang'];

        $row = self::HEADER_ROW;
        $sheet->getRowDimension($row)->setRowHeight(30);

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
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

    protected function buildTableData(Worksheet $sheet): int
    {
        $row = self::HEADER_ROW + 1;

        $sources = FundingSource::query()
            ->withCount('items')
            ->when(!empty($this->filters['search']), fn($q) => $q->search($this->filters['search']))
            ->when(isset($this->filters['is_active']) && $this->filters['is_active'] !== '', 
                fn($q) => $q->where('is_active', (bool) $this->filters['is_active']))
            ->orderBy('code')
            ->get();

        if ($sources->count() === 0) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->setCellValue("A{$row}", 'Tidak ada data sumber dana untuk filter yang dipilih.');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '8595A4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(40);

            return $row;
        }

        $no = 1;
        $startDataRow = $row;

        foreach ($sources as $source) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $source->code);
            $sheet->setCellValue("C{$row}", $source->name);
            $sheet->setCellValue("D{$row}", $source->description ?? '-');
            $sheet->setCellValue("E{$row}", $source->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue("F{$row}", $source->items_count);

            $row++;
        }

        $endDataRow = $row - 1;

        // Style body
        $sheet->getStyle("A{$startDataRow}:F{$endDataRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE3E9'],
                ],
            ],
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Alignment khusus
        $sheet->getStyle("A{$startDataRow}:A{$endDataRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$startDataRow}:F{$endDataRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Zebra
        for ($i = $startDataRow; $i <= $endDataRow; $i++) {
            if (($i - $startDataRow) % 2 === 1) {
                $sheet->getStyle("A{$i}:F{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
        }

        return $endDataRow;
    }

    protected function buildSignature(Worksheet $sheet, int $lastDataRow): void
    {
        $startRow = $lastDataRow + 3;

        $sheet->mergeCells("D{$startRow}:F{$startRow}");
        $sheet->setCellValue("D{$startRow}", now()->translatedFormat('d F Y'));
        $sheet->getStyle("D{$startRow}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = $startRow + 1;
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'Kepala Sekolah,');
        $sheet->getStyle("D{$row}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 4;

        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", setting('headmaster_name', '_______________________'));
        $sheet->getStyle("D{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0A1317'],
                ],
            ],
        ]);

        if (setting('headmaster_nip')) {
            $row++;
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", 'NIP. ' . setting('headmaster_nip'));
            $sheet->getStyle("D{$row}")->applyFromArray([
                'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Auto-size
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(40);
    }
}