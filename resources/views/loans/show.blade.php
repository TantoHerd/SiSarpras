{{-- resources/views/loans/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('page_title', 'Detail Peminjaman')
@section('page_subtitle', 'Informasi lengkap transaksi peminjaman')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    @if(auth()->user()->isGuru())
        <a href="{{ route('loans.my') }}" class="hover:text-primary flex items-center gap-1.5">
            <i class="fas fa-clock text-xs"></i> Riwayat Pinjam
        </a>
    @else
        <a href="{{ route('loans.index') }}" class="hover:text-primary flex items-center gap-1.5">
            <i class="fas fa-hand-holding text-xs"></i> Peminjaman
        </a>
    @endif
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">#{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Barang --}}
        <div class="card !p-0 overflow-hidden">
            @if($loan->item->image)
                <img src="{{ asset('storage/' . $loan->item->image) }}" 
                     alt="{{ $loan->item->name }}"
                     class="w-full aspect-square object-cover">
            @else
                <div class="w-full aspect-square bg-surface-soft flex items-center justify-center">
                    <i class="fas fa-box text-steel text-6xl"></i>
                </div>
            @endif
        </div>

        {{-- Kartu Info Barang --}}
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4">
                <i class="fas fa-box text-primary mr-2"></i>
                Informasi Barang
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-caption text-steel">Nama Barang</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $loan->item->name }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kode</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $loan->item->code }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kategori</p>
                    <p class="text-body-sm text-ink">{{ $loan->item->category->name ?? '-' }}</p>
                </div>
                {{-- ← BARU: Jumlah Dipinjam --}}
                <div>
                    <p class="text-caption text-steel">Jumlah Dipinjam</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ $loan->quantity }} unit
                        @if($loan->item->isPerBatch())
                            <span class="text-caption text-steel font-normal">
                                (stok awal: {{ $loan->item->quantity + $loan->quantity }})
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-caption text-steel">Lokasi</p>
                    <p class="text-body-sm text-ink">{{ $loan->item->location->name ?? '-' }}</p>
                </div>
            </div>
            <a href="{{ route('items.show', $loan->item->id) }}" 
               class="btn-ghost w-full justify-center mt-4">
                <i class="fas fa-external-link-alt"></i>
                Lihat Barang
            </a>
        </div>

        {{-- Kartu Aksi (Petugas/Admin & belum dikembalikan) --}}
        @if((auth()->user()->isAdmin() || auth()->user()->isPetugas()) && !$loan->isReturned())
            <div class="card bg-primary/5 border-primary/20">
                <h3 class="text-body-md-bold text-ink-deep mb-4">
                    <i class="fas fa-cog text-primary mr-2"></i>
                    Aksi
                </h3>
                <a href="{{ route('loans.return.form', $loan->id) }}" 
                   class="btn-primary w-full justify-center">
                    <i class="fas fa-undo"></i>
                    Proses Pengembalian
                </a>
            </div>
        @endif

    </div>

    {{-- ==================== KOLOM KANAN ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                <p class="text-body-sm text-ink">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('warning'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-warning/10 border border-warning/20">
                <i class="fas fa-exclamation-triangle text-warning text-lg mt-0.5"></i>
                <p class="text-body-sm text-ink">{{ session('warning') }}</p>
            </div>
        @endif

        {{-- ============ HEADER INFO ============ --}}
        <div class="card">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider mb-1">
                        Peminjaman #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                    <h1 class="text-heading-md text-ink-deep mb-3">
                        {{ $loan->item->name }}
                    </h1>
                    @php
                        $statusColor = match($loan->status->value) {
                            'dipinjam' => 'badge-info',
                            'terlambat' => 'badge-critical',
                            'dikembalikan' => 'badge-success',
                            default => 'badge-neutral',
                        };
                    @endphp
                    <span class="{{ $statusColor }}">
                        <i class="fas fa-circle text-xs mr-1.5"></i>
                        {{ $loan->status->label() }}
                    </span>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-5 border-t border-hairline-soft">
                {{-- Peminjam --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Peminjam</p>
                        <p class="text-body-sm-bold text-ink-deep truncate">{{ $loan->borrower->name ?? '-' }}</p>
                        <p class="text-caption text-steel">NIP: {{ $loan->borrower->nip ?? '-' }}</p>
                    </div>
                </div>

                {{-- Diproses Oleh --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-check text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Diproses Oleh</p>
                        <p class="text-body-sm-bold text-ink-deep truncate">{{ $loan->processor->name ?? '-' }}</p>
                    </div>
                </div>

                {{-- Tgl Pinjam --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-plus text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Tanggal Pinjam</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $loan->loan_date->translatedFormat('d F Y') }}
                        </p>
                        <p class="text-caption text-steel">{{ $loan->loan_date->format('H:i') }} WIB</p>
                    </div>
                </div>

                {{-- Jatuh Tempo --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-times text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Jatuh Tempo</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $loan->due_date->translatedFormat('d F Y') }}
                        </p>
                        @if($loan->isOverdue() && !$loan->isReturned())
                            <p class="text-caption text-critical font-bold">
                                <i class="fas fa-exclamation-circle"></i>
                                Terlambat {{ $loan->daysOverdue() }} hari
                            </p>
                        @elseif(!$loan->isReturned())
                            <p class="text-caption text-steel">
                                {{ $loan->due_date->diffInDays(now()) }} hari lagi
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Keperluan (full width) --}}
                <div class="md:col-span-2 flex items-start gap-3 pt-4 border-t border-hairline-soft">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-comment-alt text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-caption text-steel uppercase tracking-wider">Keperluan</p>
                        <p class="text-body-sm text-ink mt-1">
                            {{ $loan->purpose ?? 'Tidak ada keterangan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ TIMELINE ============ --}}
        <div class="card">
            <h3 class="text-heading-sm text-ink-deep mb-6">Timeline</h3>

            <div class="relative pl-8">
                {{-- Vertical Line --}}
                <div class="absolute left-3 top-2 bottom-2 w-px bg-hairline-soft"></div>

                {{-- Event: Dibuat --}}
                <div class="relative pb-6">
                    <div class="absolute -left-8 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center z-10">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <p class="text-body-sm-bold text-ink-deep">Peminjaman Dibuat</p>
                    <p class="text-caption text-steel">
                        {{ $loan->loan_date->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                    <p class="text-caption text-steel mt-1">
                        oleh {{ $loan->processor->name ?? 'Sistem' }}
                    </p>
                </div>

                {{-- Event: Jatuh Tempo --}}
                <div class="relative pb-6">
                    <div class="absolute -left-8 w-6 h-6 rounded-full {{ $loan->isOverdue() && !$loan->isReturned() ? 'bg-critical' : 'bg-warning' }} text-white flex items-center justify-center z-10">
                        <i class="fas fa-clock text-xs"></i>
                    </div>
                    <p class="text-body-sm-bold text-ink-deep">Jatuh Tempo</p>
                    <p class="text-caption text-steel">
                        {{ $loan->due_date->translatedFormat('d F Y') }}
                    </p>
                    @if($loan->isOverdue() && !$loan->isReturned())
                        <p class="text-caption text-critical mt-1 font-bold">
                            Terlambat {{ $loan->daysOverdue() }} hari
                        </p>
                    @endif
                </div>

                {{-- Event: Dikembalikan (jika sudah) --}}
                @if($loan->isReturned())
                    <div class="relative">
                        <div class="absolute -left-8 w-6 h-6 rounded-full bg-success text-white flex items-center justify-center z-10">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <p class="text-body-sm-bold text-ink-deep">Dikembalikan</p>
                        <p class="text-caption text-steel">
                            {{ $loan->return_date->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                @else
                    <div class="relative opacity-40">
                        <div class="absolute -left-8 w-6 h-6 rounded-full bg-hairline text-white flex items-center justify-center z-10">
                            <i class="fas fa-hourglass-half text-xs"></i>
                        </div>
                        <p class="text-body-sm-bold text-steel">Belum Dikembalikan</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ INFO DENDA (jika ada) ============ --}}
        @if($loan->fine_amount > 0)
            <div class="card bg-critical/5 border-critical/20">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-critical/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-critical text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-body-md-bold text-ink-deep mb-1">Denda Keterlambatan</p>
                        <p class="text-heading-sm text-critical mb-3">
                            {{ format_currency($loan->fine_amount) }}
                        </p>
                        <span class="{{ $loan->is_fine_paid ? 'badge-success' : 'badge-critical' }}">
                            {{ $loan->is_fine_paid ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
    function updatePreview() {
        
    const quantity = document.getElementById('quantity');
    const qty = quantity ? parseInt(quantity.value) || 1 : 1;
    
    document.getElementById('prev-item').textContent = itemId 
        ? itemData[itemId] + (qty > 1 ? ` (${qty} unit)` : '')
        : '-';
}
</script>
@endpush