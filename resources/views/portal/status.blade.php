{{-- resources/views/portal/status.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status - Portal Peminjaman</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen p-4"
      style="background: radial-gradient(circle at 20% 20%, rgba(0, 100, 224, 0.08) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(10, 19, 23, 0.06) 0%, transparent 50%), #f1f4f7;">

    <div class="max-w-3xl mx-auto py-8">

        {{-- Header --}}
        <div class="text-center mb-8">
            <a href="{{ route('portal.index') }}" class="text-primary hover:underline text-body-sm inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali ke Portal
            </a>
            <h1 class="text-heading-lg text-ink-deep mt-3">Cek Status Permintaan</h1>
            <p class="text-body-md text-steel mt-1">Masukkan NIS untuk melihat status permintaan Anda</p>
        </div>

        {{-- Search Form --}}
        <div class="card mb-6">
            <form method="GET" action="{{ route('portal.status') }}" class="flex gap-2">
                <input type="text" 
                       name="nis" 
                       value="{{ $nis }}"
                       placeholder="Masukkan NIS Anda..."
                       required
                       class="form-input flex-1 font-mono">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i>
                    Cari
                </button>
            </form>
        </div>

        {{-- Results --}}
        @if($nis)
            @if($requests->count() > 0)
                <div class="space-y-3">
                    @foreach($requests as $req)
                        <div class="card">
                            <div class="flex flex-wrap items-start gap-4">
                                {{-- Foto --}}
                                @if($req->item->image)
                                    <img src="{{ asset('storage/' . $req->item->image) }}" 
                                         alt="{{ $req->item->name }}"
                                         class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-surface-soft flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-steel"></i>
                                    </div>
                                @endif

                                {{-- Info --}}
                                <div class="flex-1 min-w-[200px]">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h3 class="text-body-md-bold text-ink-deep">{{ $req->item->name }}</h3>
                                        <span class="{{ $req->status_badge }}">{{ $req->status_label }}</span>
                                    </div>
                                    <p class="text-caption text-steel font-mono">{{ $req->item->code }}</p>
                                    <p class="text-body-sm text-steel mt-1">
                                        Jumlah: <strong>{{ $req->quantity }}</strong> • 
                                        Diajukan: {{ $req->created_at->translatedFormat('d M Y, H:i') }}
                                    </p>
                                    <p class="text-body-sm text-steel mt-1">
                                        Keperluan: {{ Str::limit($req->purpose, 80) }}
                                    </p>

                                    {{-- Alasan Reject --}}
                                    @if($req->isRejected() && $req->rejection_reason)
                                        <div class="mt-2 p-2 rounded-lg bg-critical/10 text-critical text-caption">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Alasan ditolak:</strong> {{ $req->rejection_reason }}
                                        </div>
                                    @endif

                                    {{-- Link ke loan (kalau approved) --}}
                                    @if($req->isApproved() && $req->loan)
                                        <p class="text-caption text-success mt-2">
                                            <i class="fas fa-check-circle"></i>
                                            Disetujui pada {{ $req->processed_at->translatedFormat('d M Y, H:i') }}.
                                            Silakan ambil barang di ruang sarpras.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-surface-soft mb-4">
                        <i class="fas fa-inbox text-steel text-2xl"></i>
                    </div>
                    <h3 class="text-body-md-bold text-ink-deep mb-1">Tidak Ada Permintaan</h3>
                    <p class="text-body-sm text-steel">
                        Belum ada riwayat permintaan untuk NIS <strong>{{ $nis }}</strong>.
                    </p>
                    <a href="{{ route('portal.index') }}" class="btn-primary mt-4">
                        <i class="fas fa-plus"></i> Buat Permintaan
                    </a>
                </div>
            @endif
        @endif

        {{-- Footer --}}
        <p class="text-center text-caption text-steel mt-8">
            © {{ date('Y') }} {{ setting('school_name', 'Sekolah') }}
        </p>

    </div>

</body>
</html>