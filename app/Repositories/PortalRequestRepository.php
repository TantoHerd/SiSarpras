<?php
// app/Repositories/PortalRequestRepository.php

namespace App\Repositories;

use App\Models\PortalRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PortalRequestRepository
{
    public function __construct(
        protected PortalRequest $model
    ) {}

    /**
     * Paginate untuk petugas
     */
    public function paginate(?string $status = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['item', 'student', 'processor'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nis', 'ILIKE', "%{$search}%")
                        ->orWhere('student_name', 'ILIKE', "%{$search}%")
                        ->orWhere('student_class', 'ILIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Riwayat permintaan berdasarkan NIS (untuk siswa cek status)
     */
    public function findByNis(string $nis): Collection
    {
        return $this->model
            ->with(['item', 'loan'])
            ->byNis($nis)
            ->latest()
            ->limit(20)
            ->get();
    }

    public function find(int $id): PortalRequest
    {
        return $this->model->with(['item', 'student', 'processor', 'loan'])->findOrFail($id);
    }

    public function create(array $data): PortalRequest
    {
        return $this->model->create($data);
    }

    public function update(PortalRequest $request, array $data): PortalRequest
    {
        $request->update($data);
        return $request->fresh();
    }

    /**
     * Count pending requests (untuk badge sidebar)
     */
    public function countPending(): int
    {
        return $this->model->pending()->count();
    }

    /**
     * Cek apakah siswa sudah punya pending request untuk item yang sama
     */
    public function hasPendingForItem(string $nis, int $itemId): bool
    {
        return $this->model
            ->byNis($nis)
            ->where('item_id', $itemId)
            ->pending()
            ->exists();
    }

    /**
     * Count pending untuk siswa (max loan check)
     */
    public function countActiveByNis(string $nis): int
    {
        return $this->model
            ->byNis($nis)
            ->whereIn('status', ['pending', 'approved'])
            ->count();
    }

    /**
     * Stats untuk dashboard petugas
     */
    public function getStats(): array
    {
        return [
            'pending'  => $this->model->where('status', 'pending')->count(),
            'approved' => $this->model->where('status', 'approved')->count(),
            'rejected' => $this->model->where('status', 'rejected')->count(),
            'today'    => $this->model->whereDate('created_at', today())->count(),
        ];
    }
}