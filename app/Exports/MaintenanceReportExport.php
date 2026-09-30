<?php
// app/Exports/MaintenanceReportExport.php

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

class MaintenanceReportExport implements FromCollection, WithEvents, WithTitle
{
    protected $maintenances;
    protected $summary;
    protected $filters;

    protected const HEADER_ROW = 9;
    protected const TOTAL_COLUMNS = 'I';

    public function __construct($maintenances, $summary, $filters = [])
    {
        $this->maintenances = $maintenances;
        $this->summary = $summary;
        $this->filters = $filters;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Laporan Perawatan';
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
        $logoFile = setting('school_logo', 'logo-default.png');
        $logoPath = storage_path('app/public/' . $logoFile);
        $logoExists = $logoFile && $logoFile !== 'logo-default.png' && file_exists($logoPath);

        $sheet->getRowDimension(2)->setRowHeight(28);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(16);

        if ($logoExists) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
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

        $sheet->mergeCells('B2:I2');
        $sheet->setCellValue('B2', strtoupper(setting('school_name', 'NAMA SEKOLAH')));
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('B3:I3');
        $sheet->setCellValue('B3', setting('school_address', 'Alamat Sekolah'));
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $contact = [];
        if (setting('school_phone')) $contact[] = 'Telp: ' . setting('school_phone');
        if (setting('school_email')) $contact[] = 'Email: ' . setting('school_email');
        if (setting('school_npsn'))  $contact[] = 'NPSN: ' . setting('school_npsn');

        $sheet->mergeCells('B4:I4');
        $sheet->setCellValue('B4', implode(' | ', $contact));
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getRowDimension(5)->setRowHeight(4);
        $sheet->getStyle('A5:I5')->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0A1317']]],
        ]);

        $sheet->getRowDimension(6)->setRowHeight(24);
        $sheet->mergeCells('A6:I6');
        $sheet->setCellValue('A6', 'LAPORAN PERAWATAN BARANG');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $periode = 'Periode: ';
        $periode .= !empty($this->filters['date_from']) 
            ? \Carbon\Carbon::parse($this->filters['date_from'])->translatedFormat('d F Y') 
            : 'Semua';
        $periode .= ' — ';
        $periode .= !empty($this->filters['date_to']) 
            ? \Carbon\Carbon::parse($this->filters['date_to'])->translatedFormat('d F Y') 
            : 'Semua';
        $periode .= '  |  Dicetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB';

        $sheet->mergeCells('A7:I7');
        $sheet->setCellValue('A7', $periode);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '5D6C7B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(8)->setRowHeight(28);
        $sheet->mergeCells('A8:I8');
        $summaryText = sprintf(
            'Total: %s   |   Rutin: %s   |   Perbaikan: %s   |   Proses: %s   |   Total Biaya: %s',
            number_format($this->summary['total']),
            number_format($this->summary['routine']),
            number_format($this->summary['repair']),
            number_format($this->summary['in_progress']),
            format_currency($this->summary['total_cost'])
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
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Tanggal', 'Jenis', 'Teknisi', 'Deskripsi', 'Status', 'Biaya'];

        $row = self::HEADER_ROW;
        $sheet->getRowDimension($row)->setRowHeight(30);

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }

        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);
    }

    protected function buildTableData(Worksheet $sheet): int
    {
        $row = self::HEADER_ROW + 1;

        if ($this->maintenances->count() === 0) {
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("A{$row}", 'Tidak ada data perawatan untuk periode yang dipilih.');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '8595A4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(40);
            return $row;
        }

        $no = 1;
        $startDataRow = $row;

        foreach ($this->maintenances as $maintenance) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $maintenance->item->code ?? '-');
            $sheet->setCellValue("C{$row}", $maintenance->item->name ?? '-');
            $sheet->setCellValue("D{$row}", $maintenance->maintenance_date->format('d/m/Y'));
            $sheet->setCellValue("E{$row}", $maintenance->type === 'rutin' ? 'Rutin' : 'Perbaikan');
            $sheet->setCellValue("F{$row}", $maintenance->technician ?? '-');
            $sheet->setCellValue("G{$row}", $maintenance->description ?? '-');
            $sheet->setCellValue("H{$row}", $maintenance->status === 'in_progress' ? 'Sedang Proses' : 'Selesai');
            $sheet->setCellValue("I{$row}", (float) $maintenance->cost);
            $row++;
        }

        $endDataRow = $row - 1;

        $sheet->getStyle("A{$startDataRow}:I{$endDataRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE3E9']]],
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]);

        $sheet->getStyle("A{$startDataRow}:A{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$startDataRow}:F{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$startDataRow}:H{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I{$startDataRow}:I{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("I{$startDataRow}:I{$endDataRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Zebra
        for ($i = $startDataRow; $i <= $endDataRow; $i++) {
            if (($i - $startDataRow) % 2 === 1) {
                $sheet->getStyle("A{$i}:I{$i}")->getFill()
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

        $sheet->mergeCells("A{$totalRow}:H{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL BIAYA PERAWATAN');
        $sheet->getStyle("A{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setCellValue("I{$totalRow}", (float) $this->summary['total_cost']);
        $sheet->getStyle("I{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A1317']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);

        return $totalRow;
    }

    protected function buildSignature(Worksheet $sheet, int $totalRow): void
    {
        $startRow = $totalRow + 3;

        $sheet->mergeCells("F{$startRow}:I{$startRow}");
        $sheet->setCellValue("F{$startRow}", now()->translatedFormat('d F Y'));
        $sheet->getStyle("F{$startRow}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = $startRow + 1;
        $sheet->mergeCells("F{$row}:I{$row}");
        $sheet->setCellValue("F{$row}", 'Kepala Sekolah,');
        $sheet->getStyle("F{$row}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '1C1E21']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 4;

        $sheet->mergeCells("F{$row}:I{$row}");
        $sheet->setCellValue("F{$row}", setting('headmaster_name', '_______________________'));
        $sheet->getStyle("F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0A1317']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A1317']]],
        ]);

        if (setting('headmaster_nip')) {
            $row++;
            $sheet->mergeCells("F{$row}:I{$row}");
            $sheet->setCellValue("F{$row}", 'NIP. ' . setting('headmaster_nip'));
            $sheet->getStyle("F{$row}")->applyFromArray([
                'font' => ['size' => 9, 'color' => ['rgb' => '5D6C7B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(15);
    }
}