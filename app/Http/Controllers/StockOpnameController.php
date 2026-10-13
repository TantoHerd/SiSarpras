<?php
// app/Http/Controllers/StockOpnameController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockOpnameRequest;
use App\Models\Location;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StockOpnameController extends Controller
{
    public function __construct(
        protected StockOpnameService $service
    ) {}

    /**
     * Daftar sesi opname
     */
    public function index(Request $request): View
    {
        $filters = [
            'search'      => $request->input('search'),
            'status'      => $request->input('status'),
            'location_id' => $request->input('location_id'),
        ];

        $opnames   = $this->service->paginate($filters, 15);
        $stats     = $this->service->getStats();
        $locations = Location::orderBy('name')->get();

        return view('stock-opnames.index', compact(
            'opnames', 'stats', 'locations', 'filters'
        ));
    }

    /**
     * Form buka sesi baru
     */
    public function create(): View
    {
        $locations = Location::orderBy('name')->get();
        return view('stock-opnames.create', compact('locations'));
    }

    /**
     * Simpan sesi baru
     */
    public function store(StoreStockOpnameRequest $request): RedirectResponse
    {
        try {
            $opname = $this->service->openSession(
                $request->location_id,
                $request->note
            );

            $count = $opname->items->count();

            if ($count === 0) {
                return redirect()
                    ->route('stock-opnames.index')
                    ->with('warning', "Sesi opname {$opname->opname_number} dibuat, tapi tidak ada item consumable di lokasi ini.");
            }

            return redirect()
                ->route('stock-opnames.show', $opname->id)
                ->with('success', "Sesi opname {$opname->opname_number} dibuka dengan {$count} item.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuka sesi: ' . $e->getMessage());
        }
    }

    /**
     * Detail sesi opname
     */
    public function show(StockOpname $stockOpname): View
    {
        $stockOpname->load([
            'location',
            'starter',
            'completer',
            'items.item',
            'items.item.category',
        ]);

        return view('stock-opnames.show', compact('stockOpname'));
    }

    /**
     * Update input fisik (form bulk)
     */
    public function updatePhysical(Request $request, StockOpname $stockOpname): RedirectResponse
    {
        try {
            $this->service->updatePhysicalStock($stockOpname, $request->items ?? []);

            return redirect()
                ->route('stock-opnames.show', $stockOpname->id)
                ->with('success', 'Input fisik berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Complete sesi & koreksi stok
     */
    public function complete(StockOpname $stockOpname): RedirectResponse
    {
        try {
            $this->service->completeSession($stockOpname);

            return redirect()
                ->route('stock-opnames.show', $stockOpname->id)
                ->with('success', "Sesi opname {$stockOpname->opname_number} selesai. Stok telah dikoreksi.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal complete: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan sesi
     */
    public function cancel(StockOpname $stockOpname): RedirectResponse
    {
        try {
            $this->service->cancelSession($stockOpname);

            return redirect()
                ->route('stock-opnames.index')
                ->with('success', "Sesi opname {$stockOpname->opname_number} dibatalkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cancel: ' . $e->getMessage());
        }
    }

    /**
     * Hapus sesi
     */
    public function destroy(StockOpname $stockOpname): RedirectResponse
    {
        try {
            $number = $stockOpname->opname_number;
            $this->service->deleteSession($stockOpname);

            return redirect()
                ->route('stock-opnames.index')
                ->with('success', "Sesi opname {$number} berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}