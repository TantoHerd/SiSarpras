<?php
// app/Http/Controllers/LoanController.php

namespace App\Http\Controllers;

use App\Enums\LoanStatusEnum;
use App\Http\Requests\ReturnLoanRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService
    ) {}

    /**
     * Daftar peminjaman
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Filter dasar
        $filters = [
            'search'      => $request->input('search'),
            'status'      => $request->input('status'),
            'date_from'   => $request->input('date_from'),
            'date_to'     => $request->input('date_to'),
        ];

        // Kalau guru, hanya lihat pinjaman sendiri
        if ($user->isGuru()) {
            $filters['borrower_id'] = $user->id;
        }

        $loans = $this->loanService->getLoans($filters, 15);

        return view('loans.index', compact('loans', 'filters'));
    }

    /**
     * Detail peminjaman
     */
    public function show(Loan $loan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Guru hanya bisa lihat pinjaman sendiri
        if ($user->isGuru() && $loan->borrower_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        $loan->load([
            'item',
            'item.category',
            'item.location',
            'borrower',
            'processor',
        ]);

        return view('loans.show', compact('loan'));
    }

    /**
     * Form tambah peminjaman (khusus petugas/admin — input manual)
     */
    public function create()
    {
        $items = Item::available()
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();
        
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['guru']);
        })->orderBy('name')->get();

        // Siapkan data untuk JS
        $borrowerData = $users->pluck('name', 'id');
        $itemData = $items->mapWithKeys(function ($item) {
            return [$item->id => $item->name . ' (' . $item->code . ')'];
        });

        return view('loans.create', compact('items', 'users', 'borrowerData', 'itemData'));
    }

    /**
     * Simpan peminjaman
     */
    public function store(StoreLoanRequest $request)
    {
        try {
            $loan = $this->loanService->createLoan($request->validated());

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', 'Peminjaman berhasil dibuat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Form pengembalian
     */
    public function returnForm(Loan $loan)
    {
        if ($loan->isReturned()) {
            return redirect()
                ->route('loans.show', $loan->id)
                ->with('warning', 'Peminjaman ini sudah dikembalikan.');
        }

        $loan->load(['item', 'borrower']);
        $finePreview = $this->loanService->previewFine($loan);

        return view('loans.return', compact('loan', 'finePreview'));
    }

    /**
     * Proses pengembalian
     */
    public function returnStore(ReturnLoanRequest $request, Loan $loan)
    {
        try {
            $this->loanService->returnLoan($loan, $request->validated());

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', 'Barang berhasil dikembalikan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    /**
     * Form pengajuan peminjaman (sisi guru)
     */
    public function requestForm(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil item yang tersedia
        $items = Item::available()
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();

        // Siapkan data JSON untuk JavaScript (agar tidak perlu parsing di view)
        $itemsJson = $items->mapWithKeys(function ($item) {
            return [
                $item->id => [
                    'id'       => $item->id,
                    'name'     => $item->name,
                    'code'     => $item->code,
                    'category' => $item->category->name ?? '-',
                    'location' => $item->location->name ?? '-',
                    'quantity' => $item->quantity,
                    'image'    => $item->image ? asset('storage/' . $item->image) : null,
                ]
            ];
        });

        // Ambil item yang sudah dipilih (jika ada query param)
        $selectedItemId = $request->input('item_id');

        return view('loans.request', compact('items', 'selectedItemId', 'itemsJson'));
    }

    /**
     * Simpan pengajuan (sisi guru)
     */
    public function requestStore(StoreLoanRequest $request)
    {
        try {
            $data = $request->validated();
            $data['processed_by'] = Auth::id(); // Guru self-process

            $loan = $this->loanService->createLoan($data);

            return redirect()
                ->route('loans.show', $loan->id)
                ->with('success', 'Pengajuan peminjaman berhasil.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat pinjaman saya (sisi guru)
     */
    public function myLoans(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];

        $loans = $this->loanService->getLoans(
            array_merge($filters, ['borrower_id' => $user->id]),
            15
        );

        return view('loans.my', compact('loans', 'filters'));
    }

    // Method lain (edit, update, destroy) tidak dipakai untuk loans
    // karena loan bersifat immutable — pakai return untuk mengubah status
    public function edit(Loan $loan) { abort(404); }
    public function update(Request $request, Loan $loan) { abort(404); }
    public function destroy(Loan $loan) { abort(404); }
}