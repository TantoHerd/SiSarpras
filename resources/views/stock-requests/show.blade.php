{{-- resources/views/stock-requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Permintaan ' . $stockRequest->request_number)
@section('page_title', 'Detail Permintaan BHP')
@section('page_subtitle', 'Informasi lengkap permintaan barang habis pakai')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('stock-requests.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-boxes text-xs"></i> Permintaan BHP
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep font-mono">{{ $stockRequest->request_number }}</span>
</nav>

<div class="space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
            <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-critical/10 border border-critical/20">
            <i class="fas fa-exclamation-circle text-critical text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Info Pemohon --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">
                    <i class="fas fa-user text-primary mr-2"></i>
                    Pemohon
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-caption text-steel">Nama</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $stockRequest->requester->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-caption text-steel">Tipe</p>
                        <span class="badge-info">{{ ucfirst($stockRequest->requester_type) }}</span>
                    </div>
                    <div>
                        <p class="text-caption text-steel">Email</p>
                        <p class="text-body-sm text-ink">{{ $stockRequest->requester->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Info Waktu --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">
                    <i class="fas fa-clock text-primary mr-2"></i>
                    Waktu
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-caption text-steel">Diajukan</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $stockRequest->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                    @if($stockRequest->processed_at)
                        <div>
                            <p class="text-caption text-steel">Diproses</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $stockRequest->processed_at->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                            <p class="text-caption text-steel">
                                oleh {{ $stockRequest->processor->name ?? '-' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header --}}
            <div class="card">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider mb-1">
                            No. Permintaan
                        </p>
                        <h1 class="text-heading-md text-ink-deep font-mono mb-3">
                            {{ $stockRequest->request_number }}
                        </h1>
                        <span class="{{ $stockRequest->status_badge }}">
                            <i class="fas fa-circle text-xs mr-1.5"></i>
                            {{ $stockRequest->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Purpose --}}
                <div class="pt-5 border-t border-hairline-soft">
                    <p class="text-caption text-steel uppercase tracking-wider mb-2">Keperluan</p>
                    <p class="text-body-sm text-ink">
                        {{ $stockRequest->purpose }}
                    </p>
                </div>

                {{-- Rejection Reason --}}
                @if($stockRequest->isRejected() && $stockRequest->rejection_reason)
                    <div class="mt-4 p-4 rounded-xl bg-critical/5 border border-critical/20">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-times-circle text-critical mt-0.5"></i>
                            <div>
                                <p class="text-body-sm-bold text-critical mb-1">Alasan Penolakan</p>
                                <p class="text-body-sm text-ink">{{ $stockRequest->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Aksi Approve/Reject (kalau pending & user petugas/admin) --}}
            @if($stockRequest->isPending() && (auth()->user()->isAdmin() || auth()->user()->isPetugas()))
                <div class="card bg-primary/5 border-primary/20">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-gavel text-primary text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-body-md-bold text-ink-deep mb-1">Perlu Keputusan</h3>
                            <p class="text-body-sm text-steel mb-4">
                                Setelah disetujui, stok barang akan otomatis berkurang dan tercatat di log pergerakan.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <button type="button"
                                        onclick="document.getElementById('approve-modal').classList.remove('hidden')"
                                        class="btn-primary !bg-success hover:!bg-success/80">
                                    <i class="fas fa-check-circle"></i>
                                    Setujui
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                                        class="btn-danger">
                                    <i class="fas fa-times-circle"></i>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Daftar Barang --}}
            <div class="card !p-0 overflow-hidden">
                <div class="px-5 py-4 border-b border-hairline-soft">
                    <h3 class="text-body-md-bold text-ink-deep">Barang yang Diminta</h3>
                    <p class="text-caption text-steel">
                        {{ $stockRequest->items->count() }} jenis,
                        {{ $stockRequest->items->sum('quantity_requested') }} unit total
                    </p>
                </div>

                <div class="divide-y divide-hairline-soft">
                    @foreach($stockRequest->items as $reqItem)
                        <div class="p-5 flex items-center gap-4">
                            {{-- Foto --}}
                            @if($reqItem->item && $reqItem->item->image)
                                <img src="{{ asset('storage/' . $reqItem->item->image) }}" 
                                     alt="{{ $reqItem->item->name }}"
                                     class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-steel"></i>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-body-sm-bold text-ink-deep">
                                    {{ $reqItem->item->name ?? '-' }}
                                </p>
                                <p class="text-caption text-steel font-mono">
                                    {{ $reqItem->item->code ?? '-' }}
                                </p>
                                @if($reqItem->note)
                                    <p class="text-caption text-steel mt-1">
                                        <i class="fas fa-comment text-xs"></i>
                                        {{ $reqItem->note }}
                                    </p>
                                @endif
                            </div>

                            {{-- Qty --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-caption text-steel">Jumlah</p>
                                <p class="text-heading-sm text-primary font-bold">
                                    {{ $reqItem->quantity_requested }}
                                </p>
                                <p class="text-caption text-steel">
                                    dari {{ $reqItem->item->quantity ?? 0 }} stok
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Action --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                @if($stockRequest->isPending() && auth()->id() === $stockRequest->requester_id)
                    <button type="button" 
                            onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                            class="btn-danger">
                        <i class="fas fa-trash"></i>
                        Batalkan Permintaan
                    </button>
                @else
                    <div></div>
                @endif
                <a href="{{ route('stock-requests.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

        </div>
    </div>
</div>

{{-- ==================== MODAL APPROVE ==================== --}}
<div id="approve-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Setujui Permintaan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Stok barang akan otomatis berkurang setelah disetujui.
        </p>

        <form method="POST" action="{{ route('stock-requests.approve', $stockRequest->id) }}" class="flex gap-3">
            @csrf
            <button type="button" 
                    onclick="document.getElementById('approve-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-primary flex-1 !bg-success hover:!bg-success/80">
                <i class="fas fa-check"></i> Ya, Setujui
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL REJECT ==================== --}}
<div id="reject-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-times-circle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Tolak Permintaan?</h3>
        <p class="text-body-sm text-steel text-center mb-4">
            Mohon isi alasan penolakan.
        </p>

        <form method="POST" action="{{ route('stock-requests.reject', $stockRequest->id) }}" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason" class="form-label">Alasan <span class="text-critical">*</span></label>
                <textarea id="rejection_reason" 
                          name="rejection_reason" 
                          rows="3"
                          required
                          minlength="5"
                          maxlength="500"
                          class="form-input !h-auto resize-none"
                          placeholder="Contoh: Stok sedang kosong..."></textarea>
                <p class="text-caption text-steel mt-1">
                    Minimal 5 karakter.
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('reject-modal').classList.add('hidden')"
                        class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-danger flex-1">
                    <i class="fas fa-times"></i> Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus Permintaan?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Permintaan <strong>{{ $stockRequest->request_number }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('stock-requests.destroy', $stockRequest->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" 
                    onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </form>
    </div>
</div>

@endsection