{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page_title', 'Manajemen User')
@section('page_subtitle', 'Kelola akun pengguna sistem')

@section('content')

<div class="space-y-6">

    {{-- ==================== STATISTIK CARDS ==================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total --}}
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i class="fas fa-users text-primary"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Total User</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['total']) }}</p>
        </div>

        {{-- Aktif --}}
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                    <i class="fas fa-user-check text-success"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Aktif</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['active']) }}</p>
        </div>

        {{-- Nonaktif --}}
        <div class="card-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-critical/10 flex items-center justify-center">
                    <i class="fas fa-user-slash text-critical"></i>
                </div>
            </div>
            <p class="text-caption text-steel uppercase tracking-wider">Nonaktif</p>
            <p class="text-heading-lg text-ink-deep">{{ number_format($stats['inactive']) }}</p>
        </div>
    </div>

    {{-- ==================== HEADER + ACTION ==================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $users->total() }}</span> user terdaftar
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.create') }}" class="btn-primary">
                <i class="fas fa-user-plus"></i>
                Tambah User
            </a>
        </div>
    </div>

    {{-- ==================== FILTER CARD ==================== --}}
    <div class="card">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel text-sm pointer-events-none"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama, email, atau NIP..."
                       class="form-input pl-11">
            </div>

            {{-- Role --}}
            <select name="role_id" class="form-input">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(($filters['role_id'] ?? '') == $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>

            {{-- Submit --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-ink flex-1">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('users.index') }}" class="btn-ghost" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Pill Tabs Status --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-hairline-soft">
            <span class="text-caption text-steel mr-2">Status:</span>
            <a href="{{ route('users.index', array_merge(request()->query(), ['is_active' => ''])) }}"
               class="pill-tab {{ !isset($filters['is_active']) || $filters['is_active'] === '' ? 'pill-tab-active' : '' }}">
                Semua
            </a>
            <a href="{{ route('users.index', array_merge(request()->query(), ['is_active' => '1'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '1' ? 'pill-tab-active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('users.index', array_merge(request()->query(), ['is_active' => '0'])) }}"
               class="pill-tab {{ ($filters['is_active'] ?? '') === '0' ? 'pill-tab-active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    {{-- ==================== TABEL USER ==================== --}}
    <div class="card !p-0 overflow-hidden">

        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-soft border-b border-hairline-soft">
                        <tr>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">User</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">NIP</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Role</th>
                            <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase tracking-wider hidden lg:table-cell">Login Terakhir</th>
                            <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        @foreach($users as $user)
                            <tr class="hover:bg-surface-soft/50 transition-colors">
                                {{-- User --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                 alt="{{ $user->name }}"
                                                 class="w-11 h-11 rounded-circle object-cover flex-shrink-0">
                                        @else
                                            <div class="w-11 h-11 rounded-circle bg-primary text-white flex items-center justify-center font-bold flex-shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-body-sm-bold text-ink-deep truncate max-w-[220px]">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-caption text-steel truncate max-w-[220px]">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIP --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <p class="text-body-sm text-ink font-mono">
                                        {{ $user->nip ?? '-' }}
                                    </p>
                                </td>

                                {{-- Role --}}
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $roleColor = match($user->role->name) {
                                            'admin' => 'badge-critical',
                                            'petugas_sarpras' => 'badge-info',
                                            'kepala_sekolah' => 'badge-warning',
                                            'guru' => 'badge-success',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="{{ $roleColor }}">
                                        {{ $user->role->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($user->is_active)
                                        <span class="badge-success">Aktif</span>
                                    @else
                                        <span class="badge-critical">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Last Login --}}
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @if($user->last_login_at)
                                        <p class="text-body-sm text-ink">
                                            {{ $user->last_login_at->diffForHumans() }}
                                        </p>
                                        <p class="text-caption text-steel">
                                            {{ $user->last_login_at->format('d M Y, H:i') }}
                                        </p>
                                    @else
                                        <p class="text-caption text-stone">Belum pernah login</p>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('users.show', $user->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-primary/10 hover:!text-primary" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" 
                                           class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" 
                                           title="Edit">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="button"
                                                onclick="confirmResetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="btn-icon !w-9 !h-9 hover:!bg-oculus/10 hover:!text-oculus" 
                                                title="Reset Password">
                                            <i class="fas fa-key text-sm"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button type="button"
                                                    onclick="confirmToggleActive({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->is_active ? 'true' : 'false' }})"
                                                    class="btn-icon !w-9 !h-9 hover:!bg-{{ $user->is_active ? 'critical' : 'success' }}/10 hover:!text-{{ $user->is_active ? 'critical' : 'success' }}" 
                                                    title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'user-slash' : 'user-check' }} text-sm"></i>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    class="btn-icon !w-9 !h-9 hover:!bg-critical/10 hover:!text-critical" 
                                                    title="Hapus">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="px-5 py-4 border-t border-hairline-soft">
                    {{ $users->links() }}
                </div>
            @endif

        @else
            {{-- Empty State --}}
            <div class="py-16 px-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="fas fa-users text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">
                    @if(array_filter($filters))
                        Tidak Ada Hasil
                    @else
                        Belum Ada User
                    @endif
                </h3>
                <p class="text-subtitle-md text-steel mb-6 max-w-md mx-auto">
                    @if(array_filter($filters))
                        Tidak ada user yang cocok dengan filter Anda.
                    @else
                        Mulai tambahkan user pertama ke dalam sistem.
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('users.index') }}" class="btn-ghost">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('users.create') }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Tambah User Pertama
                    </a>
                @endif
            </div>
        @endif

    </div>

</div>

{{-- ==================== MODAL RESET PASSWORD ==================== --}}
<div id="reset-password-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-oculus/10 mx-auto mb-4">
            <i class="fas fa-key text-oculus text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Reset Password User?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            Password baru akan digenerate otomatis dan ditampilkan ke Anda.<br>
            User: <strong id="reset-user-name" class="text-ink-deep"></strong>
        </p>
        <form id="reset-password-form" method="POST" class="flex gap-3">
            @csrf
            <button type="button" onclick="document.getElementById('reset-password-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-primary flex-1">
                <i class="fas fa-key"></i> Reset Password
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL TOGGLE ACTIVE ==================== --}}
<div id="toggle-active-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div id="toggle-icon-bg" class="flex items-center justify-center w-14 h-14 rounded-circle mx-auto mb-4">
            <i id="toggle-icon" class="text-2xl"></i>
        </div>
        <h3 id="toggle-title" class="text-heading-sm text-ink-deep text-center mb-2"></h3>
        <p class="text-body-sm text-steel text-center mb-6">
            User: <strong id="toggle-user-name" class="text-ink-deep"></strong>
        </p>
        <form id="toggle-active-form" method="POST" class="flex gap-3">
            @csrf
            @method('PATCH')
            <button type="button" onclick="document.getElementById('toggle-active-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button id="toggle-submit-btn" type="submit" class="flex-1">
                Konfirmasi
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL DELETE ==================== --}}
<div id="delete-user-modal" class="hidden fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
    <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
        <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-critical text-2xl"></i>
        </div>
        <h3 class="text-heading-sm text-ink-deep text-center mb-2">Hapus User Ini?</h3>
        <p class="text-body-sm text-steel text-center mb-6">
            User <strong id="delete-user-name" class="text-ink-deep"></strong> akan dihapus dari sistem.
            Data dapat dipulihkan oleh Administrator.
        </p>
        <form id="delete-user-form" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="document.getElementById('delete-user-modal').classList.add('hidden')" class="btn-ghost flex-1">
                Batal
            </button>
            <button type="submit" class="btn-danger flex-1">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- ==================== MODAL NEW PASSWORD (setelah create/reset) ==================== --}}
