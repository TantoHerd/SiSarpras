{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User')
@section('page_subtitle', 'Perbarui informasi pengguna')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('users.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-users text-xs"></i> Manajemen User
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">{{ $user->name }}</span>
</nav>

<form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Info --}}
            <div class="card text-center sticky top-24">
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

                <h4 class="text-body-md-bold text-ink-deep mb-1">{{ $user->name }}</h4>
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

                <div class="mt-6 pt-6 border-t border-hairline-soft space-y-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Terdaftar</p>
                            <p class="text-body-sm-bold text-ink-deep">
                                {{ $user->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Login Terakhir</p>
                            <p class="text-body-sm-bold text-ink-deep truncate">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Info Reset Password --}}
                <div class="mt-6 pt-6 border-t border-hairline-soft">
                    <div class="p-3 rounded-xl bg-oculus/5 border border-oculus/20 text-left">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-key text-oculus mt-0.5 text-sm"></i>
                            <div>
                                <p class="text-caption-bold text-ink-deep">Lupa Password?</p>
                                <p class="text-caption text-steel mt-1">
                                    Password tidak bisa diubah dari halaman ini.
                                </p>
                                <button type="button"
                                        onclick="document.getElementById('reset-from-edit-modal').classList.remove('hidden')"
                                        class="text-caption-bold text-oculus hover:underline mt-2">
                                    Reset Password →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ FORM ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-user-edit text-primary"></i>
                    Informasi User
                </h3>

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">
                            Nama Lengkap <span class="text-critical">*</span>
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="form-input @error('name') form-input-error @enderror">
                        @error('name')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="form-label">
                            Email <span class="text-critical">*</span>
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="form-input @error('email') form-input-error @enderror">
                        @error('email')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role_id" class="form-label">
                            Role <span class="text-critical">*</span>
                        </label>
                        <select id="role_id" 
                                name="role_id" 
                                required
                                class="form-input @error('role_id') form-input-error @enderror">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ $role->description ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($user->id === auth()->id())
                            <p class="text-caption text-warning mt-1 flex items-center gap-1.5">
                                <i class="fas fa-exclamation-triangle text-xs"></i>
                                Anda sedang mengedit akun sendiri. Berhati-hati saat mengubah role.
                            </p>
                        @endif
                        @error('role_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- NIP + Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="nip" class="form-label">NIP / NIK</label>
                            <input id="nip" 
                                   type="text" 
                                   name="nip" 
                                   value="{{ old('nip', $user->nip) }}"
                                   class="form-input @error('nip') form-input-error @enderror">
                            @error('nip')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="form-label">Telepon</label>
                            <input id="phone" 
                                   type="text" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-input @error('phone') form-input-error @enderror">
                            @error('phone')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="form-label">Status Akun</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $isActiveChecked = old('is_active', $user->is_active ? '1' : '0') == '1';
                            @endphp

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="1"
                                       @checked($isActiveChecked)
                                       class="sr-only status-radio">
                                <div class="status-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ $isActiveChecked ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-check-circle text-success text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Aktif</p>
                                    <p class="text-caption text-steel mt-1">User bisa login</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="0"
                                       @checked(!$isActiveChecked)
                                       class="sr-only status-radio">
                                <div class="status-card p-4 rounded-xl border-2 transition-all text-center
                                            {{ !$isActiveChecked ? 'border-primary bg-primary/5' : 'border-hairline-soft' }}">
                                    <i class="fas fa-user-slash text-critical text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Nonaktif</p>
                                    <p class="text-caption text-steel mt-1">User tidak bisa login</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ ACTION ============ --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{-- Tombol Hapus (kiri) --}}
                @if($user->id !== auth()->id())
                    <button type="button" 
                            onclick="document.getElementById('delete-from-edit-modal').classList.remove('hidden')"
                            class="btn-danger">
                        <i class="fas fa-trash"></i>
                        Hapus User
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('users.index') }}" class="btn-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

{{-- ==================== MODAL RESET PASSWORD ==================== --}}
<div id="reset-from-edit-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-oculus/10 mx-auto mb-4">
            <i class="fas fa-key text-oculus text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Reset Password?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Password baru untuk <strong class="text-ink-deep">{{ $user->name }}</strong> akan di-generate otomatis.
        </p>
        <form method="POST" action="{{ route('users.reset-password', $user->id) }}" class="flex gap-3">
            @csrf
            <button type="button" onclick="document.getElementById('reset-from-edit-modal').classList.add('hidden')" class="btn-ghost flex-1">
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
<div id="delete-from-edit-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus User Ini?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            User <strong class="text-ink-deep">{{ $user->name }}</strong> akan dihapus. Data dapat dipulihkan oleh Administrator.
        </p>
        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-from-edit-modal').classList.add('hidden')" class="btn-ghost flex-1">
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

@push('scripts')
<script>
    // Highlight status radio
    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.status-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary/5');
                card.classList.add('border-hairline-soft');
            });
            
            const card = this.closest('label').querySelector('.status-card');
            card.classList.remove('border-hairline-soft');
            card.classList.add('border-primary', 'bg-primary/5');
        });
    });
</script>
@endpush