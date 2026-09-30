{{-- resources/views/layouts/partials/sidebar.blade.php --}}
@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

<aside id="sidebar" 
       class="fixed top-16 left-0 bottom-0 w-64 bg-canvas border-r border-hairline-soft 
              overflow-y-auto z-20 transition-transform duration-300 lg:translate-x-0
              -translate-x-full lg:block">

    <nav class="p-4 space-y-1">

        {{-- ============ DASHBOARD (Semua Role) ============ --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm-bold transition-colors
                  {{ $currentRoute === 'dashboard' 
                     ? 'bg-primary text-white' 
                     : 'text-ink hover:bg-surface-soft' }}">
            <i class="fas fa-th-large w-5 text-center"></i>
            <span>Dashboard</span>
        </a>

        {{-- ============ ADMIN ============ --}}
        @if($user->isAdmin())
            <div class="pt-4">
                <p class="px-4 pb-2 text-caption-bold text-stone uppercase tracking-wider">
                    Master Data
                </p>

                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('users.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-users w-5 text-center {{ request()->routeIs('users.*') ? '' : 'text-steel' }}"></i>
                    <span>Manajemen User</span>
                </a>

                <a href="{{ route('categories.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('categories.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-tags w-5 text-center {{ request()->routeIs('categories.*') ? '' : 'text-steel' }}"></i>
                    <span>Kategori</span>
                </a>

                <a href="{{ route('locations.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('locations.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-map-marker-alt w-5 text-center {{ request()->routeIs('locations.*') ? '' : 'text-steel' }}"></i>
                    <span>Lokasi</span>
                </a>

                <a href="{{ route('suppliers.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('suppliers.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-truck w-5 text-center {{ request()->routeIs('suppliers.*') ? '' : 'text-steel' }}"></i>
                    <span>Supplier</span>
                </a>
            </div>
        @endif

        {{-- ============ INVENTARIS (Admin + Petugas) ============ --}}
        @if($user->isAdmin() || $user->isPetugas())
            <div class="pt-4">
                <p class="px-4 pb-2 text-caption-bold text-stone uppercase tracking-wider">
                    Inventaris
                </p>

                <a href="{{ route('items.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('items.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-boxes w-5 text-center {{ request()->routeIs('items.*') ? '' : 'text-steel' }}"></i>
                    <span>Data Barang</span>
                </a>

                <a href="{{ route('loans.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('loans.index') || request()->routeIs('loans.show') || request()->routeIs('loans.create') || request()->routeIs('loans.return.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-hand-holding w-5 text-center {{ request()->routeIs('loans.index') || request()->routeIs('loans.show') || request()->routeIs('loans.create') || request()->routeIs('loans.return.*') ? '' : 'text-steel' }}"></i>
                    <span>Peminjaman</span>
                </a>

                <a href="{{ route('maintenances.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                        {{ request()->routeIs('maintenances.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-tools w-5 text-center {{ request()->routeIs('maintenances.*') ? '' : 'text-steel' }}"></i>
                    <span>Perawatan</span>
                </a>
            </div>
        @endif

        {{-- ============ PEMINJAMAN SAYA (Guru) ============ --}}
        @if($user->isGuru())
            <div class="pt-4">
                <p class="px-4 pb-2 text-caption-bold text-stone uppercase tracking-wider">
                    Peminjaman Saya
                </p>

                <a href="{{ route('loans.request') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('loans.request') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-plus-circle w-5 text-center {{ request()->routeIs('loans.request') ? '' : 'text-steel' }}"></i>
                    <span>Ajukan Pinjam</span>
                </a>

                <a href="{{ route('loans.my') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('loans.my') || (request()->routeIs('loans.show') && !auth()->user()->isAdmin() && !auth()->user()->isPetugas()) ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-clock w-5 text-center {{ request()->routeIs('loans.my') ? '' : 'text-steel' }}"></i>
                    <span>Riwayat Pinjam</span>
                </a>
            </div>
        @endif

        {{-- ============ ANALISIS (Admin + Petugas + Kepsek) ============ --}}
        @if($user->isAdmin() || $user->isPetugas() || $user->isKepalaSekolah())
            <div class="pt-4">
                <p class="px-4 pb-2 text-caption-bold text-stone uppercase tracking-wider">
                    Analisis
                </p>

                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('reports.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-chart-bar w-5 text-center {{ request()->routeIs('reports.*') ? '' : 'text-steel' }}"></i>
                    <span>Laporan</span>
                </a>
            </div>
        @endif

        {{-- ============ SISTEM (HANYA ADMIN) ============ --}}
        @if($user->isAdmin())
            <div class="pt-4">
                <p class="px-4 pb-2 text-caption-bold text-stone uppercase tracking-wider">
                    Sistem
                </p>

                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-pill text-body-sm transition-colors
                          {{ request()->routeIs('settings.*') ? 'bg-primary text-white' : 'text-ink hover:bg-surface-soft' }}">
                    <i class="fas fa-cog w-5 text-center {{ request()->routeIs('settings.*') ? '' : 'text-steel' }}"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
        @endif
    </nav>

    {{-- Footer Sidebar --}}
    <div class="p-4 mt-4 border-t border-hairline-soft">
        <div class="card-sm">
            <p class="text-caption text-steel">Versi Aplikasi</p>
            <p class="text-body-sm-bold text-ink-deep">
                v{{ setting('app_version', '1.0.0') }}
            </p>
        </div>
    </div>
</aside>