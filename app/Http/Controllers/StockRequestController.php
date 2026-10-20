<?php
// app/Http/Controllers/StockRequestController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequestRequest;
use App\Models\Item;
use App\Models\StockRequest;
use App\Services\StockRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StockRequestController extends Controller
{
    public function __construct(
        protected StockRequestService $service
    ) {}

    /**
     * Daftar semua permintaan (untuk petugas & admin)
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $filters = [
            'search'    => $request->input('search'),
            // ← FIX: Ubah default dari 'pending' menjadi kosong (Semua)
            'status'    => $request->input('status', ''), 
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];

        if ($user->isGuru()) {
            return $this->myRequests($request);
        }

        $requests = $this->service->paginate($filters, 15);
        $stats    = $this->service->getStats();

        return view('stock-requests.index', compact('requests', 'stats', 'filters'));
    }

    /**
     * Riwayat permintaan saya (guru/staf)
     */
    public function myRequests(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];

        $requests = $this->service->forUser($user->id, 15);
        $stats    = $this->service->getStats();

        return view('stock-requests.my', compact(
            'requests', 'stats', 'filters'
        ));
    }

    /**
     * Form tambah permintaan
     */
    public function create(): View
    {
        $items = Item::consumable()
            ->where('quantity', '>', 0)
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();

        return view('stock-requests.create', compact('items'));
    }

    /**
     * Simpan permintaan
     */
    public function store(StoreStockRequestRequest $request): RedirectResponse
    {
        try {
            $stockRequest = $this->service->createRequest($request->validated());

            return redirect()
                ->route('stock-requests.show', $stockRequest->id)
                ->with('success', "Permintaan {$stockRequest->request_number} berhasil dikirim.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan permintaan: ' . $e->getMessage());
        }
    }

    /**
     * Detail permintaan
     */
    public function show(StockRequest $stockRequest): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Guru/staf hanya bisa lihat milik sendiri
        if ($user->isGuru() && $stockRequest->requester_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke permintaan ini.');
        }

        $stockRequest->load([
            'requester',
            'processor',
            'items.item',
            'items.item.category',
        ]);

        return view('stock-requests.show', compact('stockRequest'));
    }

    /**
     * Approve permintaan
     */
    public function approve(StockRequest $stockRequest): RedirectResponse
    {
        try {
            $this->service->approveRequest($stockRequest, Auth::id());

            return redirect()
                ->route('stock-requests.show', $stockRequest->id)
                ->with('success', "Permintaan {$stockRequest->request_number} berhasil disetujui. Stok telah dikurangi.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal approve: ' . $e->getMessage());
        }
    }

    /**
     * Reject permintaan
     */
    public function reject(Request $request, StockRequest $stockRequest): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan minimal 5 karakter.',
        ]);

        try {
            $this->service->rejectRequest(
                $stockRequest,
                Auth::id(),
                $request->rejection_reason
            );

            return redirect()
                ->route('stock-requests.show', $stockRequest->id)
                ->with('success', "Permintaan {$stockRequest->request_number} ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reject: ' . $e->getMessage());
        }
    }

    /**
     * Hapus permintaan (hanya pending milik sendiri)
     */
    public function destroy(StockRequest $stockRequest): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek ownership
        if ($user->isGuru() && $stockRequest->requester_id !== $user->id) {
            abort(403);
        }

        try {
            $number = $stockRequest->request_number;
            $this->service->deleteRequest($stockRequest);

            return redirect()
                ->route('stock-requests.index')
                ->with('success', "Permintaan {$number} berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}