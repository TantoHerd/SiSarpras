{{-- resources/views/layouts/partials/topnav.blade.php --}}
@php
    $user = auth()->user();

    // Logika Logo
    $logoFile = setting('school_logo', 'logo-default.png');
    $logoExists = $logoFile 
        && $logoFile !== 'logo-default.png' 
        && file_exists(storage_path('app/public/' . $logoFile));

    // Inisial sekolah untuk fallback
    $schoolInitial = strtoupper(substr(setting('school_name', 'S'), 0, 1));
@endphp

<nav class="sticky top-0 z-30 bg-canvas border-b border-hairline-soft">
    <div class="flex items-center justify-between h-16 px-4 md:px-6">

        {{-- ==================== LEFT: Logo + Brand ==================== --}}
        <div class="flex items-center gap-3 md:gap-4">

            {{-- Mobile Menu Toggle --}}
            <button id="sidebar-toggle" 
                    class="lg:hidden btn-icon" 
                    aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            {{-- Logo + Brand --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                @if($logoExists)
                    <img src="{{ asset('storage/' . $logoFile) }}" 
                         alt="Logo" 
                         class="w-9 h-9 rounded-lg object-contain bg-surface-soft p-1 flex-shrink-0">
                @else
                    <div class="w-9 h-9 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-body-sm flex-shrink-0">
                        {{ $schoolInitial }}
                    </div>
                @endif

                <div class="hidden sm:block min-w-0">
                    <h1 class="text-body-md-bold text-ink-deep leading-tight">
                        {{ setting('app_short_name', 'SISARPRAS') }}
                    </h1>
                    <p class="text-caption text-steel leading-tight truncate max-w-[200px]">
                        {{ setting('school_name', 'Sekolah') }}
                    </p>
                </div>
            </a>
        </div>

        {{-- ==================== CENTER: Quick Nav Pill Tabs ==================== --}}
        <div class="hidden lg:flex items-center gap-2">
            <a href="{{ route('dashboard') }}" 
               class="pill-tab {{ request()->routeIs('dashboard') ? 'pill-tab-active' : '' }}">
                <i class="fas fa-home mr-2 text-xs"></i> Beranda
            </a>

            @if($user->isAdmin() || $user->isPetugas())
                <a href="{{ route('items.index') }}" 
                   class="pill-tab {{ request()->routeIs('items.*') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-boxes mr-2 text-xs"></i> Inventaris
                </a>
                <a href="{{ route('loans.index') }}" 
                   class="pill-tab {{ request()->routeIs('loans.index') || request()->routeIs('loans.show') || request()->routeIs('loans.create') || request()->routeIs('loans.return.*') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-hand-holding mr-2 text-xs"></i> Peminjaman
                </a>
                <a href="{{ route('maintenances.index') }}" 
                    class="pill-tab {{ request()->routeIs('maintenances.*') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-tools mr-2 text-xs"></i> Perawatan
                </a>
            @endif

            @if($user->isGuru())
                <a href="{{ route('loans.request') }}" 
                   class="pill-tab {{ request()->routeIs('loans.request') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-plus mr-2 text-xs"></i> Ajukan Pinjam
                </a>
                <a href="{{ route('loans.my') }}" 
                   class="pill-tab {{ request()->routeIs('loans.my') || request()->routeIs('loans.show') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-clock mr-2 text-xs"></i> Riwayat
                </a>
            @endif

            @if($user->isKepalaSekolah())
                <a href="{{ route('reports.index') }}" 
                   class="pill-tab {{ request()->routeIs('reports.*') ? 'pill-tab-active' : '' }}">
                    <i class="fas fa-chart-bar mr-2 text-xs"></i> Laporan
                </a>
            @endif
        </div>

        {{-- ==================== RIGHT: Search + User ==================== --}}
        <div class="flex items-center gap-2 md:gap-3">

            {{-- Search Pill (Desktop) --}}
            <div class="hidden md:flex search-pill w-56 lg:w-64">
                <i class="fas fa-search text-stone mr-2 text-sm"></i>
                <input type="text" placeholder="Cari barang, user...">
            </div>

            {{-- ← UBAH: Notification Button dengan dropdown --}}
            <div class="relative" x-data="notificationBell()" x-init="init()">
                {{-- Bell Button --}}
                <button @click="toggle()" 
                        class="relative btn-icon" 
                        aria-label="Notifikasi">
                    <i class="far fa-bell text-lg"></i>
                    <span x-show="unreadCount > 0" 
                        x-text="unreadCount > 9 ? '9+' : unreadCount"
                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-critical text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" 
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-3 w-[380px] max-w-[calc(100vw-2rem)] bg-canvas rounded-2xl shadow-sticky border border-hairline-soft overflow-hidden z-50"
                    style="display: none;">

                    {{-- ============ HEADER ============ --}}
                    <div class="px-5 py-4 border-b border-hairline-soft flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-body-md-bold text-ink-deep">Notifikasi</p>
                            <p class="text-caption text-steel mt-0.5" x-show="unreadCount > 0">
                                <span x-text="unreadCount" class="text-primary font-bold"></span> belum dibaca
                            </p>
                            <p class="text-caption text-steel mt-0.5" x-show="unreadCount === 0">
                                Tidak ada notifikasi baru
                            </p>
                        </div>
                        <a href="{{ route('notifications.index') }}" 
                        class="text-caption-bold text-primary hover:text-primary-deep whitespace-nowrap">
                            Lihat Semua →
                        </a>
                    </div>

                    {{-- ============ BODY ============ --}}
                    <div class="max-h-[380px] overflow-y-auto">

                        {{-- Loading --}}
                        <div x-show="loading" class="px-5 py-10 text-center">
                            <i class="fas fa-spinner fa-spin text-primary text-xl"></i>
                            <p class="text-caption text-steel mt-2">Memuat...</p>
                        </div>

                        {{-- Empty --}}
                        <div x-show="!loading && notifications.length === 0" class="px-5 py-10 text-center">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-surface-soft mb-3">
                                <i class="far fa-bell text-steel text-xl"></i>
                            </div>
                            <p class="text-body-sm text-steel">Tidak ada notifikasi</p>
                        </div>

                        {{-- List --}}
                        <div x-show="!loading && notifications.length > 0">
                            <template x-for="notif in notifications" :key="notif.id">
                                <a :href="'/notifications/' + notif.id + '/read'"
                                class="flex items-start gap-3 px-5 py-4 hover:bg-surface-soft transition-colors border-b border-hairline-soft last:border-0"
                                :class="!notif.read_at ? 'bg-primary/5' : ''">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-warning text-sm"></i>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-body-sm text-ink-deep font-semibold mb-0.5">
                                            Stok Menipis
                                        </p>
                                        <p class="text-caption text-steel leading-snug line-clamp-2" x-text="notif.message"></p>
                                        <p class="text-[11px] text-stone mt-1" x-text="notif.created_at"></p>
                                    </div>

                                    {{-- Unread dot --}}
                                    <div x-show="!notif.read_at" 
                                        class="w-2 h-2 rounded-full bg-primary mt-2 flex-shrink-0"></div>
                                </a>
                            </template>
                        </div>

                    </div>

                    {{-- ============ FOOTER ============ --}}
                    <div x-show="!loading && notifications.length > 0" 
                        class="px-5 py-3 border-t border-hairline-soft bg-surface-soft/50">
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-caption-bold text-primary hover:text-primary-deep py-1">
                                <i class="fas fa-check-double text-xs mr-1"></i>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2 p-1 pr-2 md:pr-3 rounded-pill hover:bg-surface-soft transition-colors">
                    <div class="w-8 h-8 rounded-circle bg-primary text-white flex items-center justify-center text-body-sm-bold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:block text-body-sm-bold text-ink max-w-[100px] truncate">
                        {{ Str::limit($user->name, 15) }}
                    </span>
                    <i class="fas fa-chevron-down text-xs text-steel hidden md:block"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-canvas rounded-xl shadow-sticky border border-hairline-soft py-2 z-50"
                     style="display: none;">

                    {{-- User Info --}}
                    <div class="px-4 py-3 border-b border-hairline-soft">
                        <p class="text-body-sm-bold text-ink-deep truncate">{{ $user->name }}</p>
                        <p class="text-caption text-steel truncate">{{ $user->email }}</p>
                        <span class="badge-info mt-2 inline-flex">
                            {{ $user->role->name ?? '-' }}
                        </span>
                    </div>

                    {{-- Menu Items --}}
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center px-4 py-2.5 text-body-sm text-ink hover:bg-surface-soft transition-colors">
                        <i class="fas fa-user w-4 mr-3 text-steel"></i> 
                        Profil Saya
                    </a>

                    @if($user->isAdmin())
                        <a href="{{ route('settings.index') }}" 
                           class="flex items-center px-4 py-2.5 text-body-sm text-ink hover:bg-surface-soft transition-colors">
                            <i class="fas fa-cog w-4 mr-3 text-steel"></i> 
                            Pengaturan
                        </a>
                    @endif

                    <a href="#" 
                       class="flex items-center px-4 py-2.5 text-body-sm text-ink hover:bg-surface-soft transition-colors">
                        <i class="fas fa-question-circle w-4 mr-3 text-steel"></i> 
                        Bantuan
                    </a>

                    {{-- Logout --}}
                    <div class="border-t border-hairline-soft mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center px-4 py-2.5 text-body-sm text-critical hover:bg-critical/5 transition-colors">
                                <i class="fas fa-sign-out-alt w-4 mr-3"></i> 
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</nav>