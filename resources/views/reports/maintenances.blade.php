{{-- resources/views/reports/maintenances.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Perawatan')
@section('page_title', 'Laporan Perawatan Barang')
@section('page_subtitle', 'Preview & ekspor laporan perawatan')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('reports.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-chart-bar text-xs"></i> Laporan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Perawatan</span>
</nav>

<div class="space-y-6">

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink-deep">Laporan Perawatan</h1>
            <p class="text-body-sm text-steel mt-1">
                {{ $maintenances->count() }} perawatan ditampilkan
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.maintenances.pdf', request()->query()) }}" 
               class="btn-danger">
                <i class="fas fa-file-pdf"></i>
                Cetak PDF
            </a>
            <a href="{{ route('reports.maintenances.excel', request()->query()) }}" 
               class="btn-primary">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('reports.maintenances') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Jenis</label>
                <select name="type" class="form-input">
                    <option value="">Semua Jenis</option>
                    <option value="rutin" @selected(($filters['type'] ?? '') === 'rutin')>Rutin</option>
                    <option value="perbaikan" @selected(($filters['type'] ?? '') === 'perbaikan')>Perbaikan</option>
                </select>
            </div>
            <div>
                <label class="text-caption-bold text-steel mb-1 block">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="in_progress" @selected(($filters['status'] ?? '') === 'in_progress')>Sedang Proses</option>
                    <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Selesai</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('reports.maintenances') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ==================== RINGKASAN ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Rutin</p>
            <p class="text-heading-lg text-primary">{{ number_format($summary['routine']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Perbaikan</p>
            <p class="text-heading-lg text-warning">{{ number_format($summary['repair']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Sedang Proses</p>
            <p class="text-heading-lg text-warning">{{ number_format($summary['in_progress']) }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Biaya</p>
            <p class="text-body-md-bold text-oculus">{{ format_currency($summary['total_cost']) }}</p>
        </div>
    </div>

    {{-- ==================== TABEL PREVIEW ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($maintenances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-center px-3 py-3 text-caption-bold text-steel uppercase tracking-wider">No</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Tanggal</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Jenis</th>
                            <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Teknisi</th>
                            <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($maintenances as $index => $maintenance)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                <td class="text-center px-3 py-3 text-body-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-body-sm-bold text-ink-deep">{{ $maintenance->item->name ?? '-' }}</p>
                                    <p class="text-caption text-steel font-mono">{{ $maintenance->item->code ?? '-' }}</p>
                                    @if($maintenance->description)
                                        <p class="text-caption text-steel mt-1">
                                            {{ Str::limit($maintenance->description, 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="text-center px-4 py-3 text-body-sm">
                                    {{ $maintenance->maintenance_date->format('d M Y') }}
                                </td>
                                <td class="text-center px-4 py-3">
                                    @if($maintenance->type === 'rutin')
                                        <span class="badge-info">Rutin</span>
                                    @else
                                        <span class="badge-warning">Perbaikan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <span class="text-body-sm text-steel">{{ $maintenance->technician ?? '-' }}</span>
                                </td>
                                <td class="text-center px-4 py-3">
                                    @if($maintenance->status === 'in_progress')
                                        <span class="badge-warning">Sedang Proses</span>
                                    @else
                                        <span class="badge-success">Selesai</span>
                                    @endif
                                </td>
                                <td class="text-right px-4 py-3">
                                    @if($maintenance->cost > 0)
                                        <span class="text-body-sm-bold text-ink-deep">
                                            {{ number_format($maintenance->cost, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="badge-success">Gratis</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-ink-deep text-white">
                        <tr>
                            <td colspan="6" class="text-right px-4 py-4 text-body-md-bold uppercase tracking-wider">
                                Total Biaya Perawatan
                            </td>
                            <td class="text-right px-4 py-4 text-body-md-bold">
                                {{ format_currency($summary['total_cost']) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-tools text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">Tidak Ada Data</h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    Tidak ada perawatan untuk periode yang dipilih.
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('reports.maintenances') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection