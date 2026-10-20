<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Layanan Sarpras') - {{ setting('school_name', 'Sekolah') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    
    {{-- Alpine.js untuk Autofill --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: radial-gradient(circle at top right, rgba(0, 100, 224, 0.05), transparent 40%), #f1f4f7;
            min-height: 100vh;
        }
    </style>
</head>
<body>

    {{-- Topbar minimalis --}}
    <nav class="bg-white border-b border-hairline-soft px-4 py-3 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr(setting('school_name', 'S'), 0, 1)) }}
                </div>
                <span class="font-bold text-ink-deep hidden sm:block">{{ setting('app_short_name', 'SISARPRAS') }}</span>
            </div>
            <div class="text-caption text-steel">
                <i class="fas fa-clock mr-1"></i> {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="min-h-[calc(100vh-120px)]">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-hairline-soft py-4 text-center mt-auto">
        <p class="text-caption text-steel">
            © {{ date('Y') }} {{ setting('school_name', 'Sekolah') }} — Portal Layanan Sarpras
        </p>
    </footer>

    @stack('scripts')

</body>
</html>