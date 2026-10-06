{{-- resources/views/items/barcode.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label - {{ $item->code }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])

    @php
        // Load data sekolah untuk logo
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
           Single: 9 × 5.8 cm (scale 1.38× dari batch 6.5 × 4.2 cm)
           ============================================================ */
        :root {
            --label-width: 9cm;
            --label-height: 5.8cm;

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

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            background: 
                radial-gradient(circle at 20% 20%, rgba(0, 100, 224, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(10, 19, 23, 0.06) 0%, transparent 50%),
                #f1f4f7;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }

        /* ============ TOOLBAR ============ */
        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-radius: 100px;
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 10px 30px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: -0.14px;
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
           LABEL CARD — SAME STRUCTURE AS BATCH, SCALED UP
           ============================================================ */
        .label-card {
            width: var(--label-width);
            height: var(--label-height);
            background: #ffffff;
            border-radius: 3mm;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.03),
                0 8px 24px rgba(0, 0, 0, 0.06),
                0 32px 64px -16px rgba(10, 19, 23, 0.2);
        }

        /* Left accent stripe */
        .label-accent-left {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 1.8mm;
            background: linear-gradient(180deg,
                var(--accent) 0%,
                var(--cobalt) 50%,
                var(--accent) 100%);
        }

        /* Corner decor */
        .label-corner-decor {
            position: absolute;
            top: 0;
            right: 0;
            width: 28mm;
            height: 28mm;
            opacity: 0.04;
            background-image:
                linear-gradient(var(--accent) 0.3mm, transparent 0.3mm),
                linear-gradient(90deg, var(--accent) 0.3mm, transparent 0.3mm);
            background-size: 2.8mm 2.8mm;
            background-position: top right;
        }

        /* Watermark */
        .label-watermark {
            position: absolute;
            bottom: -16mm;
            right: -16mm;
            width: 48mm;
            height: 48mm;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 100, 224, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Content wrapper */
        .label-content {
            position: relative;
            z-index: 1;
            height: 100%;
            padding: 4mm 4mm 3.5mm 5.5mm;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 2mm;
        }

        /* ============ HEADER ============ */
        .label-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2.5mm;
        }

        .label-brand {
            display: flex;
            align-items: center;
            gap: 2mm;
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
            font-size: 7.5pt;
            font-weight: 800;
            color: var(--ink-deep);
            line-height: 1.15;
            letter-spacing: -0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0 0 0.3mm 0;
        }

        .label-school-tag {
            font-size: 5.2pt;
            color: var(--steel);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        /* ============ MAIN BODY ============ */
        .label-main {
            display: flex;
            align-items: center;
            gap: 3.5mm;
            min-height: 0;
        }

        /* QR Code */
        .label-qr-block {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.7mm;
        }

        .label-qr-frame {
            padding: 1.4mm;
            background: white;
            border-radius: 2mm;
            border: 0.4mm solid var(--hairline);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .label-qr-frame svg {
            width: 15mm !important;
            height: 15mm !important;
            display: block;
        }

        .label-qr-label {
            font-size: 4.4pt;
            font-weight: 800;
            color: var(--stone);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        /* Info Block */
        .label-info-block {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 1.4mm;
        }

        .label-code {
            font-family: 'SF Mono', 'Monaco', 'Courier New', monospace;
            font-size: 7.5pt;
            font-weight: 700;
            color: var(--cobalt-deep);
            letter-spacing: -0.3px;
            line-height: 1;
            padding-bottom: 1.2mm;
            border-bottom: 0.4mm solid var(--hairline);
        }

        .label-name {
            font-size: 9pt;
            font-weight: 800;
            color: var(--ink-deep);
            line-height: 1.15;
            letter-spacing: -0.3px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        /* Badge Group */
        .label-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2mm;
            margin-top: 0.5mm;
        }

        .label-category,
        .label-funding {
            display: inline-flex;
            align-items: center;
            gap: 0.8mm;
            padding: 0.8mm 2mm;
            border-radius: 100px;
            font-size: 5.5pt;
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
            font-size: 5pt;
        }

        /* Meta */
        .label-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8mm 2.5mm;
            margin-top: auto;
        }

        .label-meta-item {
            display: flex;
            align-items: center;
            gap: 0.8mm;
            font-size: 5.5pt;
            color: var(--steel);
            font-weight: 600;
            line-height: 1;
        }

        .label-meta-item i {
            color: var(--cobalt);
            font-size: 5pt;
            width: 2.5mm;
            text-align: center;
        }

        .label-meta-item span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 28mm;
        }

        /* ============ FOOTER ============ */
        .label-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.4mm;
            border-top: 0.4mm solid var(--hairline);
        }

        .label-footer-left {
            display: flex;
            align-items: center;
            gap: 1.4mm;
            font-size: 4.8pt;
            color: var(--stone);
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .label-footer-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.8mm;
            padding: 0.6mm 1.8mm;
            background: var(--accent);
            color: white;
            border-radius: 100px;
            font-size: 4.4pt;
            font-weight: 800;
            letter-spacing: 0.4px;
        }

        .label-footer-badge .dot {
            width: 1mm;
            height: 1mm;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 3px #22c55e;
        }

        .label-footer-right {
            font-size: 4.8pt;
            color: var(--stone);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ============ PRINT ============ */
        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                padding: 0;
                background: white;
                display: block;
                min-height: auto;
            }

            .toolbar {
                display: none !important;
            }

            .label-card {
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    {{-- ==================== TOOLBAR ==================== --}}
    <div class="toolbar no-print">
        <a href="{{ route('items.show', $item->id) }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak Label
        </button>
    </div>

    {{-- ==================== LABEL CARD ==================== --}}
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
                            {{ Str::limit($schoolName, 40) }}
                        </p>
                        <p class="label-school-tag">Inventaris Sarpras</p>
                    </div>
                </div>
            </div>

            {{-- Main Body --}}
            <div class="label-main">

                {{-- QR --}}
                <div class="label-qr-block">
                    <div class="label-qr-frame">
                        {!! QrCode::size(150)->margin(0)->errorCorrection('H')->generate($item->code) !!}
                    </div>
                    <div class="label-qr-label">Scan</div>
                </div>

                {{-- Info --}}
                <div class="label-info-block">
                    <div class="label-code">{{ $item->code }}</div>
                    <p class="label-name">{{ $item->name }}</p>

                    {{-- Badges: Sumber Dana + Kategori --}}
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

                    {{-- Meta --}}
                    <div class="label-meta">
                        @if($item->location)
                            <div class="label-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ Str::limit($item->location->name, 20) }}</span>
                            </div>
                        @endif

                        @if($item->brand)
                            <div class="label-meta-item">
                                <i class="fas fa-cube"></i>
                                <span>{{ Str::limit($item->brand, 15) }}</span>
                            </div>
                        @endif

                        @if($item->purchase_year)
                            <div class="label-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $item->purchase_year }}</span>
                            </div>
                        @endif

                        @if($item->serial_number)
                            <div class="label-meta-item">
                                <i class="fas fa-fingerprint"></i>
                                <span>SN: {{ Str::limit($item->serial_number, 15) }}</span>
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

</body>
</html>