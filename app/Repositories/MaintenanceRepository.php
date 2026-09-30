<?php
// app/Repositories/MaintenanceRepository.php

namespace App\Repositories;

use App\Models\Maintenance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintenanceRepository
{
    /**
     * Daftar maintenance dengan filter & pagination
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Maintenance::query()
            ->with(['item', 'item.category', 'item.location', 'creator'])
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('item', function ($item) use ($term) {
                        $item->where('name', 'ILIKE', "%{$term}%")
                             ->orWhere('code', 'ILIKE', "%{$term}%");
                    })
                    ->orWhere('technician', 'ILIKE', "%{$term}%")
                    ->orWhere('description', 'ILIKE', "%{$term}%");
                });
            })
            ->when(!empty($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(!empty($filters['date_from']), fn($q) => $q->where('maintenance_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('maintenance_date', '<=', $filters['date_to']))
            ->latest('maintenance_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Cari maintenance by ID
     */
    public function findById(int $id): ?Maintenance
    {
        return Maintenance::with(['item', 'item.category', 'item.location', 'creator'])
            ->find($id);
    }

    /**
     * Simpan maintenance baru
     */
    public function create(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    /**
     * Update maintenance
     */
    public function update(Maintenance $maintenance, array $data): Maintenance
    {
        $maintenance->update($data);
        return $maintenance->fresh();
    }

    /**
     * Soft delete maintenance
     */
    public function delete(Maintenance $maintenance): bool
    {
        return $maintenance->delete();
    }

    /**
     * Ambil maintenance per item (untuk halaman detail item)
     */
    public function getByItem(int $itemId, int $limit = 10)
    {
        return Maintenance::with('creator')
            ->where('item_id', $itemId)
            ->latest('maintenance_date')
            ->take($limit)
            ->get();
    }

    /**
     * Jadwal maintenance berikutnya yang mendekati jatuh tempo
     * (untuk notifikasi dashboard)
     */
    public function getUpcoming(int $days = 7, int $limit = 5)
    {
        return Maintenance::with(['item', 'item.location'])
            ->whereNotNull('next_maintenance_date')
            ->whereBetween('next_maintenance_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->orderBy('next_maintenance_date')
            ->take($limit)
            ->get();
    }

    /**
     * Hitung jadwal maintenance yang mendekati jatuh tempo
     */
    public function countUpcoming(int $days = 7): int
    {
        return Maintenance::whereNotNull('next_maintenance_date')
            ->whereBetween('next_maintenance_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->count();
    }

    /**
     * Statistik maintenance
     */
    public function getStats(): array
    {
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        return [
            'total'         => Maintenance::count(),
            'this_month'    => Maintenance::where('maintenance_date', '>=', $thisMonth)->count(),
            'last_month'    => Maintenance::whereBetween('maintenance_date', [$lastMonth, $lastMonthEnd])->count(),
            'routine'       => Maintenance::where('type', 'rutin')->count(),
            'repair'        => Maintenance::where('type', 'perbaikan')->count(),
            'total_cost'    => Maintenance::sum('cost'),
            'cost_this_month' => Maintenance::where('maintenance_date', '>=', $thisMonth)->sum('cost'),
            'upcoming'      => $this->countUpcoming(7),
        ];
    }

    /**
     * Statistik maintenance per item
     */
    public function getStatsByItem(int $itemId): array
    {
        $maintenances = Maintenance::where('item_id', $itemId);

        return [
            'total'      => $maintenances->count(),
            'routine'    => (clone $maintenances)->where('type', 'rutin')->count(),
            'repair'     => (clone $maintenances)->where('type', 'perbaikan')->count(),
            'total_cost' => (clone $maintenances)->sum('cost'),
            'last_date'  => (clone $maintenances)->latest('maintenance_date')->value('maintenance_date'),
        ];
    }
}