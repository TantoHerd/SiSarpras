{{-- resources/views/stock-requests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Permintaan BHP')
@section('page_title', 'Permintaan Barang Habis Pakai')
@section('page_subtitle', 'Kelola permintaan BHP dari guru, staf, dan petugas')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Pending</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['pending']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Disetujui</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['approved']) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                    <i class="fas fa-times-circle text-critical"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Ditolak</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['rejected']) }}</p>
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
                Total <span class="text-body-sm-bold text-ink-deep">{{ $requests->total() }}</span> permintaan
            </p>
        </div>
        <a href="{{ route('stock-requests.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Permintaan Baru
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('stock-requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nomor permintaan, keperluan, atau nama pemohon..."
                       class="form-input pl-11">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('stock-requests.index') }}" class="btn-ghost" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'pending' ? 'pill-tab-active' : '' }}">
                Pending
                @if($stats['pending'] > 0)
                    <span class="ml-1 badge-critical text-xs">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'approved' ? 'pill-tab-active' : '' }}">
                Disetujui
            </a>
            <a href="{{ route('stock-requests.index', array_merge(request()->query(), ['status' => 'rejected'])) }}"
               class="pill-tab {{ ($filters['status'] ?? '') === 'rejected' ? 'pill-tab-active' : '' }}">
                Ditolak
            </a>
        </div>
    </div>

    {{-- ==================== TABEL ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">No. Permintaan</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Pemohon</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Items</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Diajukan</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($requests as $req)
                            <tr class="hover:bg-surface-soft/50 transition-colors {{ $req->isPending() ? 'bg-warning/5' : '' }}">
                                {{-- No. Permintaan --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep font-mono">
                                        {{ $req->request_number }}
                                    </p>
                                    <p class="text-caption text-steel truncate max-w-[200px]">
                                        {{ Str::limit($req->purpose, 40) }}
                                    </p>
                                </td>

                                {{-- Pemohon --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep">
                                        {{ $req->requester->name ?? '-' }}
                                    </p>
                                    <span class="badge-neutral text-xs">{{ ucfirst($req->requester_type) }}</span>
                                </td>

                                {{-- Items --}}
                                <td class="px-5 py-4 text-center hidden lg:table-cell">
                                    <span class="text-body-sm-bold text-ink-deep">
                                        {{ $req->items->count() }} jenis
                                    </span>
                                    <p class="text-caption text-steel">
                                        {{ $req->items->sum('quantity_requested') }} unit total
                                    </p>
                                </td>

                                {{-- Diajukan --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $req->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $req->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="{{ $req->status_badge }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('stock-requests.show', $req->id) }}" 
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
            @if($requests->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $requests->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-inbox text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Permintaan
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada permintaan yang cocok dengan filter.
                    @else
                        Belum ada permintaan BHP dari guru/staf.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('stock-requests.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection