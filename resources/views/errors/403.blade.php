{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex items-center justify-center p-6">

    <div class="max-w-lg w-full text-center">

        {{-- Icon --}}
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-circle bg-critical/10 mb-6">
            <i class="fas fa-shield-alt text-critical text-4xl"></i>
        </div>

        {{-- Badge --}}
        <div class="badge-critical mb-4 inline-flex">
            <i class="fas fa-lock mr-2 text-xs"></i>
            Error 403
        </div>

        {{-- Heading --}}
        <h1 class="text-heading-lg text-ink-deep mb-3">
            Akses Ditolak
        </h1>

        <p class="text-subtitle-md text-steel mb-8">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            Silakan hubungi Administrator jika Anda merasa ini sebuah kesalahan.
        </p>

        {{-- Actions --}}
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Dashboard
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="btn-ghost">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>

        {{-- Info Box --}}
        <div class="mt-10 p-4 rounded-xl bg-surface-soft border border-hairline-soft text-left">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-primary mt-0.5"></i>
                <div>
                    <p class="text-body-sm-bold text-ink-deep">Role Anda saat ini</p>
                    <p class="text-body-sm text-steel mt-1">
                        {{ auth()->user()->role->name ?? 'Guest' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>