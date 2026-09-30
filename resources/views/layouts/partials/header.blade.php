{{-- resources/views/layouts/partials/header.blade.php --}}
<header class="bg-white shadow-sm sticky top-0 z-20">
    <div class="flex items-center justify-between px-6 py-3">
        {{-- Left: Page Title --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                @yield('page_title', 'Dashboard')
            </h2>
            <p class="text-xs text-gray-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

        {{-- Right: User Dropdown --}}
        <div class="flex items-center space-x-4">
            {{-- Notifications --}}
            <button class="relative text-gray-600 hover:text-blue-700 transition-colors">
                <i class="fas fa-bell text-lg"></i>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">0</span>
            </button>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center space-x-2 text-gray-700 hover:text-blue-700 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-blue-900 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:block text-sm font-medium">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>

                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user w-4 mr-2"></i> Profil Saya
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt w-4 mr-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>