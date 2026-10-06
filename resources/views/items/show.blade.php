{{-- resources/views/items/show.blade.php --}}
@extends('layouts.app')

@section('title', $item->name)
@section('page_title', 'Detail Barang')
@section('page_subtitle', 'Informasi lengkap dan riwayat barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('items.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-boxes text-xs"></i>
        Data Barang
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep flex items-center gap-1.5">
        <i class="fas fa-file-alt text-xs"></i>
        {{ $item->name }}
    </span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI: Foto + Barcode ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Foto --}}
        <div class="card !p-0 overflow-hidden">
            @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" 
                    alt="{{ $item->name }}"
                    class="w-full aspect-square object-cover">
            @else
                <div class="w-full aspect-square bg-surface-soft flex flex-col items-center justify-center gap-3">
                    <div class="w-20 h-20 rounded-circle bg-canvas flex items-center justify-center">
                        <i class="fas fa-box text-steel text-3xl"></i>
                    </div>
                    <p class="text-caption text-steel">Belum ada foto</p>
                </div>
            @endif
        </div>

        {{-- Kartu Barcode --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-body-md-bold text-ink-deep">
                    <i class="fas fa-barcode text-primary mr-2"></i>
                    Barcode Barang
                </h3>
                <a href="{{ route('items.barcode', $item->id) }}" 
                target="_blank"
                class="text-caption-bold text-primary hover:underline">
                    <i class="fas fa-print"></i> Cetak
                </a>
            </div>

            {{-- QR Code Container --}}
            <div class="p-4 bg-canvas rounded-xl border border-hairline-soft">
                <div class="flex items-center justify-center">
                    <div class="inline-flex items-center justify-center bg-white p-2 rounded-lg">
                        {!! QrCode::size(180)->margin(0)->generate($item->code) !!}
                    </div>
                </div>
            </div>

            <p class="text-caption text-steel text-center mt-3 font-mono">
                {{ $item->code }}
            </p>
        </div>

        {{-- Kartu Aksi Cepat --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('items.edit', $item->id) }}" class="btn-ghost w-full justify-center">
                        <i class="fas fa-pen"></i>
                        Edit Barang
                    </a>
                    <button type="button" 
                            onclick="document.getElementById('delete-modal-show').classList.remove('hidden')"
                            class="btn-danger w-full justify-center">
                        <i class="fas fa-trash"></i>
                        Hapus Barang
                    </button>
                </div>
            </div>
        @endif

    </div>

    {{-- ==================== KOLOM KANAN: Info + Riwayat ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                <p class="text-body-sm text-ink">{{ session('success') }}</p>
            </div>
        @endif

        {{-- ============ HEADER INFO ============ --}}
        <div class="card">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="min-w-0">
                    <h1 class="text-heading-lg text-ink-deep mb-2">{{ $item->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $statusColor = match($item->status->value) {
                                'tersedia' => 'badge-success',
                                'dipinjam' => 'badge-warning',
                                'perbaikan' => 'badge-info',
                                'tidak_aktif' => 'badge-critical',
                                default => 'badge-neutral',
                            };
                            $conditionColor = match($item->condition->value) {
                                'baik' => 'bg-success/10 text-success',
                                'rusak_ringan' => 'bg-warning/10 text-warning',
                                'rusak_berat' => 'bg-critical/10 text-critical',
                                default => 'bg-surface-soft text-steel',
                            };
                        @endphp
                        <span class="{{ $statusColor }}">{{ $item->status->label() }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-caption-bold {{ $conditionColor }}">
                            {{ $item->condition->label() }}
                        </span>
                        <span class="badge-neutral">{{ $item->category->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-hairline-soft">

                {{-- Kode --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-barcode text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Kode Barang</p>
                        <p class="text-body-sm-bold text-ink-deep font-mono">{{ $item->code }}</p>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Lokasi</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $item->location->name ?? '-' }}
                            @if($item->location->floor)
                                <span class="text-steel font-normal">({{ $item->location->floor }})</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Merk --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tag text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Merk</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $item->brand ?? '-' }}</p>
                    </div>
                </div>

                {{-- Tipe --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-cube text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Tipe</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $item->type ?? '-' }}</p>
                    </div>
                </div>

                {{-- Serial Number --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-fingerprint text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Nomor Seri</p>
                        <p class="text-body-sm-bold text-ink-deep font-mono">{{ $item->serial_number ?? '-' }}</p>
                    </div>
                </div>

                {{-- Tahun --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Tahun Perolehan</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $item->purchase_year ?? '-' }}</p>
                    </div>
                </div>

                {{-- Harga --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Harga Beli</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $item->price ? format_currency($item->price) : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Jumlah --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-cubes text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Jumlah</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $item->quantity }} unit</p>
                    </div>
                </div>
                {{-- Sumber Dana --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Sumber Dana</p>
                        @if($item->fundingSource)
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $item->fundingSource->code }} - {{ $item->fundingSource->name }}
                        </p>
                        @else
                        <p class="text-body-sm text-stone italic">Belum ditentukan</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ RIWAYAT PERUBAHAN ============ --}}
        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-heading-sm text-ink-deep">Riwayat Perubahan</h3>
                    <p class="text-body-sm text-steel mt-1">20 aktivitas terakhir</p>
                </div>
            </div>

            @if($item->histories->count() > 0)
                <div class="relative">
                    {{-- Vertical Line --}}
                    <div class="absolute left-5 top-2 bottom-2 w-px bg-hairline-soft"></div>

                    <div class="space-y-5">
                        @foreach($item->histories as $history)
                            @php
                                $icon = match($history->action) {
                                    'created' => ['fa-plus-circle', 'text-success', 'bg-success/10'],
                                    'updated' => ['fa-pen', 'text-primary', 'bg-primary/10'],
                                    'deleted' => ['fa-trash', 'text-critical', 'bg-critical/10'],
                                    'restored' => ['fa-undo', 'text-warning', 'bg-warning/10'],
                                    'borrowed' => ['fa-hand-holding', 'text-warning', 'bg-warning/10'],
                                    'returned' => ['fa-check', 'text-success', 'bg-success/10'],
                                    'repaired' => ['fa-tools', 'text-oculus', 'bg-oculus/10'],
                                    default => ['fa-circle', 'text-steel', 'bg-surface-soft'],
                                };
                            @endphp

                            <div class="relative flex items-start gap-4 pl-2">
                                {{-- Icon Circle --}}
                                <div class="w-8 h-8 rounded-circle {{ $icon[2] }} flex items-center justify-center flex-shrink-0 relative z-10">
                                    <i class="fas {{ $icon[0] }} {{ $icon[1] }} text-xs"></i>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0 pt-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span class="badge-neutral text-caption-bold">
                                            {{ $history->action_label }}
                                        </span>
                                        <span class="text-caption text-steel">
                                            oleh <strong class="text-ink">{{ $history->user->name ?? 'Sistem' }}</strong>
                                        </span>
                                    </div>
                                    <p class="text-caption text-steel">
                                        {{ $history->created_at->translatedFormat('d F Y, H:i') }}
                                        <span class="text-stone">•</span>
                                        {{ $history->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-stone">
                    <i class="fas fa-history text-4xl mb-3"></i>
                    <p class="text-body-sm">Belum ada riwayat perubahan</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ==================== MODAL HAPUS ==================== --}}
<div id="delete-modal-show" 
     class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">

        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>

        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Hapus Barang Ini?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Barang <strong class="text-ink-deep">{{ $item->name }}</strong> akan dihapus dari sistem.
            Data dapat dipulihkan oleh Admin.
        </p>

        <form method="POST" action="{{ route('items.destroy', $item->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')

            <button type="button" 
                    onclick="document.getElementById('delete-modal-show').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i>
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

@endsection