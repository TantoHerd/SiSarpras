{{-- resources/views/portal-requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Permintaan #' . str_pad($portalRequest->id, 5, '0', STR_PAD_LEFT))
@section('page_title', 'Detail Permintaan Portal')
@section('page_subtitle', 'Review dan proses permintaan dari siswa / guru')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('portal-requests.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-inbox text-xs"></i> Permintaan Portal
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">#{{ str_pad($portalRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI: Data Pemohon ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Siswa / Guru --}}
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4">
                <i class="fas fa-user-graduate text-primary mr-2"></i>
                Data Pemohon
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-caption text-steel">Nama</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $portalRequest->student_name }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">NIS / NIP</p>
                    <p class="text-body-sm-bold text-ink-deep font-mono">{{ $portalRequest->nis }}</p>
                </div>
                <div>
                    <p class="text-caption text-steel">Kelas / Tipe</p>
                    <p class="text-body-sm text-ink">{{ $portalRequest->student_class }}</p>
                </div>
                @if($portalRequest->student_phone)
                    <div>
                        <p class="text-caption text-steel">No HP</p>
                        <p class="text-body-sm text-ink">
                            <i class="fas fa-phone text-xs text-steel mr-1"></i>
                            {{ $portalRequest->student_phone }}
                        </p>
                    </div>
                @endif
            </div>

            @if($portalRequest->student)
                <a href="{{ route('students.show', $portalRequest->student_id) }}" 
                   class="btn-ghost w-full justify-center mt-4">
                    <i class="fas fa-external-link-alt"></i>
                    Lihat Profil Siswa
                </a>
            @endif
        </div>

        {{-- Kartu IP & Meta --}}
        @if($portalRequest->ip_address)
            <div class="card bg-surface-soft/50">
                <h3 class="text-body-md-bold text-ink-deep mb-3">
                    <i class="fas fa-network-wired text-steel mr-2"></i>
                    Info Pengajuan
                </h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-caption text-steel">IP Address</p>
                        <p class="text-body-sm text-ink font-mono">{{ $portalRequest->ip_address }}</p>
                    </div>
                    <div>
                        <p class="text-caption text-steel">Diajukan Pada</p>
                        <p class="text-body-sm text-ink">
                            {{ $portalRequest->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- ==================== KOLOM KANAN: Detail & Aksi ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash Messages --}}
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

        {{-- ============ HEADER INFO ============ --}}
        <div class="card">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                <div class="min-w-0">
                    <p class="text-caption text-steel uppercase tracking-wider mb-1">
                        Permintaan #{{ str_pad($portalRequest->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                    <h1 class="text-heading-md text-ink-deep mb-3">
                        Peminjaman Barang
                    </h1>
                    <span class="{{ $portalRequest->status_badge }}">
                        <i class="fas fa-circle text-xs mr-1.5"></i>
                        {{ $portalRequest->status_label }}
                    </span>
                </div>

                <div class="text-right">
                    <p class="text-caption text-steel">Tanggal Pinjam</p>
                    <p class="text-body-sm-bold text-ink-deep">
                        {{ \Carbon\Carbon::parse($portalRequest->loan_date)->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-caption text-steel mt-1">Jatuh Tempo</p>
                    <p class="text-body-sm-bold text-critical">
                        {{ \Carbon\Carbon::parse($portalRequest->due_date)->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            {{-- Keperluan --}}
            <div class="pt-5 border-t border-hairline-soft">
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Keperluan</p>
                <p class="text-body-sm text-ink">
                    {{ $portalRequest->purpose }}
                </p>
            </div>
        </div>

        {{-- ============ AKSI (Kalau Pending) ============ --}}
        @if($portalRequest->isPending() && (auth()->user()->isAdmin() || auth()->user()->isPetugas()))
            <div class="card bg-primary/5 border-primary/20">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-gavel text-primary text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-body-md-bold text-ink-deep mb-1">Perlu Keputusan</h3>
                        <p class="text-body-sm text-steel mb-4">
                            Permintaan ini menunggu review Anda. Setelah disetujui, sistem akan otomatis membuat transaksi peminjaman untuk setiap barang.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <button type="button"
                                    onclick="document.getElementById('approve-modal').classList.remove('hidden')"
                                    class="btn-primary !bg-success hover:!bg-success/80">
                                <i class="fas fa-check-circle"></i>
                                Setujui Semua
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('reject-modal').classList.remove('hidden')"
                                    class="btn-danger">
                                <i class="fas fa-times-circle"></i>
                                Tolak Permintaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ DAFTAR BARANG (MULTI-ITEM) ============ --}}
        <div class="card !p-0 overflow-hidden">
            <div class="px-5 py-4 border-b border-hairline-soft">
                <h3 class="text-body-md-bold text-ink-deep">Barang yang Diminta</h3>
                <p class="text-caption text-steel">
                    {{ $portalRequest->items->count() }} jenis,
                    {{ $portalRequest->items->sum('quantity') }} unit total
                </p>
            </div>

            @if($portalRequest->items->count() > 0)
                <div class="divide-y divide-hairline-soft">
                    @foreach($portalRequest->items as $reqItem)
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
                                        <i class="fas fa-comment text-xs"></i> {{ $reqItem->note }}
                                    </p>
                                @endif
                            </div>

                            {{-- Qty --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-caption text-steel">Jumlah</p>
                                <p class="text-heading-sm text-primary font-bold">
                                    {{ $reqItem->quantity }}
                                </p>
                                <p class="text-caption text-steel">
                                    Stok: {{ $reqItem->item->quantity ?? 0 }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-steel">
                    Tidak ada item dalam permintaan ini.
                </div>
            @endif
        </div>

        {{-- ============ HASIL (Kalau sudah diproses) ============ --}}
        @if($portalRequest->isApproved())
            <div class="card bg-success/5 border-success/20">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-success/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-success text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-body-md-bold text-ink-deep mb-1">Permintaan Disetujui</h3>
                        <p class="text-body-sm text-steel">
                            Disetujui oleh <strong>{{ $portalRequest->processor->name ?? '-' }}</strong>
                            pada {{ $portalRequest->processed_at?->translatedFormat('d F Y, H:i') }} WIB.
                            Barang sudah masuk daftar pinjaman aktif.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($portalRequest->isRejected())
            <div class="card bg-critical/5 border-critical/20">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-critical/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times-circle text-critical text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-body-md-bold text-ink-deep mb-1">Permintaan Ditolak</h3>
                        <p class="text-body-sm text-steel mb-3">
                            Ditolak oleh <strong>{{ $portalRequest->processor->name ?? '-' }}</strong>
                            pada {{ $portalRequest->processed_at?->translatedFormat('d F Y, H:i') }} WIB.
                        </p>
                        @if($portalRequest->rejection_reason)
                            <div class="p-3 rounded-lg bg-canvas border border-hairline-soft">
                                <p class="text-caption text-steel mb-1">Alasan Penolakan:</p>
                                <p class="text-body-sm text-ink">
                                    {{ $portalRequest->rejection_reason }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

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
            Permintaan dari <strong class="text-ink-deep">{{ $portalRequest->student_name }}</strong>
            sebanyak <strong class="text-ink-deep">{{ $portalRequest->items->count() }} jenis barang</strong> akan disetujui.
            <br><br>
            <span class="text-caption">Transaksi peminjaman akan otomatis dibuat untuk setiap barang.</span>
        </p>

        <form method="POST" action="{{ route('portal-requests.approve', $portalRequest->id) }}" class="flex gap-3">
            @csrf
            <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="btn-ghost flex-1">
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
            Permintaan dari <strong class="text-ink-deep">{{ $portalRequest->student_name }}</strong> akan ditolak.
            Mohon isi alasan penolakan.
        </p>

        <form method="POST" action="{{ route('portal-requests.reject', $portalRequest->id) }}" class="space-y-4">
            @csrf
            <div>
                <label for="rejection_reason" class="form-label">
                    Alasan Penolakan <span class="text-critical">*</span>
                </label>
                <textarea id="rejection_reason" 
                          name="rejection_reason" 
                          rows="3"
                          required
                          minlength="5"
                          maxlength="500"
                          placeholder="Contoh: Barang sedang digunakan untuk kegiatan lain..."
                          class="form-input !h-auto resize-none @error('rejection_reason') form-input-error @enderror">{{ old('rejection_reason') }}</textarea>
                <p class="text-caption text-steel mt-1">
                    Minimal 5 karakter. Alasan akan ditampilkan ke pemohon.
                </p>
                @error('rejection_reason')
                    <p class="mt-2 text-body-sm text-critical-strong">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-danger flex-1">
                    <i class="fas fa-times"></i> Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>

@endsection