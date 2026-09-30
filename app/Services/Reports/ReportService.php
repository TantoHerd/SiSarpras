<?php
// app/Services/Reports/ReportService.php

namespace App\Services\Reports;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(
        protected ReportRepository $repository
    ) {}

    /**
     * Data untuk halaman index laporan
     */
    public function getIndexData(): array
    {
        return [
            'inventory_total'   => \App\Models\Item::count(),
            'loan_total'        => \App\Models\Loan::count(),
            'maintenance_total' => \App\Models\Maintenance::count(),
            'loan_active'       => \App\Models\Loan::whereIn('status', ['dipinjam', 'terlambat'])->count(),
            'maintenance_in_progress' => \App\Models\Maintenance::where('status', 'in_progress')->count(),
        ];
    }

    // Shortcut methods
    public function inventory(array $filters = [])
    {
        return $this->repository->getInventoryData($filters);
    }

    public function inventorySummary(array $filters = []): array
    {
        return $this->repository->getInventorySummary($filters);
    }

    public function loans(array $filters = [])
    {
        return $this->repository->getLoanData($filters);
    }

    public function loanSummary(array $filters = []): array
    {
        return $this->repository->getLoanSummary($filters);
    }

    public function maintenances(array $filters = [])
    {
        return $this->repository->getMaintenanceData($filters);
    }

    public function maintenanceSummary(array $filters = []): array
    {
        return $this->repository->getMaintenanceSummary($filters);
    }
}