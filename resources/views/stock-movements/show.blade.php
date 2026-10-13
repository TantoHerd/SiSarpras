{{-- resources/views/stock-movements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Pergerakan #' . str_pad($stockMovement->id, 5, '0', STR_PAD_LEFT))
@section('page_title', 'Detail Pergerakan Stok')
@section('page_subtitle', 'Informasi lengkap 1 pergerakan stok')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('stock-movements.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-exchange-alt text-xs"></i> Pergerakan Stok
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">#{{ str_pad($stockMovement->id, 5, '0', STR_PAD_LEFT) }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI: Barang ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Barang --}}
        <div class="card !p-0 overflow-hidden">
            @if($stockMovement->item && $stockMovement->item->image)
                <img src="{{ asset('storage/' . $stockMovement->item->image) }}" 
                     alt="{{ $stockMovement->item->name }}"
                     class="w-full aspect-square object-cover">
            @else
                <div class="w-full aspect-square bg-surface-soft flex items-center justify-center">
                    <i class="fas fa-box text-steel text-6xl"></i>
                </div>
            @endif
        </div>

        {{-- Info Barang --}}
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4">
                <i class="fas fa-box text-primary mr-2"></i>
                Barang
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-caption text-steel">Nama</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $stockMovement->item->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kode</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $stockMovement->item->code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kategori</p>
                    <p class="text-body-sm text-ink">{{ $stockMovement->item->category->name ?? '-' }}</p>
                </div>
                <div class="pt-3 border-t border-hairline-soft">
                    <p class="text-caption text-steel">Stok Saat Ini</p>
                    <p class="text-body-md-bold text-primary">{{ $stockMovement->item->quantity ?? 0 }} unit</p>
                </div>
            </div>

            @if($stockMovement->item)
                <a href="{{ route('items.show', $stockMovement->item_id) }}" 
                   class="btn-ghost w-full justify-center mt-4">
                    <i class="fas fa-external-link-alt"></i>
                    Lihat Barang
                </a>
            @endif
        </div>

    </div>

    {{-- ==================== KOLOM KANAN: Detail ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Header Info --}}
        <div class="card">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider mb-1">
                        Pergerakan #{{ str_pad($stockMovement->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                    <h1 class="text-heading-md text-ink-deep mb-3">
                        {{ $stockMovement->type->label() }}
                    </h1>
                    <span class="{{ $stockMovement->type->badge() }} inline-flex items-center gap-1.5">
                        <i class="fas {{ $stockMovement->type->icon() }} text-xs"></i>
                        {{ $stockMovement->type->label() }}
                    </span>
                </div>

                <div class="text-right">
                    <p class="text-caption text-steel">Waktu</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ $stockMovement->created_at->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-caption text-steel">
                        {{ $stockMovement->created_at->format('H:i') }} WIB
                    </p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-5 border-t border-hairline-soft">
                {{-- Jumlah --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sort-numeric-up text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Jumlah</p>
                        <p class="text-body-md-bold 
                                  {{ $stockMovement->type->value === 'in' ? 'text-success' : ($stockMovement->type->value === 'out' ? 'text-critical' : 'text-warning') }}">
                            {{ $stockMovement->type->value === 'out' ? '-' : '+' }}{{ $stockMovement->quantity }}
                        </p>
                    </div>
                </div>

                {{-- Stok Sebelum --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrow-left text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Stok Sebelum</p>
                        <p class="text-body-md-bold text-ink-deep">{{ $stockMovement->stock_before }}</p>
                    </div>
                </div>

                {{-- Stok Sesudah --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrow-right text-primary text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Stok Sesudah</p>
                        <p class="text-body-md-bold text-primary">{{ $stockMovement->stock_after }}</p>
                    </div>
                </div>

                {{-- Oleh --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Dicatat Oleh</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $stockMovement->creator->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>

                {{-- Reference --}}
                @if($stockMovement->reference_type)
                    <div class="md:col-span-2 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-link text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel uppercase tracking-wider">Referensi</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                @if($stockMovement->reference_type === 'stock_request')
                                    <a href="{{ route('stock-requests.show', $stockMovement->reference_id) }}" 
                                       class="text-primary hover:underline">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                        Permintaan BHP #{{ str_pad($stockMovement->reference_id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                @elseif($stockMovement->reference_type === 'stock_opname')
                                    <a href="#" class="text-primary hover:underline">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                        Stok Opname #{{ str_pad($stockMovement->reference_id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                @else
                                    {{ ucfirst($stockMovement->reference_type) }} #{{ $stockMovement->reference_id }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Note --}}
                @if($stockMovement->note)
                    <div class="md:col-span-3 flex items-start gap-3 pt-4 border-t border-hairline-soft">
                        <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-comment-alt text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-caption text-steel uppercase tracking-wider">Catatan</p>
                            <p class="text-body-sm text-ink mt-1">
                                {{ $stockMovement->note }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Back Button --}}
        <div class="flex justify-end">
            <a href="{{ route('stock-movements.index') }}" class="btn-ghost">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar
            </a>
        </div>

    </div>
</div>

@endsection