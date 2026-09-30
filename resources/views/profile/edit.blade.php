{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Kelola informasi akun dan keamanan Anda')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ==================== KOLOM KIRI: Kartu Profil ==================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Kartu Avatar & Info --}}
        <div class="card text-center">
            {{-- Avatar --}}
            <div class="flex justify-center mb-4">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-circle object-cover border-4 border-surface-soft">
                @else
                    <div class="w-24 h-24 rounded-circle bg-primary text-white flex items-center justify-center text-heading-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Nama & Email --}}
            <h3 class="text-body-md-bold text-ink-deep mb-1">{{ $user->name }}</h3>
            <p class="text-body-sm text-steel mb-3">{{ $user->email }}</p>

            {{-- Badge Role --}}
            <span class="badge-info">{{ $user->role->name ?? '-' }}</span>

            {{-- Info Detail --}}
            <div class="mt-6 pt-6 border-t border-hairline-soft space-y-3 text-left">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-id-card text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel">NIP/NIK</p>
                        <p class="text-body-sm-bold text-ink-deep truncate">
                            {{ $user->nip ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone text-steel text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-caption text-steel">Telepon</p>
                        <p class="text-body-sm-bold text-ink-deep truncate">
                            {{ $user->phone ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
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
        </div>

        {{-- Kartu Bahaya (Hapus Akun) --}}
        <div class="card border-critical/20">
            <h3 class="text-body-md-bold text-critical mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i> Zona Bahaya
            </h3>
            <p class="text-body-sm text-steel mb-4">
                Setelah akun dihapus, semua data akan hilang permanen. Tindakan ini tidak dapat dibatalkan.
            </p>
            <button type="button" 
                    onclick="document.getElementById('delete-account-modal').classList.remove('hidden')"
                    class="btn-danger w-full">
                <i class="fas fa-trash"></i>
                Hapus Akun Saya
            </button>
        </div>
    </div>

    {{-- ==================== KOLOM KANAN: Form ==================== --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Flash Messages (khusus profil) --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                <p class="text-body-sm text-ink">{{ session('success') }}</p>
            </div>
        @endif

        {{-- ============ Form Update Profil ============ --}}
        <div class="card">
            <div class="mb-6">
                <h3 class="text-heading-sm text-ink-deep">Informasi Profil</h3>
                <p class="text-body-sm text-steel mt-1">
                    Perbarui nama, email, dan informasi kontak Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" 
                  enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('patch')

                {{-- Avatar Upload --}}
                <div>
                    <label class="form-label">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        @if($user->avatar)
                            <img id="avatar-preview" 
                                 src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="Preview"
                                 class="w-16 h-16 rounded-circle object-cover border-2 border-hairline-soft">
                        @else
                            <div id="avatar-preview-placeholder"
                                 class="w-16 h-16 rounded-circle bg-primary text-white flex items-center justify-center text-subtitle-lg flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img id="avatar-preview" 
                                 src="" 
                                 alt="Preview"
                                 class="w-16 h-16 rounded-circle object-cover border-2 border-hairline-soft hidden">
                        @endif

                        <div class="flex-1">
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar"
                                   accept="image/*"
                                   onchange="previewAvatar(event)"
                                   class="block w-full text-body-sm text-steel
                                          file:mr-4 file:py-2 file:px-4 file:rounded-pill
                                          file:border-0 file:text-caption-bold
                                          file:bg-surface-soft file:text-ink-deep
                                          hover:file:bg-hairline-soft file:cursor-pointer
                                          file:transition-colors">
                            <p class="text-caption text-steel mt-1">JPG, PNG, atau WEBP. Maks 2MB.</p>
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nama --}}
                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
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
                    <label for="email" class="form-label">Email</label>
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

                {{-- NIP & Phone --}}
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

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>

                    @if(session('success'))
                        <span class="text-body-sm text-success flex items-center gap-1.5">
                            <i class="fas fa-check-circle"></i> Tersimpan
                        </span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ============ Form Update Password ============ --}}
        <div class="card">
            <div class="mb-6">
                <h3 class="text-heading-sm text-ink-deep">Ubah Password</h3>
                <p class="text-body-sm text-steel mt-1">
                    Gunakan password yang kuat dan unik untuk menjaga keamanan akun.
                </p>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                @method('put')

                {{-- Password Saat Ini --}}
                <div>
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input id="current_password" 
                           type="password" 
                           name="current_password" 
                           autocomplete="current-password"
                           class="form-input @error('current_password', 'updatePassword') form-input-error @enderror">
                    @error('current_password', 'updatePassword')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password Baru & Konfirmasi --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="form-label">Password Baru</label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               autocomplete="new-password"
                               class="form-input @error('password', 'updatePassword') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">Minimal 8 karakter.</p>
                        @error('password', 'updatePassword')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation" 
                               autocomplete="new-password"
                               class="form-input">
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-ink">
                        <i class="fas fa-lock"></i>
                        Ubah Password
                    </button>

                    @if(session('status') === 'password-updated')
                        <span class="text-body-sm text-success flex items-center gap-1.5">
                            <i class="fas fa-check-circle"></i> Password diubah
                        </span>
                    @endif
                </div>
            </form>
        </div>

    </div>
</div>

{{-- ==================== MODAL HAPUS AKUN ==================== --}}
<div id="delete-account-modal" 
     class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>

        {{-- Title --}}
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">
            Hapus Akun Permanen?
        </h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Tindakan ini tidak dapat dibatalkan. Masukkan password untuk konfirmasi.
        </p>

        {{-- Form --}}
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="delete_password" class="form-label">Password</label>
                <input id="delete_password" 
                       type="password" 
                       name="password" 
                       placeholder="Masukkan password Anda"
                       class="form-input @error('password', 'userDeletion') form-input-error @enderror">
                @error('password', 'userDeletion')
                    <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('delete-account-modal').classList.add('hidden')"
                        class="btn-ghost flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-danger flex-1">
                    <i class="fas fa-trash"></i>
                    Hapus Akun
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-preview-placeholder');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush