<?php

namespace App\Repositories;

use App\Models\FundingSource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FundingSourceRepository
{
    public function __construct(
        protected FundingSource $model
    ) {}

    public function paginate(?string $search = null, ?string $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->withCount('items')
            ->when($search, fn ($q) => $q->search($search))
            ->when($isActive !== null && $isActive !== '', function ($q) use ($isActive) {
                $q->where('is_active', (bool) $isActive);
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total'  => $this->model->count(),
            'active' => $this->model->where('is_active', true)->count(),
            'empty'  => $this->model->whereDoesntHave('items')->count(),
        ];
    }

    public function allActive(): Collection
    {
        return $this->model->active()->orderBy('code')->get();
    }

    public function find(int $id): FundingSource
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): FundingSource
    {
        return $this->model->create($data);
    }

    public function update(FundingSource $fundingSource, array $data): FundingSource
    {
        $fundingSource->update($data);
        return $fundingSource->fresh();
    }

    public function delete(FundingSource $fundingSource): bool
    {
        return $fundingSource->delete();
    }

    public function countItems(FundingSource $fundingSource): int
    {
        return $fundingSource->items()->count();
    }

    /**
     * Top N sumber dana berdasarkan jumlah aset
     */
    public function topByItemCount(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->withCount('items')
            ->has('items')  // ← hanya yang punya item
            ->orderByDesc('items_count')
            ->limit($limit)
            ->get();
    }
}