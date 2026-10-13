<?php
// app/Http/Controllers/StockMovementController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function __construct(
        protected StockMovementService $service
    ) {}

    /**
     * Daftar semua pergerakan stok
     */
    public function index(Request $request): View
    {
        $filters = [
            'item_id'   => $request->input('item_id'),
            'type'      => $request->input('type'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
            'search'    => $request->input('search'),
        ];

        $movements = $this->service->paginate($filters, 20);
        $stats     = $this->service->getStats();
        $items     = Item::consumable()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('stock-movements.index', compact(
            'movements', 'stats', 'items', 'filters'
        ));
    }

    /**
     * Detail pergerakan
     */
    public function show(StockMovement $stockMovement): View
    {
        return view('stock-movements.show', compact('stockMovement'));
    }
}