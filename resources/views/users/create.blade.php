{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah User')
@section('page_title', 'Tambah User')
@section('page_subtitle', 'Buat akun pengguna baru untuk sistem')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('users.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-users text-xs"></i> Manajemen User
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Tambah User</span>
</nav>

<form method="POST" action="{{ route('users.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== KOLOM KIRI: Preview ==================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Preview User --}}
            <div class="card text-center sticky top-24">
                <h3 class="text-body-md-bold text-ink-deep mb-4 text-left">Preview</h3>

                <div class="flex justify-center mb-4">
                    <div id="avatar-preview" 
                         class="w-24 h-24 rounded-circle bg-primary text-white flex items-center justify-center font-bold text-heading-lg">
                        ?
                    </div>
                </div>

                <h4 id="preview-name" class="text-body-md-bold text-ink-deep mb-1">
                    Nama Belum Diisi
                </h4>
                <p id="preview-email" class="text-body-sm text-steel mb-3">
                    email@sekolah.sch.id
                </p>

                <span id="preview-role" class="badge-neutral">Role</span>

                <div class="mt-6 pt-6 border-t border-hairline-soft space-y-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-id-card text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">NIP</p>
                            <p id="preview-nip" class="text-body-sm-bold text-ink-deep truncate">-</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface-soft flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-steel text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-caption text-steel">Telepon</p>
                            <p id="preview-phone" class="text-body-sm-bold text-ink-deep truncate">-</p>
                        </div>
                    </div>
                </div>

                {{-- Info Password --}}
                <div class="mt-6 pt-6 border-t border-hairline-soft p-3 rounded-xl bg-primary/5 border border-primary/20 text-left">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-key text-primary mt-0.5 text-sm"></i>
                        <div>
                            <p class="text-caption-bold text-ink-deep">Password Otomatis</p>
                            <p class="text-caption text-steel mt-1">
                                Password akan di-generate otomatis dengan format <strong>Nama@Tahun</strong> (contoh: <code>Budi@2026</code>).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN: Form ==================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ============ INFORMASI UTAMA ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-user text-primary"></i>
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
                               value="{{ old('name') }}"
                               placeholder="Contoh: Budi Santoso, S.Pd"
                               required
                               oninput="updatePreview()"
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
                               value="{{ old('email') }}"
                               placeholder="budi@sekolah.sch.id"
                               required
                               oninput="updatePreview()"
                               class="form-input @error('email') form-input-error @enderror">
                        <p class="text-caption text-steel mt-1">
                            Email akan digunakan untuk login.
                        </p>
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
                                onchange="updatePreview()"
                                class="form-input @error('role_id') form-input-error @enderror">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        data-role-name="{{ $role->name }}"
                                        @selected(old('role_id') == $role->id)>
                                    {{ $role->description ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-caption text-steel mt-1">
                            Role menentukan hak akses user di sistem.
                        </p>
                        @error('role_id')
                            <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============ INFORMASI TAMBAHAN ============ --}}
            <div class="card">
                <h3 class="text-body-md-bold text-ink-deep mb-5 flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    Informasi Tambahan
                </h3>

                <div class="space-y-5">
                    {{-- NIP + Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="nip" class="form-label">NIP / NIK</label>
                            <input id="nip" 
                                   type="text" 
                                   name="nip" 
                                   value="{{ old('nip') }}"
                                   placeholder="Contoh: 1987654321"
                                   oninput="updatePreview()"
                                   class="form-input @error('nip') form-input-error @enderror">
                            <p class="text-caption text-steel mt-1">
                                Opsional, tapi disarankan untuk identifikasi.
                            </p>
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
                                   value="{{ old('phone') }}"
                                   placeholder="081234567890"
                                   oninput="updatePreview()"
                                   class="form-input @error('phone') form-input-error @enderror">
                            @error('phone')
                                <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status Aktif --}}
                    <div>
                        <label class="form-label">Status Akun</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="1"
                                       @checked(old('is_active', '1') === '1' || old('is_active') === 1)
                                       onchange="updatePreview()"
                                       class="sr-only status-radio">
                                <div class="status-card p-4 rounded-xl border-2 border-primary bg-primary/5
                                            hover:border-primary transition-all text-center">
                                    <i class="fas fa-check-circle text-success text-2xl mb-2"></i>
                                    <p class="text-body-sm-bold text-ink-deep">Aktif</p>
                                    <p class="text-caption text-steel mt-1">User bisa login</p>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="0"
                                       @checked(old('is_active') === '0' || old('is_active') === 0)
                                       onchange="updatePreview()"
                                       class="sr-only status-radio">
                                <div class="status-card p-4 rounded-xl border-2 border-hairline-soft
                                            hover:border-hairline transition-all text-center">
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
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan User
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    const roleColors = {
        'admin': 'badge-critical',
        'petugas_sarpras': 'badge-info',
        'kepala_sekolah': 'badge-warning',
        'guru': 'badge-success',
    };

    const roleLabels = {
        'admin': 'Administrator',
        'petugas_sarpras': 'Petugas Sarpras',
        'kepala_sekolah': 'Kepala Sekolah',
        'guru': 'Guru / Staf',
    };

    function updatePreview() {
        const name = document.getElementById('name').value || 'Nama Belum Diisi';
        const email = document.getElementById('email').value || 'email@sekolah.sch.id';
        const nip = document.getElementById('nip').value || '-';
        const phone = document.getElementById('phone').value || '-';
        const roleSelect = document.getElementById('role_id');
        const roleOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = roleOption?.dataset?.roleName || '';
        const initial = name.trim().charAt(0).toUpperCase() || '?';

        // Update preview
        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-email').textContent = email;
        document.getElementById('preview-nip').textContent = nip;
        document.getElementById('preview-phone').textContent = phone;
        document.getElementById('avatar-preview').textContent = initial;

        // Update role badge
        const roleBadge = document.getElementById('preview-role');
        if (roleName && roleLabels[roleName]) {
            roleBadge.textContent = roleLabels[roleName];
            roleBadge.className = roleColors[roleName] || 'badge-neutral';
        } else {
            roleBadge.textContent = 'Role';
            roleBadge.className = 'badge-neutral';
        }
    }

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

    // Init on load
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush