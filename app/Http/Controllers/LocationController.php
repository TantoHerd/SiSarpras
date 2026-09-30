<?php
// app/Http/Controllers/LocationController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Daftar lokasi
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'has_items' => $request->input('has_items'),
        ];

        $locations = $this->locationService->getLocations($filters, 15);
        $stats = $this->locationService->getStats();

        return view('locations.index', compact('locations', 'stats', 'filters'));
    }

    /**
     * Form tambah
     */
    public function create()
    {
        $parents = $this->locationService->getAllForDropdown();
        return view('locations.create', compact('parents'));
    }

    /**
     * Simpan
     */
    public function store(StoreLocationRequest $request)
    {
        try {
            $location = $this->locationService->createLocation($request->validated());

            return redirect()
                ->route('locations.index')
                ->with('success', "Lokasi \"{$location->name}\" berhasil ditambahkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Detail
     */
    public function show(Location $location)
    {
        $location->loadCount('items');
        $location->load(['parent', 'children']);
        
        $items = $location->items()
            ->with('category')
            ->latest()
            ->take(10)
            ->get();

        return view('locations.show', compact('location', 'items'));
    }

    /**
     * Form edit
     */
    public function edit(Location $location)
    {
        // Ambil parent yang available (kecuali diri sendiri)
        $parents = $this->locationService->getAvailableParents($location->id);
        return view('locations.edit', compact('location', 'parents'));
    }

    /**
     * Update
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        try {
            $this->locationService->updateLocation($location, $request->validated());

            return redirect()
                ->route('locations.index')
                ->with('success', "Lokasi \"{$location->name}\" berhasil diperbarui.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus
     */
    public function destroy(Location $location)
    {
        try {
            $name = $location->name;
            $this->locationService->deleteLocation($location);

            return redirect()
                ->route('locations.index')
                ->with('success', "Lokasi \"{$name}\" berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus lokasi: ' . $e->getMessage());
        }
    }
}