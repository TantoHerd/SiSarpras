<?php
// app/Repositories/StockRequestRepository.php

namespace App\Repositories;

use App\Models\StockRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockRequestRepository
{
    public function __construct(
        protected StockRequest $model
    ) {}

    /**
     * Paginate dengan filter
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['requester', 'processor', 'items.item'])
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['requester_id']), fn($q) => $q->where('requester_id', $filters['requester_id']))
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->where('request_number', 'ILIKE', "%{$term}%")
                        ->orWhere('purpose', 'ILIKE', "%{$term}%")
                        ->orWhereHas('requester', fn($r) => $r->where('name', 'ILIKE', "%{$term}%"));
                });
            })
            ->when(!empty($filters['date_from']), fn($q) => $q->where('created_at', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('created_at', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay()))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find detail dengan relasi lengkap
     */
    public function find(int $id): StockRequest
    {
        return $this->model
            ->with([
                'requester',
                'processor',
                'items.item',
                'items.item.category',
            ])
            ->findOrFail($id);
    }

    /**
     * Create
     */
    public function create(array $data): StockRequest
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(StockRequest $stockRequest, array $data): StockRequest
    {
        $stockRequest->update($data);
        return $stockRequest->fresh();
    }

    /**
     * Delete
     */
    public function delete(StockRequest $stockRequest): bool
    {
        return $stockRequest->delete();
    }

    /**
     * Count pending (untuk badge sidebar)
     */
    public function countPending(): int
    {
        return $this->model->pending()->count();
    }

    /**
     * Riwayat request user
     */
    public function forUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['items.item', 'processor'])
            ->where('requester_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Stats untuk dashboard
     */
    public function getStats(): array
    {
        return [
            'total'    => $this->model->count(),
            'pending'  => $this->model->where('status', 'pending')->count(),
            'approved' => $this->model->where('status', 'approved')->count(),
            'rejected' => $this->model->where('status', 'rejected')->count(),
            'today'    => $this->model->whereDate('created_at', today())->count(),
        ];
    }
}