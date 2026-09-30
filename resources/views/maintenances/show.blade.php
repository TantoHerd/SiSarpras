{{-- resources/views/maintenances/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Perawatan')
@section('page_title', 'Detail Perawatan')
@section('page_subtitle', 'Informasi lengkap perawatan barang')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('maintenances.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-tools text-xs"></i> Perawatan
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">#{{ str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Foto --}}
        <div class="card !p-0 overflow-hidden">
            @if($maintenance->item->image)
                <img src="{{ asset('storage/' . $maintenance->item->image) }}" 
                     alt="{{ $maintenance->item->name }}"
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
                    <p class="text-body-sm-bold text-ink-deep">{{ $maintenance->item->name }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kode</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $maintenance->item->code }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kategori</p>
                    <p class="text-body-sm text-ink">{{ $maintenance->item->category->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Lokasi</p>
                    <p class="text-body-sm text-ink">{{ $maintenance->item->location->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kondisi Saat Ini</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-caption-bold {{ 
                        match($maintenance->item->condition->value) {
                            'baik' => 'bg-success/10 text-success',
                            'rusak_ringan' => 'bg-warning/10 text-warning',
                            'rusak_berat' => 'bg-critical/10 text-critical',
                            default => 'bg-surface-soft text-steel',
                        }
                    }}">
                        {{ $maintenance->item->condition->label() }}
                    </span>
                </div>
            </div>
            <a href="{{ route('items.show', $maintenance->item->id) }}" 
               class="btn-ghost w-full justify-center mt-4">
                <i class="fas fa-external-link-alt"></i>
                Lihat Barang
            </a>
        </div>

        {{-- ============ Kartu "Tandai Selesai" (Khusus perbaikan in_progress) ============ --}}
        @if($maintenance->isInProgress() && $maintenance->type === 'perbaikan')
            <div class="card bg-success/5 border-success/20 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-success/10 blur-2xl pointer-events-none"></div>

                <div class="relative">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-success/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-success text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-body-md-bold text-ink-deep">Perbaikan Selesai?</h4>
                            <p class="text-caption text-steel mt-1">
                                Klik tombol di bawah jika barang sudah diperbaiki dan siap dipakai kembali.
                            </p>
                        </div>
                    </div>

                    {{-- Info waktu --}}
                    <div class="mb-4 p-3 rounded-xl bg-canvas border border-success/20">
                        <div class="flex items-center justify-between text-caption">
                            <span class="text-steel">Sudah diperbaiki sejak</span>
                            <span class="text-body-sm-bold text-success">
                                {{ $maintenance->days_in_progress }} hari
                            </span>
                        </div>
                    </div>

                    <button type="button"
                            onclick="document.getElementById('complete-modal').classList.remove('hidden')"
                            class="btn-primary w-full justify-center">
                        <i class="fas fa-check-circle"></i>
                        Tandai Perbaikan Selesai
                    </button>
                </div>
            </div>
        @endif

        {{-- Kartu Aksi --}}
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4">Aksi</h3>
            <div class="space-y-2">
                <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn-ghost w-full justify-center">
                    <i class="fas fa-pen"></i>
                    Edit Perawatan
                </a>
                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="btn-danger w-full justify-center">
                    <i class="fas fa-trash"></i>
                    Hapus Perawatan
                </button>
            </div>
        </div>

    </div>

    {{-- ==================== KOLOM KANAN ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                <p class="text-body-sm text-ink">{{ session('success') }}</p>
            </div>
        @endif

        {{-- ============ HEADER INFO ============ --}}
        <div class="card">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider mb-1">
                        Perawatan #{{ str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                    <h1 class="text-heading-md text-ink-deep mb-3">
                        {{ $maintenance->item->name }}
                    </h1>
                    @if($maintenance->type === 'rutin')
                        <span class="badge-info">
                            <i class="fas fa-sync-alt mr-1.5 text-xs"></i>
                            Perawatan Rutin
                        </span>
                    @else
                        <span class="badge-warning">
                            <i class="fas fa-wrench mr-1.5 text-xs"></i>
                            Perbaikan
                        </span>
                    @endif
                    {{-- Status Badge --}}
                    @if($maintenance->isInProgress())
                        <span class="badge-warning">
                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                            Sedang Diperbaiki
                        </span>
                    @else
                        <span class="badge-success">
                            <i class="fas fa-check mr-1.5 text-xs"></i>
                            Selesai
                        </span>
                    @endif
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-5 border-t border-hairline-soft">
                {{-- Tanggal --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-alt text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Tanggal Perawatan</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $maintenance->maintenance_date->translatedFormat('d F Y') }}
                        </p>
                        <p class="text-caption text-steel">
                            Dicatat {{ $maintenance->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Teknisi --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-cog text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Teknisi / Vendor</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $maintenance->technician ?? 'Tidak dicatat' }}
                        </p>
                    </div>
                </div>

                {{-- Biaya --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Biaya</p>
                        @if($maintenance->cost > 0)
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ format_currency($maintenance->cost) }}
                            </p>
                        @else
                            <span class="badge-success">Gratis</span>
                        @endif
                    </div>
                </div>

                {{-- Jadwal Berikutnya --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-plus text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Jadwal Berikutnya</p>
                        @if($maintenance->next_maintenance_date)
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $maintenance->next_maintenance_date->translatedFormat('d F Y') }}
                            </p>
                            @php
                                $daysUntil = now()->diffInDays($maintenance->next_maintenance_date, false);
                            @endphp
                            @if($daysUntil >= 0 && $daysUntil <= 7)
                                <span class="badge-warning text-caption">
                                    <i class="fas fa-clock mr-1 text-xs"></i>
                                    {{ $daysUntil }} hari lagi
                                </span>
                            @elseif($daysUntil < 0)
                                <span class="badge-critical text-caption">
                                    Terlambat {{ abs($daysUntil) }} hari
                                </span>
                            @else
                                <p class="text-caption text-steel">
                                    {{ $daysUntil }} hari lagi
                                </p>
                            @endif
                        @else
                            <p class="text-caption text-stone">Tidak dijadwalkan</p>
                        @endif
                    </div>
                </div>

                {{-- Deskripsi (full width) --}}
                @if($maintenance->description)
                    <div class="md:col-span-2 flex items-start gap-3 pt-4 border-t border-hairline-soft">
                        <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-comment-alt text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-caption text-steel uppercase tracking-wider">Deskripsi Perawatan</p>
                            <p class="text-body-sm text-ink mt-1 whitespace-pre-line">
                                {{ $maintenance->description }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Dicatat oleh --}}
                <div class="md:col-span-2 flex items-start gap-3 pt-4 border-t border-hairline-soft">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Dicatat Oleh</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $maintenance->creator->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>

                {{-- Selesai oleh (kalau sudah) --}}
                @if($maintenance->isCompleted() && $maintenance->completer)
                    <div class="md:col-span-2 flex items-start gap-3 pt-4 border-t border-hairline-soft">
                        <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-success text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-caption text-steel uppercase tracking-wider">Diselesaikan Oleh</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $maintenance->completer->name }}
                            </p>
                            <p class="text-caption text-steel">
                                {{ $maintenance->completed_at->translatedFormat('d F Y, H:i') }}
                            </p>
                            @if($maintenance->completion_note)
                                <div class="mt-2 p-3 rounded-xl bg-success/5 border border-success/20">
                                    <p class="text-caption text-steel mb-1">Catatan Hasil:</p>
                                    <p class="text-body-sm text-ink">{{ $maintenance->completion_note }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ RIWAYAT LAIN DARI BARANG YANG SAMA ============ --}}
        @if($otherMaintenances->count() > 0)
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-heading-sm text-ink-deep">Riwayat Perawatan Lain</h3>
                        <p class="text-body-sm text-steel mt-1">
                            Perawatan lain untuk barang yang sama
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($otherMaintenances as $other)
                        <a href="{{ route('maintenances.show', $other->id) }}"
                           class="flex items-center gap-4 p-3 rounded-xl bg-surface-soft hover:bg-primary/5 border border-transparent hover:border-primary/20 transition-all">
                            <div class="w-10 h-10 rounded-lg {{ $other->type === 'rutin' ? 'bg-primary/10' : 'bg-warning/10' }} flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $other->type === 'rutin' ? 'fa-sync-alt text-primary' : 'fa-wrench text-warning' }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-body-sm-bold text-ink-deep">
                                    {{ $other->type === 'rutin' ? 'Perawatan Rutin' : 'Perbaikan' }}
                                </p>
                                <p class="text-caption text-steel">
                                    {{ $other->maintenance_date->format('d M Y') }}
                                    @if($other->cost > 0)
                                        • {{ format_currency($other->cost) }}
                                    @endif
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-steel text-xs"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Data Perawatan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Perawatan ini akan dihapus dari riwayat. Tindakan ini tidak dapat dibatalkan.
        </p>
        <form method="POST" action="{{ route('maintenances.destroy', $maintenance->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL TANDAI SELESAI ==================== --}}
@if($maintenance->isInProgress() && $maintenance->type === 'perbaikan')
<div id="complete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-lg w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Tandai Perbaikan Selesai?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Barang <strong class="text-ink-deep">{{ $maintenance->item->name }}</strong> akan dikembalikan ke kondisi <strong>Baik</strong> dan status <strong>Tersedia</strong>.
        </p>

        <form method="POST" action="{{ route('maintenances.complete', $maintenance->id) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Catatan Hasil --}}
            <div>
                <label for="completion_note" class="form-label">Catatan Hasil Perbaikan</label>
                <textarea id="completion_note" 
                          name="completion_note" 
                          rows="3"
                          placeholder="Contoh: Lampu sudah diganti, sudah dites nyala normal..."
                          maxlength="500"
                          class="form-input !h-auto resize-none"></textarea>
                <p class="text-caption text-steel mt-1">Opsional.</p>
            </div>

            {{-- Biaya Final --}}
            <div>
                <label for="final_cost" class="form-label">Biaya Final (Rp)</label>
                <input type="number" 
                       id="final_cost" 
                       name="final_cost" 
                       value="{{ $maintenance->cost }}"
                       min="0"
                       step="1000"
                       class="form-input">
                <p class="text-caption text-steel mt-1">Ubah jika biaya aktual berbeda.</p>
            </div>

            {{-- Info --}}
            <div class="p-3 rounded-xl bg-success/5 border border-success/20">
                <div class="flex items-start gap-2">
                    <i class="fas fa-info-circle text-success mt-0.5 text-sm"></i>
                    <div>
                        <p class="text-caption-bold text-ink-deep">Yang akan terjadi:</p>
                        <ul class="text-caption text-steel mt-1 space-y-0.5 list-disc list-inside">
                            <li>Barang dikembalikan ke kondisi "Baik"</li>
                            <li>Status barang menjadi "Tersedia"</li>
                            <li>Barang bisa dipinjam lagi</li>
                            <li>Log aktivitas tersimpan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('complete-modal').classList.add('hidden')"
                        class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-check-circle"></i>
                    Ya, Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection