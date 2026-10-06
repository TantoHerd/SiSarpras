{{-- resources/views/items/barcode-batch.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label Massal — {{ count($items) }} Barang</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])

    @php
        // ← BARU: Load data sekolah untuk logo (dipakai semua label)
        $schoolName     = setting('school_name', 'Sekolah');
        $schoolInitial  = strtoupper(substr($schoolName, 0, 1));
        $logoFile       = setting('school_logo', 'logo-default.png');
        $logoExists     = $logoFile
            && $logoFile !== 'logo-default.png'
            && file_exists(storage_path('app/public/' . $logoFile));
    @endphp

    <style>
        /* ============================================================
        CSS VARIABLES
        ============================================================ */
        :root {
            --label-width: 6.5cm;
            --label-height: 4.3cm;
            
            --accent: #0a1317;
            --cobalt: #0064e0;
            --cobalt-deep: #0457cb;
            --cobalt-soft: #e6f0ff;
            --ink-deep: #0a1317;
            --ink: #1c1e21;
            --steel: #5d6c7b;
            --stone: #8595a4;
            --hairline: #e8ecf1;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: #f1f4f7;
            padding: 20px;
        }

        /* ============================================================
        TOOLBAR (screen only)
        ============================================================ */
        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 100px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
            margin: 0 auto 20px;
            max-width: 21cm;
        }

        .toolbar-info h1 {
            font-size: 15px;
            font-weight: 800;
            color: var(--ink-deep);
            margin: 0 0 2px 0;
        }

        .toolbar-info p {
            font-size: 11px;
            color: var(--steel);
            margin: 0;
            font-weight: 600;
        }

        .toolbar-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink-deep);
        }
        .btn-ghost:hover {
            background: #f1f4f7;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--cobalt) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 100, 224, 0.35);
        }

        /* ============================================================
        INFO BOX
        ============================================================ */
        .info-box {
            background: var(--cobalt-soft);
            border: 1px solid rgba(0, 100, 224, 0.2);
            color: var(--cobalt-deep);
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin: 0 auto 20px;
            max-width: 21cm;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ============================================================
        WRAPPER — pembungkus utama grid
        ============================================================ */
        .print-wrapper {
            width: 21cm;           /* ← EXACT A4 WIDTH */
            margin: 0 auto;
            background: #fff;
            padding: 1cm;          /* ← padding merata 1cm */
        }

        /* ============================================================
        LABEL GRID
        ============================================================ */
        .label-container {
            display: grid;
            grid-template-columns: repeat(3, var(--label-width));
            grid-auto-rows: var(--label-height);
            gap: 1mm;
            justify-content: center;   /* ← center dalam wrapper */
            align-content: start;
            /* Tidak ada padding lagi — sudah di wrapper */
        }

        /* ============================================================
        LABEL CARD
        ============================================================ */
        .label-card {
            width: var(--label-width);
            height: var(--label-height);
            background: #ffffff;
            border-radius: 1.5mm;
            position: relative;
            overflow: hidden;
            border: 0.3mm solid #e0e6ed;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .label-accent-left {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 1.2mm;
            background: linear-gradient(180deg,
                var(--accent) 0%,
                var(--cobalt) 50%,
                var(--accent) 100%);
        }

        .label-corner-decor {
            position: absolute;
            top: 0;
            right: 0;
            width: 20mm;
            height: 20mm;
            opacity: 0.04;
            background-image:
                linear-gradient(var(--accent) 0.2mm, transparent 0.2mm),
                linear-gradient(90deg, var(--accent) 0.2mm, transparent 0.2mm);
            background-size: 2mm 2mm;
            background-position: top right;
        }

        .label-watermark {
            position: absolute;
            bottom: -12mm;
            right: -12mm;
            width: 35mm;
            height: 35mm;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 100, 224, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .label-content {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 3mm 3mm 2.5mm 4mm;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 1.5mm;
        }

        /* ============ HEADER ============ */
        .label-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2mm;
        }

        .label-brand {
            display: flex;
            align-items: center;
            gap: 1.5mm;
            min-width: 0;
            flex: 1;
        }

        .label-logo {
            width: 6.5mm;
            height: 6.5mm;
            border-radius: 1.5mm;
            background: linear-gradient(135deg, var(--cobalt) 0%, var(--cobalt-deep) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 9pt;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0, 100, 224, 0.4);
            overflow: hidden;   /* ← BARU */
        }

        /* ← BARU: style untuk logo image */
        .label-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .label-school-info {
            min-width: 0;
            flex: 1;
        }

        .label-school-name {
            font-size: 5.5pt;
            font-weight: 800;
            color: var(--ink-deep);
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0 0 0.2mm 0;
        }

        .label-school-tag {
            font-size: 3.8pt;
            color: var(--steel);
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin: 0;
        }

        /* ============ BADGE GROUP ============ */
        .label-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8mm;
            margin-top: 0.3mm;
        }

        .label-category,
        .label-funding {
            display: inline-flex;
            align-items: center;
            gap: 0.6mm;
            padding: 0.6mm 1.5mm;
            border-radius: 100px;
            font-size: 4pt;
            font-weight: 800;
            letter-spacing: 0.2px;
            text-transform: uppercase;
            white-space: nowrap;
            flex-shrink: 0;
            max-width: 100%;
        }

        .label-category {
            background: var(--cobalt-soft);
            color: var(--cobalt-deep);
        }

        .label-funding {
            background: #fef3c7;
            color: #92400e;
        }

        .label-category i,
        .label-funding i {
            font-size: 3.5pt;
        }

        /* ============ MAIN BODY ============ */
        .label-main {
            display: flex;
            align-items: center;
            gap: 2.5mm;
            min-height: 0;
        }

        .label-qr-block {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5mm;
        }

        .label-qr-frame {
            padding: 1mm;
            background: white;
            border-radius: 1.5mm;
            border: 0.3mm solid var(--hairline);
        }

        .label-qr-frame svg {
            width: 11mm !important;
            height: 11mm !important;
            display: block;
        }

        .label-qr-label {
            font-size: 3.2pt;
            font-weight: 800;
            color: var(--stone);
            text-transform: uppercase;
        }

        .label-info-block {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 1mm;
        }

        .label-code {
            font-family: 'SF Mono', 'Monaco', 'Courier New', monospace;
            font-size: 5.5pt;
            font-weight: 700;
            color: var(--cobalt-deep);
            line-height: 1;
            padding-bottom: 0.8mm;
            border-bottom: 0.3mm solid var(--hairline);
        }

        .label-name {
            font-size: 6.5pt;
            font-weight: 800;
            color: var(--ink-deep);
            line-height: 1.15;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .label-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5mm 1.5mm;
            margin-top: auto;
        }

        .label-meta-item {
            display: flex;
            align-items: center;
            gap: 0.6mm;
            font-size: 4pt;
            color: var(--steel);
            font-weight: 600;
        }

        .label-meta-item i {
            color: var(--cobalt);
            font-size: 3.5pt;
            width: 1.8mm;
            text-align: center;
        }

        .label-meta-item span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 20mm;
        }

        /* ============ FOOTER ============ */
        .label-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1mm;
            border-top: 0.3mm solid var(--hairline);
        }

        .label-footer-left {
            display: flex;
            align-items: center;
            gap: 1mm;
            font-size: 3.5pt;
            color: var(--stone);
            font-weight: 700;
            text-transform: uppercase;
        }

        .label-footer-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6mm;
            padding: 0.4mm 1.2mm;
            background: var(--accent);
            color: white;
            border-radius: 100px;
            font-size: 3.2pt;
            font-weight: 800;
        }

        .label-footer-badge .dot {
            width: 0.7mm;
            height: 0.7mm;
            border-radius: 50%;
            background: #22c55e;
        }

        .label-footer-right {
            font-size: 3.5pt;
            color: var(--stone);
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ============================================================
        PRINT — INI BAGIAN KRITIS
        ============================================================ */
        @page {
            size: A4;
            margin: 0;          /* ← margin 0, padding di wrapper */
        }

        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            body {
                padding: 0 !important;
            }

            .toolbar,
            .info-box,
            .no-print {
                display: none !important;
            }

            .print-wrapper {
                width: 21cm;
                padding: 1cm;
                margin: 0;
                background: white;
                box-shadow: none;
            }

            .label-container {
                /* Wrapper sudah handle width & padding */
                margin: 0;
                padding: 0;
                gap: 1mm;
            }

            .label-card {
                box-shadow: none !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Setiap 18 label = halaman baru */
            .label-card:nth-child(18n+1) {
                page-break-before: always;
            }

            /* Tapi jangan break di halaman pertama */
            .label-card:first-child {
                page-break-before: auto;
            }
        }
    </style>
</head>
<body>

    {{-- ==================== TOOLBAR ==================== --}}
    <div class="toolbar no-print">
        <div class="toolbar-info">
            <h1>
                <i class="fas fa-tags" style="color: #0064e0; margin-right: 6px;"></i>
                Cetak Label Massal
            </h1>
            <p>
                {{ count($items) }} label × 1 = 
                <strong>{{ count($items) }} label</strong> 
                (≈ {{ ceil(count($items) / 18) }} halaman A4)
            </p>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('items.index') }}" class="btn btn-ghost">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Cetak Sekarang
            </button>
        </div>
    </div>

    {{-- ==================== INFO BOX ==================== --}}
    <div class="info-box no-print">
        <i class="fas fa-info-circle"></i>
        <span>
            Layout A4: <strong>3 kolom × 6 baris = 18 label</strong> per halaman.
            Ukuran label: <strong>7 × 5 cm</strong>.
            Gunakan <strong>Ctrl+P</strong> atau tombol <strong>Cetak Sekarang</strong>.
            Pengaturan cetak: <strong>Skala 100%</strong>, <strong>Margin: None</strong>.
        </span>
    </div>

    {{-- ==================== LABEL GRID ==================== --}}
    @if($items->isEmpty())
        <div class="no-print" style="text-align: center; padding: 60px; color: #8595a4;">
            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; color: #ced0d4;"></i>
            <p>Tidak ada barang yang dipilih.</p>
        </div>
    @else
        <div class="print-wrapper">
            <div class="label-container">
                @foreach($items as $item)
                    <div class="label-card">

                        {{-- Decorative --}}
                        <div class="label-accent-left"></div>
                        <div class="label-corner-decor"></div>
                        <div class="label-watermark"></div>

                        {{-- Content --}}
                        <div class="label-content">

                            {{-- Header --}}
                            <div class="label-header">
                                <div class="label-brand">
                                    <div class="label-logo">
                                        @if($logoExists)
                                            <img src="{{ asset('storage/' . $logoFile) }}" alt="Logo">
                                        @else
                                            {{ $schoolInitial }}
                                        @endif
                                    </div>
                                    <div class="label-school-info">
                                        <p class="label-school-name">
                                            {{ Str::limit($schoolName, 30) }}
                                        </p>
                                        <p class="label-school-tag">Inventaris Sarpras</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Body --}}
                            <div class="label-main">
                                <div class="label-qr-block">
                                    <div class="label-qr-frame">
                                        {!! QrCode::size(100)->margin(0)->errorCorrection('H')->generate($item->code) !!}
                                    </div>
                                    <div class="label-qr-label">Scan</div>
                                </div>

                                <div class="label-info-block">
                                    <div class="label-code">{{ $item->code }}</div>
                                    <p class="label-name">{{ $item->name }}</p>

                                    {{-- ← BARU: Badge group --}}
                                    @if($item->fundingSource || $item->category)
                                        <div class="label-badges">
                                            @if($item->fundingSource)
                                                <div class="label-funding">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    {{ $item->fundingSource->code }}
                                                </div>
                                            @endif

                                            @if($item->category)
                                                <div class="label-category">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $item->category->name }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="label-meta">
                                        @if($item->location)
                                            <div class="label-meta-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ Str::limit($item->location->name, 15) }}</span>
                                            </div>
                                        @endif
                                        @if($item->brand)
                                            <div class="label-meta-item">
                                                <i class="fas fa-cube"></i>
                                                <span>{{ Str::limit($item->brand, 10) }}</span>
                                            </div>
                                        @endif
                                        @if($item->purchase_year)
                                            <div class="label-meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <span>{{ $item->purchase_year }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="label-footer">
                                <div class="label-footer-left">
                                    <div class="label-footer-badge">
                                        <span class="dot"></span>
                                        <span>AKTIF</span>
                                    </div>
                                    <span>{{ now()->format('d M Y') }}</span>
                                </div>
                                <div class="label-footer-right">
                                    {{ setting('app_short_name', 'SISARPRAS') }}
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</body>
</html>