{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('app_short_name', 'SISARPRAS') }} - @yield('title', 'Dashboard')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-canvas text-ink">

    {{-- Top Navigation --}}
    @include('layouts.partials.topnav')

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main Content --}}
    <main class="lg:ml-64 pt-6 pb-12 px-6 min-h-screen">
        <div class="max-w-7xl mx-auto">

            {{-- Page Header --}}
            @hasSection('page_title')
                <div class="mb-6">
                    <h1 class="text-heading-lg text-ink-deep">@yield('page_title')</h1>
                    @hasSection('page_subtitle')
                        <p class="text-subtitle-md text-steel mt-1">@yield('page_subtitle')</p>
                    @endif
                </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
                    <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-critical/10 border border-critical/20">
                    <i class="fas fa-exclamation-circle text-critical text-lg mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-warning/10 border border-warning/20">
                    <i class="fas fa-exclamation-triangle text-warning text-lg mt-0.5"></i>
                    <p class="text-body-sm text-ink">{{ session('warning') }}</p>
                </div>
            @endif

            {{-- Content --}}
            @yield('content')

        </div>
    </main>

    {{-- Mobile Overlay --}}
    <div id="sidebar-overlay" 
         class="hidden fixed inset-0 bg-ink-deep/50 z-10 lg:hidden"></div>

    {{-- Toggle Script --}}
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        document.getElementById('sidebar-overlay')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>