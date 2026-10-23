{{-- resources/views/stock-movements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pergerakan Stok')
@section('page_title', 'Pergerakan Stok')
@section('page_subtitle', 'Log semua pergerakan stok barang habis pakai')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Pergerakan</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-arrow-down text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Masuk</p>
            <p class="text-heading-lg text-success">{{ number_format($stats['total_in']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                    <i class="fas fa-arrow-up text-critical"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Keluar</p>
            <p class="text-heading-lg text-critical">{{ number_format($stats['total_out']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-balance-scale text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Penyesuaian</p>
            <p class="text-heading-lg text-warning">{{ number_format($stats['total_adjust']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $movements->total() }}</span> pergerakan tercatat
            </p>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="space-y-4">
            
            {{-- BARIS 1: Pencarian & Tipe --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Search --}}
                <div class="md:col-span-2 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari nama atau kode barang..."
                           class="form-input pl-11">
                </div>

                {{-- Type --}}
                <select name="type" class="form-input">
                    <option value="">Semua Tipe</option>
                    <option value="in" @selected(($filters['type'] ?? '') === 'in')>Masuk</option>
                    <option value="out" @selected(($filters['type'] ?? '') === 'out')>Keluar</option>
                    <option value="adjustment" @selected(($filters['type'] ?? '') === 'adjustment')>Penyesuaian</option>
                </select>
            </div>

            {{-- BARIS 2: Barang, Tanggal, & Tombol --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center pt-4 border-t border-hairline-soft">
                {{-- Dropdown Barang (Lebar 4/12) --}}
                <div class="md:col-span-4">
                    <select name="item_id" class="form-input w-full">
                        <option value="">Semua Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" @selected(($filters['item_id'] ?? '') == $item->id)>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tanggal (Lebar 5/12) --}}
                <div class="md:col-span-5 flex items-center gap-2">
                    <span class="text-caption text-steel whitespace-nowrap">Periode:</span>
                    <input type="date" 
                           name="date_from" 
                           value="{{ $filters['date_from'] ?? '' }}" 
                           class="form-input flex-1"
                           title="Dari Tanggal">
                    <span class="text-caption text-steel">s/d</span>
                    <input type="date" 
                           name="date_to" 
                           value="{{ $filters['date_to'] ?? '' }}" 
                           class="form-input flex-1"
                           title="Sampai Tanggal">
                </div>

                {{-- Tombol Aksi (Lebar 3/12) --}}
                <div class="md:col-span-3 flex items-center justify-end gap-2">
                    <button type="submit" class="btn-ink w-full justify-center">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                    @if(array_filter($filters))
                        <a href="{{ route('stock-movements.index') }}" class="btn-ghost px-3" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Tipe</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Jumlah</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Stok</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Oleh</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Waktu</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($movements as $movement)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Barang --}}
                                <td class="px-5 py-4">
                                    <div class="min-w-0">
                                        <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                            {{ $movement->item->name ?? '-' }}
                                        </p>
                                        <p class="text-caption text-steel font-mono">
                                            {{ $movement->item->code ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Tipe --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="{{ $movement->type->badge() }} inline-flex items-center gap-1">
                                        <i class="fas {{ $movement->type->icon() }} text-xs"></i>
                                        {{ $movement->type->label() }}
                                    </span>
                                </td>

                                {{-- Jumlah --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="text-body-sm-bold {{ $movement->type->value === 'in' ? 'text-success' : ($movement->type->value === 'out' ? 'text-critical' : 'text-warning') }}">
                                        {{ $movement->type->value === 'out' ? '-' : '+' }}{{ $movement->quantity }}
                                    </span>
                                </td>

                                {{-- Stok --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    <span class="text-caption text-steel">
                                        {{ $movement->stock_before }} → 
                                        <strong class="text-ink-deep">{{ $movement->stock_after }}</strong>
                                    </span>
                                </td>

                                {{-- Oleh --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $movement->creator->name ?? 'Sistem' }}
                                    </p>
                                </td>

                                {{-- Waktu --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm text-ink">
                                        {{ $movement->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $movement->created_at->format('H:i') }} WIB
                                    </p>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('stock-movements.show', $movement->id) }}" 
                                       class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($movements->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $movements->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-exchange-alt text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Pergerakan
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada pergerakan stok yang cocok dengan filter.
                    @else
                        Belum ada pergerakan stok yang tercatat di sistem.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('stock-movements.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection