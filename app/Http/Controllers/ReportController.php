<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Exports\InventoryReportExport;
use App\Exports\LoanReportExport;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\Reports\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\FundingSourceService;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected FundingSourceService $fundingSourceService,
    ) {}

    /**
     * Halaman index laporan
     */
    public function index()
    {
        $data = $this->reportService->getIndexData();
        return view('reports.index', $data);
    }

    /**
     * ==================== LAPORAN INVENTARIS ====================
     */
    public function inventory(Request $request)
    {
        $filters = [
            'category_id'       => $request->input('category_id'),
            'location_id'       => $request->input('location_id'),
            'condition'         => $request->input('condition'),
            'status'            => $request->input('status'),
            'funding_source_id' => $request->input('funding_source_id'),  // ← BARU
        ];

        $items          = $this->reportService->inventory($filters);
        $summary        = $this->reportService->inventorySummary($filters);
        $categories     = Category::orderBy('name')->get();
        $locations      = Location::orderBy('name')->get();
        $fundingSources = $this->fundingSourceService->allActive();  // ← BARU

        return view('reports.inventory', compact(
            'items', 'summary', 'categories', 'locations', 'fundingSources', 'filters'
        ));
    }

    /**
     * ==================== LAPORAN PEMINJAMAN ====================
     */
    public function loans(Request $request)
    {
        $filters = [
            'date_from'   => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to'     => $request->input('date_to', now()->format('Y-m-d')),
            'status'      => $request->input('status'),
            'borrower_id' => $request->input('borrower_id'),
        ];

        $loans = $this->reportService->loans($filters);
        $summary = $this->reportService->loanSummary($filters);

        return view('reports.loans', compact('loans', 'summary', 'filters'));
    }

    /**
     * ==================== LAPORAN PERAWATAN ====================
     */
    public function maintenances(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to'   => $request->input('date_to', now()->format('Y-m-d')),
            'type'      => $request->input('type'),
            'status'    => $request->input('status'),
        ];

        $maintenances = $this->reportService->maintenances($filters);
        $summary = $this->reportService->maintenanceSummary($filters);

        return view('reports.maintenances', compact('maintenances', 'summary', 'filters'));
    }

    /**
     * ==================== EXPORT INVENTARIS ====================
     */

    /**
     * Export PDF Laporan Inventaris
     */
    public function inventoryPdf(Request $request)
    {
        $filters = [
            'category_id'       => $request->input('category_id'),
            'location_id'       => $request->input('location_id'),
            'condition'         => $request->input('condition'),
            'status'            => $request->input('status'),
            'funding_source_id' => $request->input('funding_source_id'),  // ← BARU
        ];

        $items   = $this->reportService->inventory($filters);
        $summary = $this->reportService->inventorySummary($filters);
        
        // ← BARU: ambil nama sumber dana yang dipilih untuk header PDF
        $selectedSource = null;
        if (!empty($filters['funding_source_id'])) {
            $selectedSource = $this->fundingSourceService->find($filters['funding_source_id']);
        }

        $pdf = Pdf::loadView('reports.pdf.inventory', compact('items', 'summary', 'filters', 'selectedSource'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $filename = 'Laporan-Inventaris-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel Laporan Inventaris
     */
    public function inventoryExcel(Request $request)
    {
        $filters = [
            'category_id'       => $request->input('category_id'),
            'location_id'       => $request->input('location_id'),
            'condition'         => $request->input('condition'),
            'status'            => $request->input('status'),
            'funding_source_id' => $request->input('funding_source_id'),  // ← BARU
        ];

        $items   = $this->reportService->inventory($filters);
        $summary = $this->reportService->inventorySummary($filters);

        $filename = 'Laporan-Inventaris-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new InventoryReportExport($items, $summary, $filters), $filename);
    }

    /**
     * ==================== EXPORT PEMINJAMAN ====================
     */

    /**
     * Export PDF Laporan Peminjaman
     */
    public function loansPdf(Request $request)
    {
        $filters = [
            'date_from'   => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to'     => $request->input('date_to', now()->format('Y-m-d')),
            'status'      => $request->input('status'),
            'borrower_id' => $request->input('borrower_id'),
        ];

        $loans = $this->reportService->loans($filters);
        $summary = $this->reportService->loanSummary($filters);

        $pdf = Pdf::loadView('reports.pdf.loans', compact('loans', 'summary', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $filename = 'Laporan-Peminjaman-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel Laporan Peminjaman
     */
    public function loansExcel(Request $request)
    {
        $filters = [
            'date_from'   => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to'     => $request->input('date_to', now()->format('Y-m-d')),
            'status'      => $request->input('status'),
            'borrower_id' => $request->input('borrower_id'),
        ];

        $loans = $this->reportService->loans($filters);
        $summary = $this->reportService->loanSummary($filters);

        $filename = 'Laporan-Peminjaman-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new \App\Exports\LoanReportExport($loans, $summary, $filters), $filename);
    }

    /**
 * ==================== EXPORT PERAWATAN ====================
 */

/**
 * Export PDF Laporan Perawatan
 */
public function maintenancesPdf(Request $request)
{
    $filters = [
        'date_from' => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
        'date_to'   => $request->input('date_to', now()->format('Y-m-d')),
        'type'      => $request->input('type'),
        'status'    => $request->input('status'),
    ];

    $maintenances = $this->reportService->maintenances($filters);
    $summary = $this->reportService->maintenanceSummary($filters);

    $pdf = Pdf::loadView('reports.pdf.maintenances', compact('maintenances', 'summary', 'filters'))
        ->setPaper('a4', 'landscape')
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);

    $filename = 'Laporan-Perawatan-' . now()->format('Ymd-His') . '.pdf';

    return $pdf->download($filename);
}

/**
 * Export Excel Laporan Perawatan
 */
public function maintenancesExcel(Request $request)
{
    $filters = [
        'date_from' => $request->input('date_from', now()->startOfMonth()->format('Y-m-d')),
        'date_to'   => $request->input('date_to', now()->format('Y-m-d')),
        'type'      => $request->input('type'),
        'status'    => $request->input('status'),
    ];

    $maintenances = $this->reportService->maintenances($filters);
    $summary = $this->reportService->maintenanceSummary($filters);

    $filename = 'Laporan-Perawatan-' . now()->format('Ymd-His') . '.xlsx';

    return Excel::download(new \App\Exports\MaintenanceReportExport($maintenances, $summary, $filters), $filename);
}
    
}