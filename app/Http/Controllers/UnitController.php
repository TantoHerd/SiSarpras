<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::withCount('items')->orderBy('name')->paginate(15);
        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:units,name',
            'symbol' => 'nullable|string|max:10',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $unit->id,
            'symbol' => 'nullable|string|max:10',
        ]);

        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->items()->count() > 0) {
            return back()->with('error', 'Satuan masih digunakan oleh barang, tidak bisa dihapus.');
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }
}