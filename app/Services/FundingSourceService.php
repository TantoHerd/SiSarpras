<?php

namespace App\Services;

use App\Models\FundingSource;
use App\Repositories\FundingSourceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class FundingSourceService
{
    public function __construct(
        protected FundingSourceRepository $repository
    ) {}

    public function paginate(?string $search = null, ?string $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $isActive, $perPage);
    }

    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    public function allActive(): Collection
    {
        return $this->repository->allActive();
    }

    public function find(int $id): FundingSource
    {
        return $this->repository->find($id);
    }

    public function create(array $data): FundingSource
    {
        $data = $this->normalize($data);
        return $this->repository->create($data);
    }

    public function update(FundingSource $fundingSource, array $data): FundingSource
    {
        $data = $this->normalize($data);
        return $this->repository->update($fundingSource, $data);
    }

    public function delete(FundingSource $fundingSource): void
    {
        $count = $this->repository->countItems($fundingSource);

        if ($count > 0) {
            throw new RuntimeException(
                "Sumber dana \"{$fundingSource->name}\" masih digunakan oleh {$count} item. " .
                "Silakan nonaktifkan saja daripada menghapus."
            );
        }

        $this->repository->delete($fundingSource);
    }

    /**
     * Normalisasi input: code uppercase, trim string.
     */
    protected function normalize(array $data): array
    {
        if (isset($data['code'])) {
            $data['code'] = Str::upper(trim($data['code']));
        }

        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }

        if (isset($data['description'])) {
            $data['description'] = trim($data['description']) ?: null;
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }

    public function topByItemCount(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->topByItemCount($limit);
    }
}