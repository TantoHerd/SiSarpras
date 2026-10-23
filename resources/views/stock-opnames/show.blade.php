{{-- resources/views/stock-opnames/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Opname ' . $stockOpname->opname_number)
@section('page_title', 'Detail Sesi Opname')
@section('page_subtitle', 'Input stok fisik dan koreksi stok sistem')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('stock-opnames.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-clipboard-list text-xs"></i> Stok Opname
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep font-mono">{{ $stockOpname->opname_number }}</span>
</nav>

<div class="space-y-6">
    {{-- ============ HEADER CARD ============ --}}
    <div class="card">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
            <div class="min-w-0">
                <p class="text-caption text-steel uppercase tracking-wider mb-1">
                    No. Opname
                </p>
                <h1 class="text-heading-md text-ink-deep font-mono mb-3">
                    {{ $stockOpname->opname_number }}
                </h1>
                <span class="{{ $stockOpname->status_badge }}">
                    <i class="fas fa-circle text-xs mr-1.5"></i>
                    {{ $stockOpname->status_label }}
                </span>
            </div>

            @if($stockOpname->isDraft())
                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                            class="btn-ghost">
                        <i class="fas fa-times"></i>
                        Batalkan
                    </button>
                    <button type="button"
                            onclick="document.getElementById('complete-modal').classList.remove('hidden')"
                            class="btn-primary !bg-success hover:!bg-success/80">
                        <i class="fas fa-check"></i>
                        Selesaikan & Koreksi Stok
                    </button>
                </div>
            @endif
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-5 border-t border-hairline-soft">
            {{-- Lokasi --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Lokasi</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $stockOpname->location->name ?? '-' }}</p>
                    @if($stockOpname->location->floor)
                        <p class="text-caption text-steel">{{ $stockOpname->location->floor }}</p>
                    @endif
                </div>
            </div>

            {{-- Dibuka --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-play text-steel text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider">Dibuka</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ $stockOpname->started_at->translatedFormat('d F Y, H:i') }}
                    </p>
                    <p class="text-caption text-steel">
                        oleh {{ $stockOpname->starter->name ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Selesai (kalau ada) --}}
            @if($stockOpname->isCompleted())
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-success text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Selesai</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $stockOpname->completed_at->translatedFormat('d F Y, H:i') }}
                        </p>
                        <p class="text-caption text-steel">
                            oleh {{ $stockOpname->completer->name ?? '-' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Note --}}
        @if($stockOpname->note)
            <div class="mt-4 pt-4 border-t border-hairline-soft">
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Catatan</p>
                <p class="text-body-sm text-ink">{{ $stockOpname->note }}</p>
            </div>
        @endif
    </div>

    {{-- ============ STATISTIK SESI ============ --}}
    @php
        $totalSystem = $stockOpname->items->sum('system_stock');
        $totalPhysical = $stockOpname->items->sum('physical_stock');
        $totalDiff = $stockOpname->items->sum('difference');
        $itemsWithDiff = $stockOpname->items->where('difference', '!=', 0)->count();
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Item</p>
            <p class="text-heading-lg text-ink-deep">{{ $stockOpname->items->count() }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Stok Sistem</p>
            <p class="text-heading-lg text-ink-deep">{{ $totalSystem }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Stok Fisik</p>
            <p class="text-heading-lg text-primary">{{ $totalPhysical }}</p>
        </div>
        <div class="card-sm">
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Selisih</p>
            <p class="text-heading-lg {{ $totalDiff > 0 ? 'text-warning' : ($totalDiff < 0 ? 'text-critical' : 'text-success') }}">
                {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
            </p>
            <p class="text-caption text-steel">
                {{ $itemsWithDiff }} item berbeda
            </p>
        </div>
    </div>

    {{-- ============ FORM INPUT FISIK ============ --}}
    <form method="POST" 
          action="{{ route('stock-opnames.update-physical', $stockOpname->id) }}"
          id="physical-form">
        @csrf
        @method('PUT')

        <div class="card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-hairline-soft flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-body-md-bold text-ink-deep">Input Stok Fisik</h3>
                    <p class="text-caption text-steel">
                        @if($stockOpname->isDraft())
                            Isi jumlah fisik per item, lalu simpan atau selesaikan.
                        @else
                            Sesi sudah selesai. Data hanya bisa dilihat.
                        @endif
                    </p>
                </div>
                @if($stockOpname->isDraft())
                    <button type="submit" class="btn-ink">
                        <i class="fas fa-save"></i>
                        Simpan Input
                    </button>
                @endif
            </div>

            @if($stockOpname->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-surface-soft border-b border-hairline-soft">
                            <tr>
                                <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Barang</th>
                                <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Stok Sistem</th>
                                <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Stok Fisik</th>
                                <th class="text-center px-4 py-3 text-caption-bold text-steel uppercase tracking-wider">Selisih</th>
                                <th class="text-left px-4 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft">
                            @foreach($stockOpname->items as $opnameItem)
                                <tr class="hover:bg-surface-soft/50 transition-colors" 
                                    data-opname-item="{{ $opnameItem->id }}">
                                    {{-- Barang --}}
                                    <td class="px-4 py-3">
                                        <p class="text-body-sm-bold text-ink-deep">
                                            {{ $opnameItem->item->name ?? '-' }}
                                        </p>
                                        <p class="text-caption text-steel font-mono">
                                            {{ $opnameItem->item->code ?? '-' }}
                                        </p>
                                    </td>

                                    {{-- Stok Sistem --}}
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-body-sm-bold text-ink-deep">{{ $opnameItem->system_stock }}</span>
                                    </td>

                                    {{-- Stok Fisik --}}
                                    <td class="px-4 py-3 text-center">
                                        @if($stockOpname->isDraft())
                                            <input type="number"
                                                   name="items[{{ $opnameItem->id }}][physical_stock]"
                                                   value="{{ $opnameItem->physical_stock }}"
                                                   min="0"
                                                   oninput="calculateDiff({{ $opnameItem->id }}, {{ $opnameItem->system_stock }})"
                                                   class="form-input !w-24 !h-10 text-center mx-auto physical-input">
                                        @else
                                            <span class="text-body-sm-bold text-primary">{{ $opnameItem->physical_stock }}</span>
                                        @endif
                                    </td>

                                    {{-- Selisih --}}
                                    <td class="px-4 py-3 text-center">
                                        <span class="diff-display text-body-sm-bold
                                                    {{ $opnameItem->difference > 0 ? 'text-warning' : ($opnameItem->difference < 0 ? 'text-critical' : 'text-success') }}">
                                            {{ $opnameItem->difference > 0 ? '+' : '' }}{{ $opnameItem->difference }}
                                        </span>
                                    </td>

                                    {{-- Catatan --}}
                                    <td class="px-4 py-3 hidden lg:table-cell">
                                        @if($stockOpname->isDraft())
                                            <input type="text"
                                                   name="items[{{ $opnameItem->id }}][note]"
                                                   value="{{ $opnameItem->note }}"
                                                   placeholder="Catatan..."
                                                   maxlength="200"
                                                   class="form-input !h-9 !text-xs">
                                        @else
                                            <span class="text-caption text-steel">{{ $opnameItem->note ?? '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-circle bg-surface-soft mb-4">
                        <i class="fas fa-box-open text-steel text-2xl"></i>
                    </div>
                    <p class="text-body-sm text-steel">
                        Tidak ada barang consumable di lokasi ini.
                    </p>
                </div>
            @endif
        </div>
    </form>

    {{-- Action --}}
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('stock-opnames.index') }}" class="btn-ghost">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

</div>

{{-- ==================== MODAL COMPLETE ==================== --}}
<div id="complete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Selesaikan Sesi Opname?</h3>
        <p class="text-body-sm text-steel text-center mb-2">
            Stok sistem akan dikoreksi sesuai stok fisik yang Anda input.
        </p>
        <p class="text-caption text-critical text-center mb-6">
            <i class="fas fa-exclamation-triangle"></i>
            Tindakan ini tidak dapat dibatalkan.
        </p>

        <form method="POST" action="{{ route('stock-opnames.complete', $stockOpname->id) }}" class="flex gap-3">
            @csrf
            <button type="button" 
                    onclick="document.getElementById('complete-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-primary flex-1 !bg-success hover:!bg-success/80">
                <i class="fas fa-check"></i> Ya, Selesaikan
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL CANCEL ==================== --}}
<div id="cancel-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-warning/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-warning text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Batalkan Sesi Opname?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Sesi akan ditandai sebagai <strong>dibatalkan</strong>. Stok tidak akan dikoreksi.
        </p>

        <form method="POST" action="{{ route('stock-opnames.cancel', $stockOpname->id) }}" class="flex gap-3">
            @csrf
            <button type="button" 
                    onclick="document.getElementById('cancel-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-times"></i> Ya, Batalkan
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function calculateDiff(opnameItemId, systemStock) {
        const row = document.querySelector(`tr[data-opname-item="${opnameItemId}"]`);
        if (!row) return;

        const input = row.querySelector('.physical-input');
        const diffDisplay = row.querySelector('.diff-display');

        const physical = parseInt(input.value) || 0;
        const diff = physical - systemStock;

        // Update display
        diffDisplay.textContent = diff > 0 ? `+${diff}` : diff;

        // Update color
        diffDisplay.classList.remove('text-success', 'text-warning', 'text-critical');
        if (diff === 0) {
            diffDisplay.classList.add('text-success');
        } else if (diff > 0) {
            diffDisplay.classList.add('text-warning');
        } else {
            diffDisplay.classList.add('text-critical');
        }
    }
</script>
@endpush