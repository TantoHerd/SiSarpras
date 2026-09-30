<?php
// app/Repositories/LoanRepository.php

namespace App\Repositories;

use App\Models\Loan;
use App\Enums\LoanStatusEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LoanRepository
{
    /**
     * Daftar peminjaman dengan filter
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Loan::query()
            ->with(['item', 'item.category', 'borrower', 'processor'])
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('item', function ($item) use ($term) {
                        $item->where('name', 'ILIKE', "%{$term}%")
                             ->orWhere('code', 'ILIKE', "%{$term}%");
                    })
                    ->orWhereHas('borrower', function ($user) use ($term) {
                        $user->where('name', 'ILIKE', "%{$term}%")
                             ->orWhere('nip', 'ILIKE', "%{$term}%");
                    });
                });
            })
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['borrower_id']), fn($q) => $q->where('borrower_id', $filters['borrower_id']))
            ->when(!empty($filters['date_from']), fn($q) => $q->where('loan_date', '>=', $filters['date_from']))
            ->when(!empty($filters['date_to']), fn($q) => $q->where('loan_date', '<=', $filters['date_to']))
            ->latest('loan_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Cari loan berdasarkan ID
     */
    public function findById(int $id): ?Loan
    {
        return Loan::with(['item', 'item.category', 'item.location', 'borrower', 'processor'])
            ->find($id);
    }

    /**
     * Simpan loan baru
     */
    public function create(array $data): Loan
    {
        return Loan::create($data);
    }

    /**
     * Update loan
     */
    public function update(Loan $loan, array $data): Loan
    {
        $loan->update($data);
        return $loan->fresh();
    }

    /**
     * Cek apakah user punya pinjaman aktif untuk item tertentu
     */
    public function hasActiveLoan(int $userId, int $itemId): bool
    {
        return Loan::where('borrower_id', $userId)
            ->where('item_id', $itemId)
            ->whereIn('status', [LoanStatusEnum::DIPINJAM, LoanStatusEnum::TERLAMBAT])
            ->exists();
    }

    /**
     * Hitung jumlah pinjaman aktif user
     */
    public function countActiveByUser(int $userId): int
    {
        return Loan::where('borrower_id', $userId)
            ->whereIn('status', [LoanStatusEnum::DIPINJAM, LoanStatusEnum::TERLAMBAT])
            ->count();
    }

    /**
     * Ambil pinjaman aktif user (untuk dashboard guru)
     */
    public function getActiveByUser(int $userId, int $limit = 5)
    {
        return Loan::with('item')
            ->where('borrower_id', $userId)
            ->whereIn('status', [LoanStatusEnum::DIPINJAM, LoanStatusEnum::TERLAMBAT])
            ->latest('loan_date')
            ->take($limit)
            ->get();
    }

    /**
     * Ambil semua pinjaman aktif (untuk dashboard admin/petugas)
     */
    public function getAllActive(int $limit = 5)
    {
        return Loan::with(['item', 'borrower'])
            ->whereIn('status', [LoanStatusEnum::DIPINJAM, LoanStatusEnum::TERLAMBAT])
            ->latest('loan_date')
            ->take($limit)
            ->get();
    }

    /**
     * Cek pinjaman yang terlambat (untuk auto-update status)
     */
    public function getOverdue()
    {
        return Loan::where('status', LoanStatusEnum::DIPINJAM)
            ->where('due_date', '<', now())
            ->get();
    }

    /**
     * Statistik peminjaman
     */
    public function getStats(): array
    {
        return [
            'total'       => Loan::count(),
            'active'      => Loan::whereIn('status', [LoanStatusEnum::DIPINJAM, LoanStatusEnum::TERLAMBAT])->count(),
            'overdue'     => Loan::where('status', LoanStatusEnum::TERLAMBAT)->count(),
            'returned'    => Loan::where('status', LoanStatusEnum::DIKEMBALIKAN)->count(),
            'returned_today' => Loan::where('status', LoanStatusEnum::DIKEMBALIKAN)
                ->whereDate('return_date', today())
                ->count(),
        ];
    }
}