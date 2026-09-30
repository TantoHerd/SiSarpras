<?php
// app/Http/Controllers/MaintenanceController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Item;
use App\Models\Maintenance;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    /**
     * Daftar maintenance
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'type'      => $request->input('type'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
        ];

        $maintenances = $this->maintenanceService->getMaintenances($filters, 15);
        $stats = $this->maintenanceService->getStats();

        return view('maintenances.index', compact('maintenances', 'stats', 'filters'));
    }

    /**
     * Form tambah
     */
    public function create(Request $request)
    {
        // Ambil semua item (bukan cuma yang tersedia)
        $items = Item::with(['category', 'location'])
            ->orderBy('name')
            ->get();

        // Pre-select item kalau ada query param
        $selectedItemId = $request->input('item_id');

        return view('maintenances.create', compact('items', 'selectedItemId'));
    }

    /**
     * Simpan
     */
    public function store(StoreMaintenanceRequest $request)
    {
        try {
            $maintenance = $this->maintenanceService->createMaintenance($request->validated());

            return redirect()
                ->route('maintenances.show', $maintenance->id)
                ->with('success', "Perawatan untuk \"{$maintenance->item->name}\" berhasil dicatat.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan perawatan: ' . $e->getMessage());
        }
    }

    /**
     * Detail
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['item', 'item.category', 'item.location', 'creator']);

        // Ambil riwayat maintenance lain dari item yang sama
        $otherMaintenances = $this->maintenanceService->getByItem($maintenance->item_id, 10)
            ->where('id', '!=', $maintenance->id);

        return view('maintenances.show', compact('maintenance', 'otherMaintenances'));
    }

    /**
     * Form edit
     */
    public function edit(Maintenance $maintenance)
    {
        $maintenance->load('item');
        return view('maintenances.edit', compact('maintenance'));
    }

    /**
     * Update
     */
    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance)
    {
        try {
            $this->maintenanceService->updateMaintenance($maintenance, $request->validated());

            return redirect()
                ->route('maintenances.show', $maintenance->id)
                ->with('success', 'Perawatan berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui perawatan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus
     */
    public function destroy(Maintenance $maintenance)
    {
        try {
            $this->maintenanceService->deleteMaintenance($maintenance);

            return redirect()
                ->route('maintenances.index')
                ->with('success', 'Data perawatan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus perawatan: ' . $e->getMessage());
        }
    }
}