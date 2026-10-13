{{-- resources/views/stock-opnames/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Stok Opname')
@section('page_title', 'Stok Opname')
@section('page_subtitle', 'Kelola sesi hitung fisik stok barang')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total Sesi</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-edit text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Draft</p>
            <p class="text-heading-lg text-warning">{{ number_format($stats['draft']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Selesai</p>
            <p class="text-heading-lg text-success">{{ number_format($stats['completed']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Hari Ini</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['today']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $opnames->total() }}</span> sesi opname
            </p>
        </div>
        <a href="{{ route('stock-opnames.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Buka Sesi Baru
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('stock-opnames.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nomor opname atau lokasi..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('stock-opnames.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            
            <a href="{{ route('stock-opnames.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('stock-opnames.index', array_merge(request()->query(), ['status' => 'draft'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'draft' ? 'pill-tab-active' : '' }}">
                Draft
                @if($stats['draft'] > 0)
                    <span class="ml-1 badge-warning text-xs">{{ $stats['draft'] }}</span>
                @endif
            </a>
            <a href="{{ route('stock-opnames.index', array_merge(request()->query(), ['status' => 'completed'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'completed' ? 'pill-tab-active' : '' }}">
                Selesai
            </a>
            <a href="{{ route('stock-opnames.index', array_merge(request()->query(), ['status' => 'cancelled'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'cancelled' ? 'pill-tab-active' : '' }}">
                Dibatalkan
            </a>
        </div>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($opnames->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">No. Opname</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Lokasi</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Items</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Dibuka</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($opnames as $opname)
                            <tr class="hover:bg-surface-soft/50 transition-colors {{ $opname->isDraft() ? 'bg-warning/5' : '' }}">
                                {{-- No. Opname --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep font-mono">
                                        {{ $opname->opname_number }}
                                    </p>
                                    @if($opname->note)
                                        <p class="text-caption text-steel truncate max-w-[200px]">
                                            {{ Str::limit($opname->note, 40) }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Lokasi --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep">
                                        {{ $opname->location->name ?? '-' }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        oleh {{ $opname->starter->name ?? '-' }}
                                    </p>
                                </td>

                                {{-- Items --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    <span class="text-body-sm-bold text-ink-deep">
                                        {{ $opname->items_count ?? $opname->items->count() }}
                                    </span>
                                    <p class="text-caption text-steel">item</p>
                                </td>

                                {{-- Dibuka --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $opname->started_at->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $opname->started_at->format('H:i') }} WIB
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="{{ $opname->status_badge }}">
                                        {{ $opname->status_label }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('stock-opnames.show', $opname->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($opnames->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $opnames->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-clipboard-list text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Sesi Opname
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada sesi opname yang cocok dengan filter.
                    @else
                        Mulai buka sesi opname untuk mengecek fisik stok.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('stock-opnames.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('stock-opnames.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Buka Sesi Pertama
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection