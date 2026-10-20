<?php
// app/Services/PortalLoanService.php

namespace App\Services;

use App\Models\Item;
use App\Models\PortalRequest;
use App\Models\PortalRequestItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PortalLoanService
{
    /**
     * Buat permintaan peminjaman (multi-item)
     * Status masih pending, tunggu approval petugas.
     */
    public function createRequest(array $data): PortalRequest
    {
        return DB::transaction(function () use ($data) {
            // 1. Generate Nomor
            $requestNumber = 'PL-' . now()->format('Y-m-d') . '-' . strtoupper(Str::random(4));

            // 2. Buat Header Portal Request
            $portalRequest = PortalRequest::create([
                'request_number'  => $requestNumber, // Pastikan kolom ini ada di migration
                'student_id'      => $data['identity_type'] === 'student' ? $data['identity_id'] : null,
                'nis'             => $data['identity_nis_nip'],
                'student_name'    => $data['identity_name'],
                'student_class'   => $data['identity_type'] === 'student' ? 'Siswa' : 'Guru/Staf',
                'student_phone'   => $data['identity_phone'],
                'purpose'         => $data['purpose'],
                'status'          => 'pending',
                'loan_date'       => now()->startOfDay(),
                'due_date'        => now()->addDays(7), // Default 7 hari
                'ip_address'      => request()->ip(),
                'user_agent'      => substr(request()->userAgent() ?? '', 0, 255),
            ]);

            // 3. Loop Items
            foreach ($data['items'] as $itemData) {
                $item = Item::lockForUpdate()->find($itemData['item_id']);
                
                if (!$item || !$item->isAvailable()) {
                    throw ValidationException::withMessages(['items' => "Barang {$item->name} tidak tersedia."]);
                }

                // Buat detail item
                PortalRequestItem::create([
                    'portal_request_id' => $portalRequest->id,
                    'item_id'           => $item->id,
                    'quantity'          => $itemData['quantity'],
                    'note'              => $itemData['note'] ?? null,
                ]);
            }

            return $portalRequest;
        });
    }
}