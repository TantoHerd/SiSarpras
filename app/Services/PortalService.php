<?php
// app/Services/PortalService.php

namespace App\Services;

use App\Models\Item;
use App\Models\PortalRequest;
use App\Models\Student;
use App\Repositories\PortalRequestRepository;
use App\Repositories\StudentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PortalService
{
    public function __construct(
        protected PortalRequestRepository $portalRepository,
        protected StudentRepository $studentRepository,
        protected SettingService $settingService,
        protected LoanService $loanService
    ) {}

    /**
     * Cek apakah portal aktif
     */
    public function isPortalEnabled(): bool
    {
        return $this->settingService->getTyped('portal_siswa_enabled', false);
    }

    /**
     * Verifikasi siswa berdasarkan NIS + HP
     */
    public function verifyStudent(string $nis, ?string $phone = null): ?Student
    {
        $student = $this->studentRepository->findByNis($nis);
        
        if (!$student || !$student->is_active) {
            return null;
        }

        // Kalau siswa punya HP, verifikasi HP
        if ($student->phone && $phone && trim($student->phone) !== trim($phone)) {
            return null;
        }

        return $student;
    }

    /**
     * Buat permintaan dari portal
     */
    public function createRequest(Student $student, array $data): PortalRequest
    {
        return DB::transaction(function () use ($student, $data) {
            $item = Item::with('category')->findOrFail($data['item_id']);

            // Validasi: item harus allow_student_loan
            if (!$item->category || !$item->category->allow_student_loan) {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang ini tidak dapat dipinjam melalui portal siswa.',
                ]);
            }

            // Validasi: stok
            $quantity = (int) ($data['quantity'] ?? 1);
            if ($item->isPerBatch() && $quantity > $item->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak mencukupi. Tersedia: {$item->quantity}.",
                ]);
            }
            if ($item->isPerUnit() && $quantity !== 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Barang per unit hanya bisa dipinjam 1.',
                ]);
            }

            // Cek duplikat: pending untuk item yang sama
            if ($this->portalRepository->hasPendingForItem($student->nis, $item->id)) {
                throw ValidationException::withMessages([
                    'item_id' => 'Anda sudah memiliki permintaan yang menunggu review untuk barang ini.',
                ]);
            }

            // Cek max loans
            $maxLoans = $this->settingService->getTyped('portal_siswa_max_loans', 3);
            $activeCount = $this->portalRepository->countActiveByNis($student->nis);
            
            if ($activeCount >= $maxLoans) {
                throw ValidationException::withMessages([
                    'item_id' => "Anda sudah mencapai batas maksimal {$maxLoans} permintaan aktif.",
                ]);
            }

            // Hitung tanggal
            $maxDays = $this->settingService->getTyped('portal_siswa_max_days', 7);
            $loanDate = now()->startOfDay();
            $dueDate = $loanDate->copy()->addDays($maxDays);

            // Simpan
            return $this->portalRepository->create([
                'student_id'     => $student->id,
                'nis'            => $student->nis,
                'student_name'   => $student->name,
                'student_class'  => $student->class,
                'student_phone'  => $student->phone,
                'item_id'        => $item->id,
                'quantity'       => $quantity,
                'purpose'        => $data['purpose'],
                'loan_date'      => $loanDate,
                'due_date'       => $dueDate,
                'status'         => 'pending',
                'ip_address'     => request()->ip(),
                'user_agent'     => substr(request()->userAgent() ?? '', 0, 255),
            ]);
        });
    }

    /**
     * Approve permintaan → buat loan
     */
    public function approveRequest(PortalRequest $portalRequest, int $processedBy): PortalRequest
    {
        if (!$portalRequest->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'Permintaan ini sudah diproses sebelumnya.',
            ]);
        }

        return DB::transaction(function () use ($portalRequest, $processedBy) {
            // Buat loan via LoanService
            $loan = $this->loanService->createLoan([
                'item_id'      => $portalRequest->item_id,
                'quantity'     => $portalRequest->quantity,
                'borrower_id'  => null, // bukan user, tapi kita butuh field
                'processed_by' => $processedBy,
                'purpose'      => "[Portal Siswa - {$portalRequest->student_name} ({$portalRequest->student_class})] " . $portalRequest->purpose,
            ]);

            // Update portal request
            return $this->portalRepository->update($portalRequest, [
                'status'       => 'approved',
                'processed_by' => $processedBy,
                'processed_at' => now(),
                'loan_id'      => $loan->id,
            ]);
        });
    }

    /**
     * Reject permintaan
     */
    public function rejectRequest(PortalRequest $portalRequest, int $processedBy, string $reason): PortalRequest
    {
        if (!$portalRequest->isPending()) {
            throw ValidationException::withMessages([
                'request' => 'Permintaan ini sudah diproses sebelumnya.',
            ]);
        }

        return $this->portalRepository->update($portalRequest, [
            'status'           => 'rejected',
            'processed_by'     => $processedBy,
            'processed_at'     => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Ambil daftar pending untuk petugas
     */
    public function getPendingRequests(?string $status = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->portalRepository->paginate($status, $search, $perPage);
    }

    /**
     * Cek riwayat permintaan siswa
     */
    public function getStudentRequests(string $nis): Collection
    {
        return $this->portalRepository->findByNis($nis);
    }

    /**
     * Count pending (untuk badge)
     */
    public function countPending(): int
    {
        return $this->portalRepository->countPending();
    }

    /**
     * Get stats
     */
    public function getStats(): array
    {
        return $this->portalRepository->getStats();
    }

    /**
     * Ambil item yang tersedia untuk siswa
     */
    public function getAvailableItems(): Collection
    {
        return Item::available()
            ->whereHas('category', fn($q) => $q->where('allow_student_loan', true))
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();
    }
}