<?php
// app/Services/LoanService.php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use App\Enums\ItemStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Repositories\ItemRepository;
use App\Repositories\LoanRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(
        protected LoanRepository $loanRepository,
        protected ItemRepository $itemRepository,
        protected SettingService $settingService
    ) {}

    /**
     * Ambil daftar loan (dengan filter)
     */
    public function getLoans(array $filters = [], int $perPage = 15)
    {
        return $this->loanRepository->paginate($filters, $perPage);
    }

    /**
     * Ambil detail loan
     */
    public function getLoan(int $id): ?Loan
    {
        return $this->loanRepository->findById($id);
    }

    /**
     * Ajukan peminjaman baru
     */
    public function createLoan(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            // Lock item untuk mencegah race condition stok
            $item = Item::with('category')->lockForUpdate()->find($data['item_id']);
            
            if (!$item) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang tidak ditemukan.',
                ]);
            }

            if (!$item->isAvailable()) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang ini tidak tersedia untuk dipinjam saat ini.',
                ]);
            }

            // ============ VALIDASI QUANTITY BERDASARKAN MODE ============
            $quantity = (int) ($data['quantity'] ?? 1);

            if ($item->isPerUnit()) {
                // Per unit: quantity harus 1
                if ($quantity !== 1) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Barang per unit hanya bisa dipinjam 1 unit per transaksi.',
                    ]);
                }
            } else {
                // Per batch: cek stok
                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Jumlah minimal 1.',
                    ]);
                }
                if ($quantity > $item->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok tidak mencukupi. Tersedia: {$item->quantity}, diminta: {$quantity}.",
                    ]);
                }
            }

            // Tentukan borrower
            $borrowerId = $data['borrower_id'] ?? Auth::id();
            $processedBy = $data['processed_by'] ?? Auth::id();

            // Cek pinjaman aktif untuk item yang sama
            if ($this->loanRepository->hasActiveLoan($borrowerId, $item->id)) {
                throw ValidationException::withMessages([
                    'item_id' => 'Peminjam sudah memiliki pinjaman aktif untuk barang ini.',
                ]);
            }

            // Cek batas maksimal pinjaman aktif per user
            $maxLoan = $this->settingService->getTyped('max_loan_per_user', 5);
            $activeCount = $this->loanRepository->countActiveByUser($borrowerId);
            if ($activeCount >= $maxLoan) {
                throw ValidationException::withMessages([
                    'item_id' => "Peminjam sudah mencapai batas maksimal {$maxLoan} pinjaman aktif.",
                ]);
            }

            // Hitung tanggal
            $maxDays = $this->settingService->getTyped('max_loan_days', 7);
            $loanDate = Carbon::now();
            $dueDate = $loanDate->copy()->addDays($maxDays);

            // Simpan loan
            $loan = $this->loanRepository->create([
                'item_id'      => $item->id,
                'quantity'     => $quantity,  // ← BARU
                'borrower_id'  => $borrowerId,
                'processed_by' => $processedBy,
                'loan_date'    => $loanDate,
                'due_date'     => $dueDate,
                'status'       => LoanStatusEnum::DIPINJAM->value,
                'purpose'      => $data['purpose'] ?? null,
                'fine_amount'  => 0,
                'is_fine_paid' => false,
            ]);

            // ============ UPDATE STOK & STATUS ITEM ============
            if ($item->isPerBatch()) {
                // Per batch: kurangi stok
                $newQuantity = $item->quantity - $quantity;
                
                $this->itemRepository->update($item, [
                    'quantity' => $newQuantity,
                    // Kalau stok habis, status jadi tidak_aktif (atau tetap tersedia?)
                    'status' => $newQuantity <= 0 
                        ? ItemStatusEnum::TIDAK_AKTIF->value 
                        : $item->status->value,
                ]);
            } else {
                // Per unit: status jadi dipinjam
                $this->itemRepository->update($item, [
                    'status' => ItemStatusEnum::DIPINJAM->value,
                ]);
            }

            return $loan;
        });
    }

    /**
     * Proses pengembalian barang (dari sisi Petugas)
     */
    public function returnLoan(Loan $loan, array $data = []): Loan
    {
        // Cek sudah dikembalikan
        if ($loan->isReturned()) {
            throw ValidationException::withMessages([
                'loan' => 'Peminjaman ini sudah dikembalikan sebelumnya.',
            ]);
        }

        return DB::transaction(function () use ($loan, $data) {
            // Hitung denda (jika aktif)
            $fineAmount = 0;
            $isFineActive = $this->settingService->getTyped('is_fine_active', false);
            
            if ($isFineActive) {
                $finePerDay = $this->settingService->getTyped('fine_per_day', 0);
                $fineAmount = $loan->calculateFine($finePerDay);
            }

            // Cek apakah denda sudah dibayar (dari form)
            $isFinePaid = !empty($data['is_fine_paid']);

            // Update loan
            $updated = $this->loanRepository->update($loan, [
                'return_date' => now(),
                'status'      => LoanStatusEnum::DIKEMBALIKAN->value,
                'fine_amount' => $fineAmount,
                'is_fine_paid' => $isFinePaid,
            ]);

            // Update status & stok item
            if ($loan->item) {
                $item = $loan->item;

                if ($item->isPerBatch()) {
                    // Per batch: kembalikan stok
                    $newQuantity = $item->quantity + $loan->quantity;
                    
                    $this->itemRepository->update($item, [
                        'quantity' => $newQuantity,
                        // Stok kembali ada → status tersedia (kecuali kondisi rusak berat)
                        'status' => $item->condition->value === 'rusak_berat'
                            ? ItemStatusEnum::TIDAK_AKTIF->value
                            : ItemStatusEnum::TERSEDIA->value,
                    ]);
                } else {
                    // Per unit: status kembali tersedia
                    $this->itemRepository->update($item, [
                        'status' => ItemStatusEnum::TERSEDIA->value,
                    ]);
                }
            }

            // Log ke ItemHistory
            \App\Models\ItemHistory::create([
                'item_id'    => $loan->item_id,
                'user_id'    => Auth::id(),
                'action'     => 'returned',
                'old_value'  => ['status' => 'dipinjam', 'loan_id' => $loan->id],
                'new_value'  => [
                    'status'         => 'dikembalikan',
                    'loan_id'        => $loan->id,
                    'condition_note' => $data['condition_note'] ?? null,
                    'fine_amount'    => $fineAmount,
                    'is_fine_paid'   => $isFinePaid,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $updated;
        });
    }

    /**
     * Perpanjang masa pinjam (opsional)
     */
    public function extendLoan(Loan $loan, int $days = 7): Loan
    {
        if ($loan->isReturned()) {
            throw ValidationException::withMessages([
                'loan' => 'Peminjaman ini sudah dikembalikan.',
            ]);
        }

        return DB::transaction(function () use ($loan, $days) {
            $newDueDate = Carbon::parse($loan->due_date)->addDays($days);
            
            return $this->loanRepository->update($loan, [
                'due_date' => $newDueDate,
                'status'   => LoanStatusEnum::DIPINJAM->value, // Reset jika sebelumnya terlambat
            ]);
        });
    }

    /**
     * Auto-update status loan yang terlambat
     * Bisa dipanggil dari scheduled task
     */
    public function updateOverdueLoans(): int
    {
        $overdueLoans = $this->loanRepository->getOverdue();
        $count = 0;

        foreach ($overdueLoans as $loan) {
            $this->loanRepository->update($loan, [
                'status' => LoanStatusEnum::TERLAMBAT->value,
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Hitung denda untuk preview (tanpa simpan)
     */
    public function previewFine(Loan $loan): array
    {
        $isFineActive = $this->settingService->getTyped('is_fine_active', false);
        $finePerDay = $this->settingService->getTyped('fine_per_day', 0);
        
        return [
            'is_active' => $isFineActive,
            'per_day'   => $finePerDay,
            'days'      => $loan->daysOverdue(),
            'total'     => $isFineActive ? $loan->calculateFine($finePerDay) : 0,
        ];
    }
}