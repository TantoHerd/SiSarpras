{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_subtitle', 'Cetak dan ekspor laporan inventaris sekolah')

@section('content')

<div class="space-y-6">

    {{-- ==================== WELCOME CARD ==================== --}}
    <div class="card-promo relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-circle bg-primary/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <p class="text-body-sm-bold text-primary-soft uppercase tracking-wider mb-3">
                Modul Laporan
            </p>
            <h2 class="text-heading-lg text-white leading-tight mb-3">
                Cetak & Ekspor Laporan<br>Inventaris Sekolah
            </h2>
            <p class="text-subtitle-md text-white/70">
                Pilih jenis laporan di bawah untuk melihat preview, mencetak PDF, atau mengekspor ke Excel.
            </p>
        </div>
    </div>

    {{-- ==================== STATISTIK RINGKAS ==================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-boxes text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Barang</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($inventory_total) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center">
                    <i class="fas fa-hand-holding text-warning"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Peminjaman Aktif</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($loan_active) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-oculus/10 flex items-center justify-center">
                    <i class="fas fa-tools text-oculus"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Sedang Diperbaiki</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($maintenance_in_progress) }}</p>
        </div>

        <div class="card-sm">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-history text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider mb-1">Total Peminjaman</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($loan_total) }}</p>
        </div>
    </div>

    {{-- ==================== JENIS LAPORAN ==================== --}}
    <section>
        <div class="mb-4">
            <h3 class="text-heading-sm text-ink-deep">Jenis Laporan</h3>
            <p class="text-body-sm text-steel mt-1">Pilih laporan yang ingin Anda cetak atau ekspor</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Laporan Inventaris --}}
            <a href="{{ route('reports.inventory') }}" 
               class="card hover:border-primary/30 hover:shadow-md transition-all group">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
                        <i class="fas fa-boxes text-primary text-xl group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body-md-bold text-ink-deep mb-1">Inventaris Barang</h4>
                        <p class="text-body-sm text-steel">
                            Laporan lengkap semua barang dengan filter kategori, lokasi, kondisi, dan status.
                        </p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-hairline-soft flex items-center justify-between">
                    <div class="flex items-center gap-3 text-caption text-steel">
                        <span><i class="fas fa-file-pdf text-critical mr-1"></i> PDF</span>
                        <span><i class="fas fa-file-excel text-success mr-1"></i> Excel</span>
                    </div>
                    <i class="fas fa-arrow-right text-primary text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Laporan Peminjaman --}}
            <a href="{{ route('reports.loans') }}" 
               class="card hover:border-warning/30 hover:shadow-md transition-all group">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-warning/10 flex items-center justify-center flex-shrink-0 group-hover:bg-warning group-hover:text-white transition-all">
                        <i class="fas fa-hand-holding text-warning text-xl group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body-md-bold text-ink-deep mb-1">Peminjaman</h4>
                        <p class="text-body-sm text-steel">
                            Riwayat peminjaman barang per periode dengan filter status dan peminjam.
                        </p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-hairline-soft flex items-center justify-between">
                    <div class="flex items-center gap-3 text-caption text-steel">
                        <span><i class="fas fa-file-pdf text-critical mr-1"></i> PDF</span>
                        <span><i class="fas fa-file-excel text-success mr-1"></i> Excel</span>
                    </div>
                    <i class="fas fa-arrow-right text-warning text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            {{-- Laporan Perawatan --}}
            <a href="{{ route('reports.maintenances') }}" 
               class="card hover:border-oculus/30 hover:shadow-md transition-all group">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-oculus/10 flex items-center justify-center flex-shrink-0 group-hover:bg-oculus group-hover:text-white transition-all">
                        <i class="fas fa-tools text-oculus text-xl group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body-md-bold text-ink-deep mb-1">Perawatan</h4>
                        <p class="text-body-sm text-steel">
                            Laporan perawatan & perbaikan barang dengan rincian biaya per periode.
                        </p>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-hairline-soft flex items-center justify-between">
                    <div class="flex items-center gap-3 text-caption text-steel">
                        <span><i class="fas fa-file-pdf text-critical mr-1"></i> PDF</span>
                        <span><i class="fas fa-file-excel text-success mr-1"></i> Excel</span>
                    </div>
                    <i class="fas fa-arrow-right text-oculus text-sm group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>
    </section>

    {{-- ==================== INFO BOX ==================== --}}
    <div class="card bg-primary/5 border-primary/20">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-primary"></i>
            </div>
            <div>
                <p class="text-body-sm-bold text-ink-deep">Tips Penggunaan Laporan</p>
                <ul class="text-body-sm text-steel mt-1 space-y-1 list-disc list-inside">
                    <li>Gunakan <strong>filter periode</strong> untuk membatasi data laporan</li>
                    <li><strong>Preview di browser</strong> sebelum mencetak untuk memastikan data benar</li>
                    <li><strong>Cetak PDF</strong> untuk arsip fisik atau <strong>Excel</strong> untuk analisis lebih lanjut</li>
                    <li>Semua laporan otomatis menggunakan <strong>kop surat sekolah</strong> dan tanggal cetak</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@endsection