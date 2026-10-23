{{-- resources/views/portal/index.blade.php --}}
@extends('portal.layouts.app')

@section('title', 'Portal Layanan Sarpras')

@section('content')

{{-- ==================== POPUP MODAL SUKSES ==================== --}}
@if(session('success_popup'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     {{-- PAKSA POSISI CENTER DENGAN INLINE STYLE --}}
     style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; z-index: 9999 !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 1rem; background: rgba(10, 19, 23, 0.6); backdrop-filter: blur(4px);"
     @click.away="show = false">
    
    {{-- Card Container --}}
    <div style="background: #ffffff; border-radius: 24px; width: 100%; max-width: 400px; padding: 32px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; margin: auto;">
        
        {{-- Ikon Centang --}}
        <div style="width: 80px; height: 80px; margin: 0 auto 20px auto; border-radius: 50%; background: rgba(49, 162, 76, 0.1); display: flex; align-items: center; justify-content: center;">
            <svg width="40" height="40" fill="#31a24c" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <h2 style="font-size: 24px; font-weight: 800; color: #0a1317; margin-bottom: 8px; margin-top: 0;">Berhasil!</h2>
        
        <p style="font-size: 14px; line-height: 1.6; color: #5d6c7b; margin-bottom: 24px; margin-top: 0;">
            {{ session('success_popup') }}
        </p>
        
        {{-- Tombol --}}
        <button type="button" 
                @click="show = false"
                style="background: #0064e0; color: white; border: none; border-radius: 12px; padding: 14px 24px; font-size: 14px; font-weight: 700; cursor: pointer; width: 100%; transition: background 0.2s;"
                onmouseover="this.style.background='#0052b8'"
                onmouseout="this.style.background='#0064e0'">
            Oke, Mengerti
        </button>
    </div>
</div>
@endif

<div class="max-w-4xl mx-auto py-12 px-4">

    {{-- Header --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br from-primary to-primary-deep shadow-xl mb-6">
            <i class="fas fa-school text-white text-4xl"></i>
        </div>
        <h1 class="text-4xl font-bold text-ink-deep mb-4">Portal Layanan Sarpras</h1>
        <p class="text-lg text-steel max-w-xl mx-auto">
            Selamat datang! Silakan pilih layanan yang Anda butuhkan.
        </p>
    </div>

    <br>

    {{-- Flash Error --}}
    @if(session('error'))
        <div class="mb-8 p-4 rounded-xl bg-critical/10 border border-critical/20 text-critical flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <p class="font-bold">{{ session('error') }}</p>
        </div>
    @endif

    {{-- 2 Pilihan Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- CARD 1: Permintaan BHP --}}
        <a href="{{ route('portal.bhp') }}" 
           class="group card p-8 hover:border-warning hover:shadow-xl transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-warning/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-warning/10 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-boxes text-warning text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-ink-deep mb-2">Permintaan BHP</h2>
                <p class="text-steel mb-4">
                    Ambil barang habis pakai (ATK, kertas, dll) yang tersedia di gudang. 
                    Stok akan langsung berkurang.
                </p>
                <div class="flex items-center text-warning font-bold mt-4">
                    <span>Minta Barang Sekarang</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- CARD 2: Peminjaman Barang --}}
        <a href="{{ route('portal.loan') }}" 
           class="group card p-8 hover:border-primary hover:shadow-xl transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-150"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hand-holding text-primary text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-ink-deep mb-2">Peminjaman Barang</h2>
                <p class="text-steel mb-4">
                    Pinjam aset sekolah (laptop, proyektor, alat olahraga). 
                    Bisa meminjam lebih dari 1 jenis barang.
                </p>
                <div class="flex items-center text-primary font-bold mt-4">
                    <span>Ajukan Peminjaman</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

    </div>

    <br>

    {{-- Footer Info --}}
    <div class="mt-12 text-center">
        <p class="text-caption text-steel">
            <i class="fas fa-info-circle"></i>
            Anda hanya dapat mengakses portal ini melalui komputer yang disediakan di ruang Sarana & Prasarana.
        </p>
    </div>

</div>

@endsection