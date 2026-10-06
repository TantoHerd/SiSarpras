{{-- resources/views/portal/success.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Terkirim - Portal Peminjaman</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: radial-gradient(circle at 20% 20%, rgba(0, 100, 224, 0.08) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(10, 19, 23, 0.06) 0%, transparent 50%), #f1f4f7;">

    <div class="max-w-md w-full text-center">

        <div class="card">
            <div class="w-20 h-20 rounded-full bg-success/10 flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-check-circle text-success text-4xl"></i>
            </div>

            <h1 class="text-heading-md text-ink-deep mb-2">Permintaan Terkirim!</h1>
            <p class="text-body-md text-steel mb-6">
                Permintaan peminjaman Anda sudah kami terima. Petugas sarpras akan segera mereview.
            </p>

            @if($nis)
                <div class="p-3 rounded-xl bg-surface-soft mb-6">
                    <p class="text-caption text-steel mb-1">NIS Anda</p>
                    <p class="text-body-md-bold text-ink-deep font-mono">{{ $nis }}</p>
                </div>
            @endif

            <div class="space-y-2">
                <a href="{{ route('portal.status', ['nis' => $nis]) }}" class="btn-primary w-full justify-center">
                    <i class="fas fa-search"></i>
                    Cek Status Permintaan
                </a>
                <a href="{{ route('portal.index') }}" class="btn-ghost w-full justify-center">
                    <i class="fas fa-plus"></i>
                    Buat Permintaan Baru
                </a>
            </div>
        </div>

        <p class="text-caption text-steel mt-6">
            © {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}
        </p>

    </div>

</body>
</html>