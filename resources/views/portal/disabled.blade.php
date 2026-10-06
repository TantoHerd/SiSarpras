{{-- resources/views/portal/disabled.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Tidak Aktif</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background: #f1f4f7;">
    <div class="max-w-md w-full text-center">
        <div class="card">
            <div class="w-20 h-20 rounded-full bg-warning/10 flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-lock text-warning text-3xl"></i>
            </div>
            <h1 class="text-heading-md text-ink-deep mb-2">Portal Sedang Tidak Aktif</h1>
            <p class="text-body-md text-steel mb-6">
                Portal peminjaman siswa saat ini sedang dinonaktifkan oleh admin.
                Silakan hubungi petugas sarpras untuk informasi lebih lanjut.
            </p>
        </div>
        <p class="text-caption text-steel mt-6">
            © {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}
        </p>
    </div>
</body>
</html>