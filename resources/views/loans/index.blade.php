{{-- resources/views/loans/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Peminjaman')
@section('page_title', 'Peminjaman')
@section('page_subtitle', 'Kelola peminjaman dan pengembalian barang')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK CARDS ==================== --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-hand-holding text-primary"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider">Aktif</p>
                <p class="text-heading-lg text-ink-deep">{{ $loans->whereIn('status.value', ['dipinjam', 'terlambat'])->count() }}</p>
            </div>

            <div class="card-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-critical"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider">Terlambat</p>
                <p class="text-heading-lg text-ink-deep">{{ $loans->where('status.value', 'terlambat')->count() }}</p>
            </div>

            <div class="card-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider">Total</p>
                <p class="text-heading-lg text-ink-deep">{{ $loans->total() }}</p>
            </div>

            <div class="card-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider">Hari Ini</p>
                <p class="text-heading-lg text-ink-deep">
                    {{ $loans->where('return_date', '!=', null)->where('return_date', '>=', today())->count() }}
                </p>
            </div>
        </div>
    @endif

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $loans->total() }}</span> peminjaman terdaftar
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('loans.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Peminjaman Baru
            </a>
        </div>
    </div>

    {{-- ==================== FILTER CARD ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('loans.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama barang atau peminjam..."
                       class="form-input pl-11">
            </div>

            {{-- Date From --}}
            <input type="date" 
                   name="date_from" 
                   value="{{ $filters['date_from'] ?? '' }}"
                   class="form-input">

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('loans.index') }}" class="btn-ghost" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('loans.index', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            @foreach(\App\Enums\LoanStatusEnum::cases() as $status)
                <a href="{{ route('loans.index', array_merge(request()->query(), ['status' => $status->value])) }}"
                   class="pill-tab {{ ($filters['status'] ?? '') === $status->value ? 'pill-tab-active' : '' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ==================== TABEL PEMINJAMAN ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($loans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Peminjam</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Tgl Pinjam</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Jatuh Tempo</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($loans as $loan)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- Barang --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($loan->item->image)
                                            <img src="{{ asset('storage/' . $loan->item->image) }}" 
                                                 alt="{{ $loan->item->name }}"
                                                 class="w-12 h-12 rounded-lg object-cover bg-surface-soft flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-steel"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[250px]">
                                                {{ $loan->item->name }}
                                            </p>
                                            <p class="text-caption text-steel font-mono">
                                                {{ $loan->item->code }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Peminjam --}}
                                <td class="px-5 py-4">
                                    <p class="text-body-sm-bold text-ink-deep">
                                        {{ $loan->borrower->name ?? '-' }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        NIP: {{ $loan->borrower->nip ?? '-' }}
                                    </p>
                                </td>

                                {{-- Tgl Pinjam --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $loan->loan_date->format('d M Y') }}
                                    </p>
                                    <p class="text-caption text-steel">
                                        {{ $loan->loan_date->format('H:i') }}
                                    </p>
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink">
                                        {{ $loan->due_date->format('d M Y') }}
                                    </p>
                                    @if($loan->isOverdue() && !$loan->isReturned())
                                        <p class="text-caption text-critical font-bold">
                                            Terlambat {{ $loan->daysOverdue() }} hari
                                        </p>
                                    @elseif(!$loan->isReturned())
                                        <p class="text-caption text-steel">
                                            {{ $loan->due_date->diffInDays(now()) }} hari lagi
                                        </p>
                                    @else
                                        <p class="text-caption text-success">
                                            Dikembalikan {{ $loan->return_date->format('d M') }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $statusColor = match($loan->status->value) {
                                            'dipinjam' => 'badge-info',
                                            'terlambat' => 'badge-critical',
                                            'dikembalikan' => 'badge-success',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $statusColor }}">
                                        {{ $loan->status->label() }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('loans.show', $loan->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        @if(!$loan->isReturned())
                                            <a href="{{ route('loans.return.form', $loan->id) }}" 
                                               class="btn-icon !w-9 !h-9 hover:!bg-success/10 hover:!text-success" 
                                               title="Kembalikan">
                                                <i class="fas fa-undo text-sm"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($loans->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $loans->links() }}
                </div>
            @endif

        @else
            {{-- ==================== EMPTY STATE ==================== --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-hand-holding text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada Peminjaman
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada peminjaman yang cocok dengan filter Anda.
                    @else
                        Belum ada transaksi peminjaman barang yang tercatat.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('loans.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('loans.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Peminjaman Baru
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

@endsection