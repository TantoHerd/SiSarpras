<?php
// app/Http/Controllers/ItemController.php

namespace App\Http\Controllers;

use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Supplier;
use App\Services\ItemService;
use Illuminate\Http\Request;
use App\Services\FundingSourceService;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService,
        protected FundingSourceService $fundingSourceService
    ) {}

    /**
     * Daftar semua barang
     */
    public function index(Request $request)
    {
        // Ambil filter dari query string
        $filters = [
            'search'      => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'location_id' => $request->input('location_id'),
            'funding_source_id'  => $request->get('funding_source_id'),
            'condition'   => $request->input('condition'),
            'status'      => $request->input('status'),
        ];

        // Ambil data
        $items = $this->itemService->getItems($filters, 15);
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $fundingSources = $this->fundingSourceService->allActive();

        return view('items.index', compact('items', 'categories', 'locations','fundingSources', 'filters'));
    }

    /**
     * Form tambah barang
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $fundingSources = $this->fundingSourceService->allActive();

        return view('items.create', compact('categories', 'locations', 'suppliers', 'fundingSources'));
    }

    /**
     * Simpan barang baru
     */
    public function store(StoreItemRequest $request)
    {
        try {
            $item = $this->itemService->createItem(
                $request->validated(),
                $request->file('image')
            );

            return redirect()
                ->route('items.index')
                ->with('success', "Barang \"{$item->name}\" berhasil ditambahkan dengan kode {$item->code}.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan barang: ' . $e->getMessage());
        }
    }

    /**
     * Detail barang
     */
    public function show(Item $item)
    {
        $item->load([
            'category',
            'location',
            'supplier',
            'creator',
            'histories' => fn($q) => $q->latest()->take(20),
            'histories.user',
        ]);

        return view('items.show', compact('item'));
    }

    /**
     * Form edit barang
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $fundingSources = $this->fundingSourceService->allActive();

        return view('items.edit', compact('item', 'categories', 'locations', 'suppliers', 'fundingSources'));
    }

    /**
     * Update barang
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        try {
            $this->itemService->updateItem(
                $item,
                $request->validated(),
                $request->file('image')
            );

            return redirect()
                ->route('items.index')
                ->with('success', "Barang \"{$item->name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui barang: ' . $e->getMessage());
        }
    }

    /**
     * Hapus barang (soft delete)
     */
    public function destroy(Item $item)
    {
        try {
            $name = $item->name;
            $this->itemService->deleteItem($item);

            return redirect()
                ->route('items.index')
                ->with('success', "Barang \"{$name}\" berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }

    /**
     * Halaman cetak barcode (print-friendly)
     */
    public function barcode(Item $item)
    {
        // Eager load relasi yang dibutuhkan
        $item->load(['category', 'location', 'supplier', 'fundingSource']);

        return view('items.barcode', compact('item'));
    }

    /**
     * Cetak label massal (batch)
     * Akses: admin + petugas_sarpras (via route middleware)
     *
     * @param Request $request  Query: ids (comma-separated), mis. "1,2,3,4"
     */
    public function barcodeBatch(Request $request)
    {
        // Ambil & validasi IDs
        $idsParam = $request->get('ids', '');
        
        $ids = collect(explode(',', $idsParam))
            ->map(fn($id) => (int) trim($id))
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        // Kalau kosong → redirect dengan pesan
        if (empty($ids)) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Pilih minimal 1 barang untuk dicetak labelnya.');
        }

        // Batasi max 100 item sekaligus (biar tidak berat)
        if (count($ids) > 100) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Maksimal 100 barang per sekali cetak. Silakan filter dulu.');
        }

        // Query items (urutan sesuai urutan ID yang dikirim)
        $items = Item::with(['category', 'location', 'fundingSource'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($item) => array_search($item->id, $ids))
            ->values();

        // Kalau tidak ada item ditemukan
        if ($items->isEmpty()) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Barang yang dipilih tidak ditemukan.');
        }

        return view('items.barcode-batch', compact('items'));
    }
}