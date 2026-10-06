<?php
// app/Http/Controllers/PortalRequestController.php

namespace App\Http\Controllers;

use App\Models\PortalRequest;
use App\Services\PortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PortalRequestController extends Controller
{
    public function __construct(
        protected PortalService $portalService
    ) {}

    /**
     * Daftar permintaan dari portal
     */
    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status', 'pending'),
            'search' => $request->input('search'),
        ];

        $requests = $this->portalService->getPendingRequests(
            $filters['status'],
            $filters['search']
        );

        $stats = $this->portalService->getStats();

        return view('portal-requests.index', compact('requests', 'stats', 'filters'));
    }

    /**
     * Detail permintaan
     */
    public function show(PortalRequest $portalRequest): View
    {
        $portalRequest->load(['item', 'item.category', 'item.location', 'student', 'processor', 'loan']);
        return view('portal-requests.show', compact('portalRequest'));
    }

    /**
     * Approve
     */
    public function approve(PortalRequest $portalRequest): RedirectResponse
    {
        try {
            $this->portalService->approveRequest($portalRequest, Auth::id());

            return redirect()
                ->route('portal-requests.index')
                ->with('success', "Permintaan dari {$portalRequest->student_name} berhasil disetujui. Loan telah dibuat.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal approve: ' . $e->getMessage());
        }
    }

    /**
     * Reject
     */
    public function reject(Request $request, PortalRequest $portalRequest): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan minimal 5 karakter.',
        ]);

        try {
            $this->portalService->rejectRequest(
                $portalRequest,
                Auth::id(),
                $request->rejection_reason
            );

            return redirect()
                ->route('portal-requests.index')
                ->with('success', "Permintaan dari {$portalRequest->student_name} ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reject: ' . $e->getMessage());
        }
    }
}