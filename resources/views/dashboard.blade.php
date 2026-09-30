{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan data inventaris sekolah')

@section('content')

<div class="space-y-8">

    {{-- ==================== HERO BAND ==================== --}}
    <section class="card-promo relative overflow-hidden">
        {{-- Decorative gradient circle --}}
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-circle 
                    bg-primary/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-circle 
                    bg-primary-soft/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <p class="text-body-sm-bold text-primary-soft uppercase tracking-wider mb-3">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h2 class="text-heading-lg text-white leading-tight mb-3">
                Selamat datang kembali,<br>
                {{ Str::words(auth()->user()->name, 2, '') }} 👋
            </h2>
            <p class="text-subtitle-md text-white/70 mb-6">
                Anda login sebagai
                <span class="text-white font-semibold">{{ auth()->user()->role->name ?? '-' }}</span>.
                Berikut ringkasan kondisi inventaris sekolah hari ini.
            </p>

            <div class="flex flex-wrap gap-3">
                @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
                    <a href="{{ route('items.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </a>
                    <a href="{{ route('reports.index') }}" 
                       class="btn-secondary !text-white !border-white hover:!bg-white hover:!text-ink-deep">
                        <i class="fas fa-chart-bar"></i> Lihat Laporan
                    </a>
                @elseif(auth()->user()->isGuru())
                    <a href="{{ route('loans.request') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Ajukan Peminjaman
                    </a>
                    <a href="{{ route('loans.my') }}" 
                       class="btn-secondary !text-white !border-white hover:!bg-white hover:!text-ink-deep">
                        <i class="fas fa-clock"></i> Riwayat Saya
                    </a>
                @elseif(auth()->user()->isKepalaSekolah())
                    <a href="{{ route('reports.index') }}" class="btn-primary">
                        <i class="fas fa-chart-bar"></i> Lihat Laporan
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ==================== STATISTIK INVENTARIS ==================== --}}
    <section>
        <div class="flex items-end justify-between mb-4">
            <div>
                <h3 class="text-heading-sm text-ink-deep">Statistik Inventaris</h3>
                <p class="text-body-sm text-steel mt-1">Ringkasan kondisi barang sekolah</p>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
                <a href="{{ route('items.index') }}" 
                   class="text-body-sm-bold text-primary hover:text-primary-deep inline-flex items-center gap-1.5">
                    Lihat semua 
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total Barang --}}
            <div class="card-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-boxes text-primary"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Barang</p>
                <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
            </div>

            {{-- Tersedia --}}
            <div class="card-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Tersedia</p>
                <p class="text-heading-lg text-ink-deep">{{ number_format($stats['available']) }}</p>
            </div>

            {{-- Dipinjam --}}
            <div class="card-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                        <i class="fas fa-hand-holding text-warning"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Dipinjam</p>
                <p class="text-heading-lg text-ink-deep">{{ number_format($stats['borrowed']) }}</p>
            </div>

            {{-- Rusak Berat --}}
            <div class="card-sm">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-critical"></i>
                    </div>
                </div>
                <p class="text-caption text-steel uppercase tracking-wider mb-1">Rusak Berat</p>
                <p class="text-heading-lg text-ink-deep">{{ number_format($stats['damaged']) }}</p>
            </div>
        </div>
    </section>

    {{-- ==================== PERLU PERHATIAN (Ringkasan Peminjaman) ==================== --}}
    @if($loanStats && (auth()->user()->isAdmin() || auth()->user()->isPetugas() || auth()->user()->isKepalaSekolah()))
    <section>
        <div class="flex items-end justify-between mb-4">
            <div>
                <h3 class="text-heading-sm text-ink-deep">Perlu Perhatian</h3>
                <p class="text-body-sm text-steel mt-1">Ringkasan peminjaman yang butuh tindakan</p>
            </div>
            <a href="{{ route('loans.index') }}" 
               class="text-body-sm-bold text-primary hover:text-primary-deep inline-flex items-center gap-1.5">
                Lihat semua 
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Card: Terlambat (Kondisional) --}}
            @if($loanStats['overdue'] > 0)
                <div class="card border-critical/30 bg-gradient-to-br from-critical/5 to-critical/0 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-critical/10 blur-2xl pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-critical/10 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-critical"></i>
                            </div>
                            <span class="badge-critical animate-pulse">Urgent</span>
                        </div>

                        <p class="text-caption text-steel uppercase tracking-wider mb-1">Terlambat</p>
                        <p class="text-heading-lg text-ink-deep mb-3">
                            {{ number_format($loanStats['overdue']) }}
                        </p>

                        <a href="{{ route('loans.index', ['status' => 'terlambat']) }}" 
                           class="inline-flex items-center gap-1.5 text-caption-bold text-critical hover:underline">
                            Lihat daftar
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Card: Sedang Dipinjam --}}
            <div class="card relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>

                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-hand-holding text-primary"></i>
                        </div>
                    </div>

                    <p class="text-caption text-steel uppercase tracking-wider mb-1">Sedang Dipinjam</p>
                    <p class="text-heading-lg text-ink-deep mb-3">
                        {{ number_format($loanStats['active']) }}
                    </p>

                    <a href="{{ route('loans.index', ['status' => 'dipinjam']) }}" 
                       class="inline-flex items-center gap-1.5 text-caption-bold text-primary hover:underline">
                        Lihat daftar
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- Card: Kembali Hari Ini --}}
            <div class="card relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-success/10 blur-2xl pointer-events-none"></div>

                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl bg-success/10 flex items-center justify-center">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>

                    <p class="text-caption text-steel uppercase tracking-wider mb-1">Kembali Hari Ini</p>
                    <p class="text-heading-lg text-ink-deep mb-3">
                        {{ number_format($loanStats['returned_today']) }}
                    </p>

                    <p class="text-caption text-steel">
                        Total: 
                        <strong class="text-ink-deep">{{ number_format($loanStats['total']) }}</strong> 
                        peminjaman
                    </p>
                </div>
            </div>

            {{-- ============ Card: Maintenance Jatuh Tempo (Kondisional) ============ --}}
            @if(isset($maintenanceStats) && $maintenanceStats['upcoming'] > 0)
                <div class="card border-warning/30 bg-gradient-to-br from-warning/5 to-warning/0 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-warning/10 blur-2xl pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-xl bg-warning/10 flex items-center justify-center">
                                <i class="fas fa-tools text-warning"></i>
                            </div>
                            <span class="badge-warning">Jadwal</span>
                        </div>

                        <p class="text-caption text-steel uppercase tracking-wider mb-1">Maintenance Jatuh Tempo</p>
                        <p class="text-heading-lg text-ink-deep mb-3">
                            {{ number_format($maintenanceStats['upcoming']) }}
                        </p>

                        <a href="{{ route('maintenances.index') }}" 
                        class="inline-flex items-center gap-1.5 text-caption-bold text-warning hover:underline">
                            Lihat daftar
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </section>
    @endif

    {{-- ==================== JADWAL MAINTENANCE MENDATANG ==================== --}}
    @if(isset($upcomingMaintenances) && $upcomingMaintenances && $upcomingMaintenances->count() > 0)
        <section>
            <div class="flex items-end justify-between mb-4">
                <div>
                    <h3 class="text-heading-sm text-ink-deep">Jadwal Maintenance Mendatang</h3>
                    <p class="text-body-sm text-steel mt-1">Barang yang butuh perawatan dalam 7 hari ke depan</p>
                </div>
                <a href="{{ route('maintenances.index') }}" 
                class="text-body-sm-bold text-primary hover:text-primary-deep inline-flex items-center gap-1.5">
                    Lihat semua 
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="card">
                <div class="space-y-3">
                    @foreach($upcomingMaintenances as $maintenance)
                        <a href="{{ route('maintenances.show', $maintenance->id) }}"
                        class="flex items-center gap-4 p-3 rounded-xl bg-surface-soft hover:bg-warning/5 border border-transparent hover:border-warning/20 transition-all">
                            @if($maintenance->item->image)
                                <img src="{{ asset('storage/' . $maintenance->item->image) }}" 
                                    alt="{{ $maintenance->item->name }}"
                                    class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-tools text-warning"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-body-sm-bold text-ink-deep truncate">
                                    {{ $maintenance->item->name }}
                                </p>
                                <p class="text-caption text-steel">
                                    <i class="fas fa-map-marker-alt text-xs"></i>
                                    {{ $maintenance->item->location->name ?? '-' }}
                                    • Jadwal: {{ $maintenance->next_maintenance_date->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @php
                                    $daysUntil = now()->startOfDay()->diffInDays($maintenance->next_maintenance_date->startOfDay(), false);
                                @endphp
                                @if($daysUntil == 0)
                                    <span class="badge-critical">Hari Ini</span>
                                @elseif($daysUntil <= 3)
                                    <span class="badge-warning">{{ $daysUntil }} hari lagi</span>
                                @else
                                    <span class="badge-neutral">{{ $daysUntil }} hari lagi</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ==================== SEDANG DIPERBAIKI ==================== --}}
    @if(isset($inProgressMaintenances) && $inProgressMaintenances && $inProgressMaintenances->count() > 0)
        <section>
            <div class="flex items-end justify-between mb-4">
                <div>
                    <h3 class="text-heading-sm text-ink-deep">Sedang Diperbaiki</h3>
                    <p class="text-body-sm text-steel mt-1">Barang yang masih dalam proses perbaikan</p>
                </div>
                <a href="{{ route('maintenances.index', ['status' => 'in_progress']) }}" 
                class="text-body-sm-bold text-primary hover:text-primary-deep inline-flex items-center gap-1.5">
                    Lihat semua 
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="card">
                <div class="space-y-3">
                    @foreach($inProgressMaintenances as $maintenance)
                        <div class="flex flex-wrap items-center gap-4 p-3 rounded-xl bg-surface-soft">
                            @if($maintenance->item->image)
                                <img src="{{ asset('storage/' . $maintenance->item->image) }}" 
                                    alt="{{ $maintenance->item->name }}"
                                    class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-tools text-warning"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-body-sm-bold text-ink-deep truncate">
                                    {{ $maintenance->item->name }}
                                </p>
                                <p class="text-caption text-steel truncate">
                                    <i class="fas fa-user-cog text-xs"></i>
                                    {{ $maintenance->technician ?? 'Tanpa teknisi' }}
                                    <span class="text-stone">•</span>
                                    {{ $maintenance->maintenance_date->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($maintenance->days_in_progress >= 7)
                                    <span class="badge-critical">
                                        <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>
                                        {{ $maintenance->days_in_progress }} hari
                                    </span>
                                @else
                                    <span class="badge-warning">
                                        {{ $maintenance->days_in_progress }} hari
                                    </span>
                                @endif
                                <button type="button"
                                        onclick="openCompleteModalFromDashboard({{ $maintenance->id }}, '{{ addslashes($maintenance->item->name) }}', {{ $maintenance->cost }})"
                                        class="btn-ghost btn-sm !text-success hover:!bg-success/10">
                                    <i class="fas fa-check-circle"></i>
                                    Selesai
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ==================== CONTENT GRID ==================== --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ============ PINJAMAN AKTIF (Admin & Petugas) ============ --}}
        @if($activeLoans)
            <div class="lg:col-span-2 card">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-heading-sm text-ink-deep">Peminjaman Aktif</h3>
                        <p class="text-body-sm text-steel">5 transaksi terbaru</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="btn-ghost btn-sm">Lihat Semua</a>
                </div>

                @if($activeLoans->count() > 0)
                    <div class="space-y-3">
                        @foreach($activeLoans as $loan)
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-soft">
                                @if($loan->item->image)
                                    <img src="{{ asset('storage/' . $loan->item->image) }}" 
                                         alt="{{ $loan->item->name }}"
                                         class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-white text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-sm-bold text-ink-deep truncate">{{ $loan->item->name }}</p>
                                    <p class="text-caption text-steel truncate">
                                        Dipinjam: {{ $loan->borrower->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if($loan->isOverdue())
                                        <span class="badge-critical">Terlambat</span>
                                        <p class="text-caption text-critical mt-1">
                                            {{ $loan->daysOverdue() }} hari
                                        </p>
                                    @else
                                        <span class="badge-success">Aktif</span>
                                        <p class="text-caption text-steel mt-1">
                                            {{ $loan->due_date->diffInDays(now()) }} hari lagi
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-stone">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-body-sm">Tidak ada peminjaman aktif</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- ============ PINJAMAN SAYA (Guru) ============ --}}
        @if($myLoans)
            <div class="lg:col-span-2 card">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-heading-sm text-ink-deep">Pinjaman Saya</h3>
                        <p class="text-body-sm text-steel">Riwayat pinjaman aktif</p>
                    </div>
                    <a href="{{ route('loans.my') }}" class="btn-ghost btn-sm">Lihat Semua</a>
                </div>

                @if($myLoans->count() > 0)
                    <div class="space-y-3">
                        @foreach($myLoans as $loan)
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-soft">
                                @if($loan->item->image)
                                    <img src="{{ asset('storage/' . $loan->item->image) }}" 
                                         alt="{{ $loan->item->name }}"
                                         class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-white text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-sm-bold text-ink-deep truncate">{{ $loan->item->name }}</p>
                                    <p class="text-caption text-steel truncate">{{ $loan->item->code }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if($loan->isOverdue())
                                        <span class="badge-critical">Terlambat</span>
                                    @else
                                        <span class="badge-success">Aktif</span>
                                        <p class="text-caption text-steel mt-1">
                                            {{ $loan->due_date->format('d M') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-stone">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-body-sm">Anda belum memiliki pinjaman aktif</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- ============ SIDEBAR KANAN ============ --}}
        <div class="space-y-6">

            {{-- Aksi Cepat --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isPetugas())
                <div class="card">
                    <h3 class="text-body-md-bold text-ink-deep mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('items.create') }}" 
                           class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-primary/5 
                                  hover:bg-primary/10 transition-colors text-center">
                            <i class="fas fa-plus-circle text-primary text-xl"></i>
                            <span class="text-caption-bold text-ink">Tambah</span>
                        </a>
                        <a href="{{ route('loans.create') }}" 
                           class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-success/5 
                                  hover:bg-success/10 transition-colors text-center">
                            <i class="fas fa-hand-holding text-success text-xl"></i>
                            <span class="text-caption-bold text-ink">Pinjam</span>
                        </a>
                        <a href="#" 
                           class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-warning/5 
                                  hover:bg-warning/10 transition-colors text-center">
                            <i class="fas fa-tools text-warning text-xl"></i>
                            <span class="text-caption-bold text-ink">Perawatan</span>
                        </a>
                        <a href="{{ route('reports.index') }}" 
                           class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-oculus/5 
                                  hover:bg-oculus/10 transition-colors text-center">
                            <i class="fas fa-chart-bar text-oculus text-xl"></i>
                            <span class="text-caption-bold text-ink">Laporan</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Info Sekolah --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-4">Informasi Sekolah</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-school text-steel mt-1 w-4 text-sm"></i>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Nama</p>
                            <p class="text-body-sm-bold text-ink-deep truncate">
                                {{ setting('school_name', '-') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-steel mt-1 w-4 text-sm"></i>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Alamat</p>
                            <p class="text-body-sm text-ink-deep">
                                {{ setting('school_address', '-') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-phone text-steel mt-1 w-4 text-sm"></i>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Telepon</p>
                            <p class="text-body-sm text-ink-deep">
                                {{ setting('school_phone', '-') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-steel mt-1 w-4 text-sm"></i>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Email</p>
                            <p class="text-body-sm text-ink-deep break-all">
                                {{ setting('school_email', '-') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
</div>

{{-- ==================== MODAL TANDAI SELESAI (DASHBOARD) ==================== --}}
@if(isset($inProgressMaintenances) && $inProgressMaintenances && $inProgressMaintenances->count() > 0)
<div id="complete-modal-dashboard" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-lg w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
            <i class="fas fa-check-circle text-success text-2xl"></i>
        </div>
        
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Tandai Perbaikan Selesai?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Barang <strong id="dashboard-complete-item-name" class="text-ink-deep"></strong> akan dikembalikan ke kondisi <strong>Baik</strong> dan status <strong>Tersedia</strong>.
        </p>

        <form id="dashboard-complete-form" method="POST" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="dashboard_completion_note" class="form-label">Catatan Hasil Perbaikan</label>
                <textarea id="dashboard_completion_note" 
                          name="completion_note" 
                          rows="3"
                          placeholder="Contoh: Lampu sudah diganti, sudah dites nyala normal..."
                          maxlength="500"
                          class="form-input !h-auto resize-none"></textarea>
                <p class="text-caption text-steel mt-1">Opsional.</p>
            </div>

            <div>
                <label for="dashboard_final_cost" class="form-label">Biaya Final (Rp)</label>
                <input type="number" 
                       id="dashboard_final_cost" 
                       name="final_cost" 
                       min="0"
                       step="1000"
                       class="form-input">
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('complete-modal-dashboard').classList.add('hidden')"
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

@push('scripts')
<script>
    function openCompleteModalFromDashboard(id, itemName, currentCost) {
        document.getElementById('dashboard-complete-item-name').textContent = itemName;
        document.getElementById('dashboard_final_cost').value = currentCost || '';
        document.getElementById('dashboard-complete-form').action = `/maintenances/${id}/complete`;
        document.getElementById('complete-modal-dashboard').classList.remove('hidden');
    }
</script>
@endpush
@endif

@endsection