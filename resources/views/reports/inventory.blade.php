{{-- resources/views/reports/inventory.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Inventaris')
@section('page_title', 'Laporan Inventaris Barang')
@section('page_subtitle', 'Preview & ekspor laporan inventaris')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('reports.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-chart-bar text-xs"></i> Laporan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Inventaris Barang</span>
</nav>

<div class="space-y-6">

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink-deep">Laporan Inventaris Barang</h1>
            <p class="text-body-sm text-steel mt-1">
                {{ $items->count() }} barang ditampilkan
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.inventory.pdf', request()->query()) }}" 
               class="btn-danger">
                <i class="fas fa-file-pdf"></i>
                Cetak PDF
            </a>
            <a href="{{ route('reports.inventory.excel', request()->query()) }}" 
               class="btn-primary">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('reports.inventory') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Kategori --}}
            <select name="category_id" class="form-input">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Lokasi --}}
            <select name="location_id" class="form-input">
                <option value="">Semua Lokasi</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" @selected(($filters['location_id'] ?? '') == $loc->id)>
                        {{ $loc->name }}
                    </option>
                @endforeach
            </select>

            {{-- Kondisi --}}
            <select name="condition" class="form-input">
                <option value="">Semua Kondisi</option>
                @foreach(\App\Enums\ItemConditionEnum::cases() as $cond)
                    <option value="{{ $cond->value }}" @selected(($filters['condition'] ?? '') === $cond->value)>
                        {{ $cond->label() }}
                    </option>
                @endforeach
            </select>

            {{-- Status --}}
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\ItemStatusEnum::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('reports.inventory') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ==================== RINGKASAN ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Item</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($summary['total_items']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Quantity</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($summary['total_quantity']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Nilai Aset</p>
            <p class="text-body-md-bold text-primary">{{ format_currency($summary['total_value']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Kondisi Baik</p>
            <p class="text-heading-lg text-success">{{ number_format($summary['by_condition']['baik']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Rusak</p>
            <p class="text-heading-lg text-critical">
                {{ number_format($summary['by_condition']['rusak_ringan'] + $summary['by_condition']['rusak_berat']) }}
            </p>
        </div>
    </div>

    {{-- ==================== TABEL PREVIEW ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-center px-3 py-3 text-caption-bold text-steel uppercase tracking-wider">No</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Kode</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Nama Barang</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Lokasi</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Kondisi</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Qty</th>
                            <th class="text-right px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Harga</th>
                            <th class="text-right px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($items as $index => $item)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="text-center px-3 py-3 text-body-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-caption font-mono text-steel">{{ $item->code }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-body-sm-bold text-ink-deep">{{ $item->name }}</p>
                                    @if($item->brand || $item->type)
                                        <p class="text-caption text-steel">
                                            {{ $item->brand }} {{ $item->type }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <span class="badge-neutral">{{ $item->category->name ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <p class="text-body-sm text-steel">{{ $item->location->name ?? '-' }}</p>
                                </td>
                                <td class="text-center px-4 py-3">
                                    @php
                                        $condColor = match($item->condition->value) {
                                            'baik' => 'bg-success/10 text-success',
                                            'rusak_ringan' => 'bg-warning/10 text-warning',
                                            'rusak_berat' => 'bg-critical/10 text-critical',
                                            default => 'bg-surface-soft text-steel',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-pill text-caption-bold {{ $condColor }}">
                                        {{ $item->condition->label() }}
                                    </span>
                                </td>
                                <td class="text-center px-4 py-3 text-body-sm-bold">{{ $item->quantity }}</td>
                                <td class="text-right px-4 py-3 text-body-sm">
                                    {{ $item->price > 0 ? number_format($item->price, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right px-4 py-3 text-body-sm-bold text-ink-deep">
                                    {{ $item->price > 0 ? number_format($item->price * $item->quantity, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-ink-deep text-white">
                        <tr>
                            <td colspan="8" class="text-right px-4 py-4 text-body-md-bold uppercase tracking-wider">
                                Total Nilai Aset
                            </td>
                            <td class="text-right px-4 py-4 text-body-md-bold">
                                {{ format_currency($summary['total_value']) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-box-open text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">Tidak Ada Data</h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    Tidak ada barang yang cocok dengan filter yang dipilih.
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('reports.inventory') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection