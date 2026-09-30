{{-- resources/views/loans/my.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('page_title', 'Riwayat Peminjaman Saya')
@section('page_subtitle', 'Daftar semua peminjaman yang pernah Anda lakukan')

@section('content')

<div class="space-y-6">

    {{-- ==================== HEADER ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $loans->total() }}</span> peminjaman
            </p>
        </div>
        <a href="{{ route('loans.request') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- ==================== FILTER ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('loans.my') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama barang atau kode..."
                       class="form-input pl-11">
            </div>
            <button type="submit" class="btn-ink">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>

        {{-- Pill Tabs --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('loans.my', array_merge(request()->query(), ['status' => ''])) }}"
               class="pill-tab {{ empty($filters['status']) ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            @foreach(\App\Enums\LoanStatusEnum::cases() as $status)
                <a href="{{ route('loans.my', array_merge(request()->query(), ['status' => $status->value])) }}"
                   class="pill-tab {{ ($filters['status'] ?? '') === $status->value ? 'pill-tab-active' : '' }}">
                    {{ $status->label() }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ==================== LIST ==================== --}}
    @if($loans->count() > 0)
        <div class="space-y-3">
            @foreach($loans as $loan)
                <div class="card hover:border-primary/30 transition-colors">
                    <div class="flex flex-wrap items-center gap-4">

                        {{-- Foto --}}
                        @if($loan->item->image)
                            <img src="{{ asset('storage/' . $loan->item->image) }}" 
                                 alt="{{ $loan->item->name }}"
                                 class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-surface-soft flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-steel text-xl"></i>
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h4 class="text-body-md-bold text-ink-deep">{{ $loan->item->name }}</h4>
                                @php
                                    $statusColor = match($loan->status->value) {
                                        'dipinjam' => 'badge-info',
                                        'terlambat' => 'badge-critical',
                                        'dikembalikan' => 'badge-success',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <span class="{{ $statusColor }}">{{ $loan->status->label() }}</span>
                            </div>
                            <p class="text-caption text-steel font-mono">{{ $loan->item->code }}</p>
                            <p class="text-body-sm text-steel mt-1">
                                <i class="fas fa-calendar text-xs"></i>
                                {{ $loan->loan_date->format('d M Y') }} — 
                                @if($loan->isReturned())
                                    {{ $loan->return_date->format('d M Y') }}
                                @else
                                    {{ $loan->due_date->format('d M Y') }}
                                    @if($loan->isOverdue())
                                        <span class="text-critical font-bold">
                                            (Terlambat {{ $loan->daysOverdue() }} hari)
                                        </span>
                                    @endif
                                @endif
                            </p>
                        </div>

                        {{-- Action --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn-ghost btn-sm">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($loans->hasPages())
            <div class="card">
                {{ $loans->links() }}
            </div>
        @endif

    @else
        <div class="card py-16 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                <i class="fas fa-inbox text-steel text-3xl"></i>
            </div>
            <h3 class="text-heading-sm text-ink-deep mb-2">
                @if(array_filter($filters))
                    Tidak Ada Hasil
                @else
                    Belum Ada Riwayat
                @endif
            </h3>
            <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                @if(array_filter($filters))
                    Tidak ada peminjaman yang cocok dengan filter Anda.
                @else
                    Anda belum pernah melakukan peminjaman barang.
                @endif
            </p>
            @if(array_filter($filters))
                <a href="{{ route('loans.my') }}" class="btn-ghost">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            @else
                <a href="{{ route('loans.request') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Ajukan Peminjaman
                </a>
            @endif
        </div>
    @endif

</div>

@endsection