@if(session('new_user_password'))
    @php $newPass = session('new_user_password'); @endphp
    <div id="new-password-modal" class="fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
        <div class="bg-canvas rounded-3xl max-w-md w-full p-6 shadow-sticky">
            <div class="flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4">
                <i class="fas fa-check-circle text-success text-2xl"></i>
            </div>
            <h3 class="text-heading-sm text-ink-deep text-center mb-2">Password Berhasil Dibuat</h3>
            <p class="text-body-sm text-steel text-center mb-6">
                Catat informasi berikut dan sampaikan ke user.<br>
                Password tidak akan ditampilkan lagi.
            </p>

            <div class="space-y-3 mb-6">
                <div class="p-3 rounded-xl bg-surface-soft">
                    <p class="text-caption text-steel mb-1">Nama</p>
                    <p class="text-body-sm-bold text-ink-deep">{{ $newPass['name'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-surface-soft">
                    <p class="text-caption text-steel mb-1">Email</p>
                    <p class="text-body-sm-bold text-ink-deep break-all font-mono">{{ $newPass['email'] }}</p>
                </div>
                <div class="p-4 rounded-xl bg-primary/5 border-2 border-primary/20">
                    <p class="text-caption text-steel mb-2">Password Baru</p>
                    <div class="flex items-center justify-between gap-3">
                        <p id="new-password-text" class="text-heading-sm text-primary font-mono break-all">
                            {{ $newPass['password'] }}
                        </p>
                        <button type="button" 
                                onclick="copyPassword('{{ $newPass['password'] }}')"
                                class="btn-icon !w-10 !h-10 !bg-primary !text-white hover:!bg-primary-deep flex-shrink-0"
                                title="Copy">
                            <i class="fas fa-copy text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" 
                    onclick="document.getElementById('new-password-modal').classList.add('hidden')"
                    class="btn-primary w-full">
                <i class="fas fa-check"></i> Saya Sudah Mencatat
            </button>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    // Reset Password
    function confirmResetPassword(id, name) {
        document.getElementById('reset-user-name').textContent = name;
        document.getElementById('reset-password-form').action = `/users/${id}/reset-password`;
        document.getElementById('reset-password-modal').classList.remove('hidden');
    }

    // Toggle Active
    function confirmToggleActive(id, name, isActive) {
        document.getElementById('toggle-user-name').textContent = name;
        document.getElementById('toggle-active-form').action = `/users/${id}/toggle-active`;
        
        const iconBg = document.getElementById('toggle-icon-bg');
        const icon = document.getElementById('toggle-icon');
        const title = document.getElementById('toggle-title');
        const submitBtn = document.getElementById('toggle-submit-btn');
        
        if (isActive) {
            // Akan menonaktifkan
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-critical/10 mx-auto mb-4';
            icon.className = 'fas fa-user-slash text-critical text-2xl';
            title.textContent = 'Nonaktifkan User?';
            submitBtn.className = 'btn-danger flex-1';
            submitBtn.innerHTML = '<i class="fas fa-user-slash"></i> Nonaktifkan';
        } else {
            // Akan mengaktifkan
            iconBg.className = 'flex items-center justify-center w-14 h-14 rounded-circle bg-success/10 mx-auto mb-4';
            icon.className = 'fas fa-user-check text-success text-2xl';
            title.textContent = 'Aktifkan User?';
            submitBtn.className = 'btn-primary flex-1';
            submitBtn.innerHTML = '<i class="fas fa-user-check"></i> Aktifkan';
        }
        
        document.getElementById('toggle-active-modal').classList.remove('hidden');
    }

    // Delete
    function confirmDelete(id, name) {
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('delete-user-form').action = `/users/${id}`;
        document.getElementById('delete-user-modal').classList.remove('hidden');
    }

    // Copy Password
    function copyPassword(password) {
        navigator.clipboard.writeText(password).then(() => {
            // Tampilkan feedback
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-sm"></i>';
            btn.classList.add('!bg-success');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('!bg-success');
            }, 1500);
        });
    }
</script>
@endpush