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

            {{-- Notification Button --}}
            <button class="relative btn-icon" aria-label="Notifikasi">
                <i class="far fa-bell text-lg"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-critical rounded-circle"></span>
            </button>

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