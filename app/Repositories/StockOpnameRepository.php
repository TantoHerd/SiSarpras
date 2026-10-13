<?php
// app/Repositories/StockOpnameRepository.php

namespace App\Repositories;

use App\Models\StockOpname;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockOpnameRepository
{
    public function __construct(
        protected StockOpname $model
    ) {}

    /**
     * Paginate untuk index
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['location', 'starter', 'completer'])
            ->withCount('items')
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['location_id']), fn($q) => $q->where('location_id', $filters['location_id']))
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where('opname_number', 'ILIKE', "%{$term}%")
                  ->orWhereHas('location', fn($l) => $l->where('name', 'ILIKE', "%{$term}%"));
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find detail dengan relasi lengkap
     */
    public function find(int $id): StockOpname
    {
        return $this->model
            ->with([
                'location',
                'starter',
                'completer',
                'items.item',
                'items.item.category',
            ])
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): StockOpname
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(StockOpname $stockOpname, array $data): StockOpname
    {
        $stockOpname->update($data);
        return $stockOpname->fresh();
    }

    /**
     * Delete
     */
    public function delete(StockOpname $stockOpname): bool
    {
        return $stockOpname->delete();
    }

    /**
     * Cek apakah ada sesi draft untuk lokasi
     */
    public function hasDraftForLocation(int $locationId): bool
    {
        return $this->model
            ->where('location_id', $locationId)
            ->draft()
            ->exists();
    }

    /**
     * Stats
     */
    public function getStats(): array
    {
        return [
            'total'     => $this->model->count(),
            'draft'     => $this->model->where('status', 'draft')->count(),
            'completed' => $this->model->where('status', 'completed')->count(),
            'today'     => $this->model->whereDate('created_at', today())->count(),
        ];
    }
}