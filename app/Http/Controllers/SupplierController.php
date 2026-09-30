<?php
// app/Http/Controllers/SupplierController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    /**
     * Daftar supplier
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'has_items' => $request->input('has_items'),
        ];

        $suppliers = $this->supplierService->getSuppliers($filters, 15);
        $stats = $this->supplierService->getStats();

        return view('suppliers.index', compact('suppliers', 'stats', 'filters'));
    }

    /**
     * Form tambah
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Simpan
     */
    public function store(StoreSupplierRequest $request)
    {
        try {
            $supplier = $this->supplierService->createSupplier($request->validated());

            return redirect()
                ->route('suppliers.index')
                ->with('success', "Supplier \"{$supplier->name}\" berhasil ditambahkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan supplier: ' . $e->getMessage());
        }
    }

    /**
     * Detail
     */
    public function show(Supplier $supplier)
    {
        $supplier->loadCount('items');
        $items = $supplier->items()
            ->with(['category', 'location'])
            ->latest()
            ->take(10)
            ->get();

        return view('suppliers.show', compact('supplier', 'items'));
    }

    /**
     * Form edit
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        try {
            $this->supplierService->updateSupplier($supplier, $request->validated());

            return redirect()
                ->route('suppliers.index')
                ->with('success', "Supplier \"{$supplier->name}\" berhasil diperbarui.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui supplier: ' . $e->getMessage());
        }
    }

    /**
     * Hapus
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $name = $supplier->name;
            $this->supplierService->deleteSupplier($supplier);

            return redirect()
                ->route('suppliers.index')
                ->with('success', "Supplier \"{$name}\" berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
}