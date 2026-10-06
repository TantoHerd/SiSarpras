<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFundingSourceRequest;
use App\Http\Requests\UpdateFundingSourceRequest;
use App\Models\FundingSource;
use App\Services\FundingSourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use App\Exports\FundingSourceExport;
use Maatwebsite\Excel\Facades\Excel;

class FundingSourceController extends Controller
{
    public function __construct(
        protected FundingSourceService $service
    ) {}

    /**
     * Tampilkan daftar sumber dana.
     * Akses: admin + petugas (via route middleware).
     */
    public function index(Request $request): View
    {
        $filters = [
            'search'    => $request->get('search'),
            'is_active' => $request->get('is_active'),
        ];

        $sources = $this->service->paginate(
            $filters['search'],
            $filters['is_active']
        );

        $stats = $this->service->getStats();

        return view('funding-sources.index', compact('sources', 'stats', 'filters'));
    }

    /**
     * Form tambah sumber dana.
     * Akses: admin only (via route middleware).
     */
    public function create(): View
    {
        return view('funding-sources.create');
    }

    /**
     * Simpan sumber dana baru.
     * Akses: admin only (via route middleware).
     */
    public function store(StoreFundingSourceRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('funding-sources.index')
            ->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    /**
     * Form edit sumber dana.
     * Akses: admin only (via route middleware).
     */
    public function edit(FundingSource $fundingSource): View
    {
        $itemCount = $fundingSource->items()->count();

        return view('funding-sources.edit', compact('fundingSource', 'itemCount'));
    }

    /**
     * Update sumber dana.
     * Akses: admin only (via route middleware).
     */
    public function update(UpdateFundingSourceRequest $request, FundingSource $fundingSource): RedirectResponse
    {
        $this->service->update($fundingSource, $request->validated());

        return redirect()
            ->route('funding-sources.index')
            ->with('success', 'Sumber dana berhasil diperbarui.');
    }

    /**
     * Hapus sumber dana.
     * Akses: admin only (via route middleware).
     * Proteksi: tidak bisa hapus jika masih dipakai item.
     */
    public function destroy(FundingSource $fundingSource): RedirectResponse
    {
        try {
            $this->service->delete($fundingSource);

            return redirect()
                ->route('funding-sources.index')
                ->with('success', 'Sumber dana berhasil dihapus.');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('funding-sources.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Export Excel master Sumber Dana
     * Akses: admin only (via route middleware)
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'search'    => $request->get('search'),
            'is_active' => $request->get('is_active'),
        ];

        $filename = 'Master-Sumber-Dana-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new FundingSourceExport($filters), $filename);
    }
}