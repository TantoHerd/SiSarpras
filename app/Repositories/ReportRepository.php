<?php
// app/Repositories/ReportRepository.php

namespace App\Repositories;

use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Maintenance;
use Carbon\Carbon;

class ReportRepository
{
    /**
     * ==================== LAPORAN INVENTARIS BARANG ====================
     */
    public function getInventoryData(array $filters = [])
    {
        $query = Item::query()
            ->with(['category', 'location', 'supplier'])
            ->when(!empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(!empty($filters['location_id']), fn($q) => $q->where('location_id', $filters['location_id']))
            ->when(!empty($filters['condition']), fn($q) => $q->where('condition', $filters['condition']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->orderBy('category_id')
            ->orderBy('name');

        return $query->get();
    }

    /**
     * Ringkasan inventaris
     */
    public function getInventorySummary(array $filters = []): array
    {
        $query = Item::query()
            ->when(!empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(!empty($filters['location_id']), fn($q) => $q->where('location_id', $filters['location_id']));

        $allItems = (clone $query)->get();

        return [
            'total_items'     => $allItems->count(),
            'total_quantity'  => $allItems->sum('quantity'),
            'total_value'     => $allItems->sum(fn($item) => $item->price * $item->quantity),
            'by_condition'    => [
                'baik'          => (clone $query)->where('condition', ItemConditionEnum::BAIK)->count(),
                'rusak_ringan'  => (clone $query)->where('condition', ItemConditionEnum::RUSAK_RINGAN)->count(),
                'rusak_berat'   => (clone $query)->where('condition', ItemConditionEnum::RUSAK_BERAT)->count(),
            ],
            'by_status'       => [
                'tersedia'    => (clone $query)->where('status', ItemStatusEnum::TERSEDIA)->count(),
                'dipinjam'    => (clone $query)->where('status', ItemStatusEnum::DIPINJAM)->count(),
                'perbaikan'   => (clone $query)->where('status', ItemStatusEnum::PERBAIKAN)->count(),
                'tidak_aktif' => (clone $query)->where('status', ItemStatusEnum::TIDAK_AKTIF)->count(),
            ],
        ];
    }

    /**
     * ==================== LAPORAN PEMINJAMAN ====================
     */
    public function getLoanData(array $filters = [])
    {
        return Loan::query()
            ->with(['item', 'item.category', 'borrower', 'processor'])
            ->when(!empty($filters['date_from']), fn($q) => $q->where('loan_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('loan_date', '<=', Carbon::parse($filters['date_to'])->endOfDay()))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['borrower_id']), fn($q) => $q->where('borrower_id', $filters['borrower_id']))
            ->latest('loan_date')
            ->get();
    }

    /**
     * Ringkasan peminjaman
     */
    public function getLoanSummary(array $filters = []): array
    {
        $query = Loan::query()
            ->when(!empty($filters['date_from']), fn($q) => $q->where('loan_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('loan_date', '<=', Carbon::parse($filters['date_to'])->endOfDay()));

        return [
            'total'         => (clone $query)->count(),
            'borrowed'      => (clone $query)->where('status', LoanStatusEnum::DIPINJAM)->count(),
            'overdue'       => (clone $query)->where('status', LoanStatusEnum::TERLAMBAT)->count(),
            'returned'      => (clone $query)->where('status', LoanStatusEnum::DIKEMBALIKAN)->count(),
            'total_fines'   => (clone $query)->sum('fine_amount'),
            'fines_paid'    => (clone $query)->where('is_fine_paid', true)->sum('fine_amount'),
            'fines_unpaid'  => (clone $query)->where('is_fine_paid', false)->where('fine_amount', '>', 0)->sum('fine_amount'),
        ];
    }

    /**
     * ==================== LAPORAN PERAWATAN ====================
     */
    public function getMaintenanceData(array $filters = [])
    {
        return Maintenance::query()
            ->with(['item', 'item.category', 'item.location', 'creator', 'completer'])
            ->when(!empty($filters['date_from']), fn($q) => $q->where('maintenance_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('maintenance_date', '<=', Carbon::parse($filters['date_to'])->endOfDay()))
            ->when(!empty($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->latest('maintenance_date')
            ->get();
    }

    /**
     * Ringkasan perawatan
     */
    public function getMaintenanceSummary(array $filters = []): array
    {
        $query = Maintenance::query()
            ->when(!empty($filters['date_from']), fn($q) => $q->where('maintenance_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('maintenance_date', '<=', Carbon::parse($filters['date_to'])->endOfDay()));

        return [
            'total'         => (clone $query)->count(),
            'routine'       => (clone $query)->where('type', 'rutin')->count(),
            'repair'        => (clone $query)->where('type', 'perbaikan')->count(),
            'in_progress'   => (clone $query)->where('status', 'in_progress')->count(),
            'completed'     => (clone $query)->where('status', 'completed')->count(),
            'total_cost'    => (clone $query)->sum('cost'),
            'avg_cost'      => (clone $query)->avg('cost') ?? 0,
        ];
    }
}