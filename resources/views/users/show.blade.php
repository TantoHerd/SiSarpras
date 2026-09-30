{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', $user->name)
@section('page_title', 'Detail User')
@section('page_subtitle', 'Informasi lengkap pengguna')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('users.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-users text-xs"></i> Manajemen User
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">{{ $user->name }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Profil --}}
        <div class="card text-center">
            <div class="flex justify-center mb-4">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-circle object-cover border-4 border-surface-soft">
                @else
                    <div class="w-24 h-24 rounded-circle bg-primary text-white flex items-center justify-center font-bold text-heading-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h3 class="text-body-md-bold text-ink-deep mb-1">{{ $user->name }}</h3>
            <p class="text-body-sm text-steel mb-3">{{ $user->email }}</p>

            <span class="{{ 
                match($user->role->name) {
                    'admin' => 'badge-critical',
                    'petugas_sarpras' => 'badge-info',
                    'kepala_sekolah' => 'badge-warning',
                    'guru' => 'badge-success',
                    default => 'badge-neutral',
                }
            }}">
                {{ $user->role->name ?? '-' }}
            </span>

            <div class="mt-4">
                @if($user->is_active)
                    <span class="badge-success">Aktif</span>
                @else
                    <span class="badge-critical">Nonaktif</span>
                @endif
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="card">
            <h3 class="text-body-md-bold text-ink-deep mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('users.edit', $user->id) }}" class="btn-ghost w-full justify-center">
                    <i class="fas fa-pen"></i> Edit User
                </a>
                <button type="button" 
                        onclick="document.getElementById('reset-modal').classList.remove('hidden')"
                        class="btn-ink w-full justify-center">
                    <i class="fas fa-key"></i> Reset Password
                </button>
                @if($user->id !== auth()->id())
                    <button type="button" 
                            onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                            class="btn-danger w-full justify-center">
                        <i class="fas fa-trash"></i> Hapus User
                    </button>
                @endif
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

        {{-- Info User --}}
        <div class="card">
            <h3 class="text-heading-sm text-ink-deep mb-5">Informasi Akun</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Nama</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $user->name }}</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Email</p>
                        <p class="text-body-sm-bold text-ink-deep break-all">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- NIP --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-id-card text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">NIP / NIK</p>
                        <p class="text-body-sm-bold text-ink-deep font-mono">{{ $user->nip ?? '-' }}</p>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Telepon</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Role --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-tag text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Role</p>
                        <p class="text-body-sm-bold text-ink-deep">{{ $user->role->description ?? $user->role->name }}</p>
                    </div>
                </div>

                {{-- Terdaftar --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Terdaftar</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $user->created_at->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- Last Login --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel uppercase tracking-wider">Login Terakhir</p>
                        <p class="text-body-sm-bold text-ink-deep">
                            {{ $user->last_login_at ? $user->last_login_at->translatedFormat('d F Y, H:i') : 'Belum pernah' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Peminjaman (kalau guru) --}}
        @if($user->isGuru())
            <div class="card">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-heading-sm text-ink-deep">Riwayat Peminjaman</h3>
                        <p class="text-body-sm text-steel mt-1">5 peminjaman terbaru</p>
                    </div>
                </div>

                @if($recentLoans->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentLoans as $loan)
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-surface-soft">
                                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-white text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-sm-bold text-ink-deep truncate">{{ $loan->item->name }}</p>
                                    <p class="text-caption text-steel">{{ $loan->loan_date->format('d M Y') }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @php
                                        $statusColor = match($loan->status->value) {
                                            'dipinjam' => 'badge-info',
                                            'terlambat' => 'badge-critical',
                                            'dikembalikan' => 'badge-success',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $statusColor }}">{{ $loan->status->label() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-stone">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-body-sm">Belum ada riwayat peminjaman</p>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

{{-- ==================== MODAL RESET PASSWORD ==================== --}}
<div id="reset-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-oculus/10 mx-auto mb-4">
            <i class="fas fa-key text-oculus text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Reset Password?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Password baru akan di-generate otomatis.
        </p>
        <form method="POST" action="{{ route('users.reset-password', $user->id) }}" class="flex gap-3">
            @csrf
            <button type="button" onclick="document.getElementById('reset-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-key"></i> Reset
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL DELETE ==================== --}}
@if($user->id !== auth()->id())
<div id="delete-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus User Ini?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            User <strong class="text-ink-deep">{{ $user->name }}</strong> akan dihapus.
        </p>
        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="flex gap-3">
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
@endif

@endsection