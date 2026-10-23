<?php
// app/Repositories/StockMovementRepository.php

namespace App\Repositories;

use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockMovementRepository
{
    public function __construct(
        protected StockMovement $model
    ) {}

    /**
     * Paginate untuk index
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->model
            ->with(['item', 'item.category', 'creator'])
            ->when(!empty($filters['item_id']), fn($q) => $q->where('item_id', $filters['item_id']))
            ->when(!empty($filters['type']), fn($q) => $q->where('type', $filters['type']))
            // ↓ TAMBAHKAN INI
            ->when(!empty($filters['date_from']), fn($q) => $q->where('created_at', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay()))
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->whereHas('item', function ($sub) use ($filters) {
                    $term = $filters['search'];
                    $sub->where('name', 'ILIKE', "%{$term}%")
                        ->orWhere('code', 'ILIKE', "%{$term}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Riwayat movement untuk item tertentu
     */
    public function forItem(int $itemId, int $limit = 20): Collection
    {
        return $this->model
            ->with('creator')
            ->where('item_id', $itemId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Find detail
     */
    public function find(int $id): StockMovement
    {
        return $this->model
            ->with(['item', 'item.category', 'creator'])
            ->findOrFail($id);
    }

    /**
     * Create movement
     */
    public function create(array $data): StockMovement
    {
        return $this->model->create($data);
    }

        /**
     * Stats untuk dashboard widget
     */
        public function getStats(): array
    {
        $today = now()->startOfDay();

        return [
            'total'         => $this->model->count(),
            'total_in'      => $this->model->where('type', 'in')->count(),
            'total_out'     => $this->model->where('type', 'out')->count(),
            'total_adjust'  => $this->model->where('type', 'adjustment')->count(),
            
            // Tetap pertahankan yang ini jika Anda ingin menampilkannya nanti
            'today'         => $this->model->where('created_at', '>=', $today)->count(),
            'in_today'      => $this->model->where('created_at', '>=', $today)->where('type', 'in')->count(),
            'out_today'     => $this->model->where('created_at', '>=', $today)->where('type', 'out')->count(),
            'adjust_today'  => $this->model->where('created_at', '>=', $today)->where('type', 'adjustment')->count(),
        ];
    }

    /**
     * Recent movements (untuk dashboard)
     */
    public function recent(int $limit = 5): Collection
    {
        return $this->model
            ->with(['item', 'creator'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}