<?php
// app/Http/Controllers/ReceivingController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FundingSource;
use App\Models\Item;
use App\Models\Location;
use App\Models\Receiving;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\ReceivingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReceivingController extends Controller
{
    public function __construct(
        protected ReceivingService $service
    ) {}

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $receivings = Receiving::with(['supplier', 'creator'])
            ->withCount('items')
            ->when($search, function($q) use ($search) {
                $q->where('receiving_number', 'ILIKE', "%{$search}%")
                  ->orWhere('invoice_number', 'ILIKE', "%{$search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'ILIKE', "%{$search}%"));
            })
            ->latest('receiving_date')
            ->paginate(15);

        return view('receivings.index', compact('receivings', 'search'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $fundingSources = FundingSource::active()->orderBy('name')->get();
        $items = Item::orderBy('name')->get(); // Untuk dropdown barang existing
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('receivings.create', compact(
            'suppliers', 'fundingSources', 'items', 'categories', 'locations', 'units'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiving_date'    => 'required|date',
            'supplier_id'       => 'nullable|exists:suppliers,id',
            'funding_source_id' => 'nullable|exists:funding_sources,id',
            'invoice_number'    => 'nullable|string|max:100',
            'note'              => 'nullable|string|max:500',
            
            'items'                     => 'required|array|min:1',
            'items.*.item_id'           => 'nullable|exists:items,id',
            'items.*.name'              => 'required_without:items.*.item_id|string|max:200',
            'items.*.category_id'       => 'required_without:items.*.item_id|exists:categories,id',
            'items.*.unit_id'           => 'required_without:items.*.item_id|exists:units,id',
            
            // ↓ TAMBAHKAN INI
            'items.*.location_id'       => 'nullable|exists:locations,id', 
            
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.price'             => 'nullable|numeric|min:0',
        ]);

        try {
            $receiving = $this->service->createReceiving($validated);

            return redirect()
                ->route('receivings.show', $receiving->id)
                ->with('success', "Penerimaan barang #{$receiving->receiving_number} berhasil disimpan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(Receiving $receiving): View
    {
        $receiving->load(['supplier', 'fundingSource', 'creator', 'items.item']);
        return view('receivings.show', compact('receiving'));
    }
}