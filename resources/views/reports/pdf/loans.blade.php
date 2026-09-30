{{-- resources/views/reports/pdf/loans.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Barang</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #0a1317;
            margin: 0;
            padding: 0;
        }

        /* ============ KOP SURAT ============ */
        table.kop {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #0a1317;
            margin-bottom: 4px;
        }

        table.kop td {
            vertical-align: middle;
            padding: 5px;
            border: none;
        }

        td.kop-logo {
            width: 85px;
            text-align: center;
        }

        td.kop-logo .logo-circle {
            width: 70px;
            height: 70px;
            line-height: 70px;
            background: #0064e0;
            border-radius: 50%;
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 0 auto;
        }

        td.kop-logo img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        td.kop-text {
            text-align: center;
        }

        .school-name {
            font-size: 17px;
            font-weight: bold;
            color: #0a1317;
            text-transform: uppercase;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .school-address {
            font-size: 9.5px;
            color: #1c1e21;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .school-contact {
            font-size: 8.5px;
            color: #5d6c7b;
            line-height: 1.3;
        }

        td.kop-spacer {
            width: 85px;
        }

        /* ============ JUDUL ============ */
        .judul {
            text-align: center;
            margin: 12px 0 10px 0;
        }

        .judul h2 {
            font-size: 13px;
            font-weight: bold;
            color: #0a1317;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: underline;
            margin: 0 0 4px 0;
        }

        .judul p {
            font-size: 9.5px;
            color: #5d6c7b;
            margin: 0;
        }

        /* ============ RINGKASAN ============ */
        table.ringkasan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background: #f8fafc;
            border: 1px solid #dee3e9;
        }

        table.ringkasan td {
            padding: 8px 10px;
            font-size: 9.5px;
            text-align: center;
            border-right: 1px solid #dee3e9;
            color: #1c1e21;
        }

        table.ringkasan td:last-child {
            border-right: none;
        }

        table.ringkasan .label {
            color: #5d6c7b;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        table.ringkasan .value {
            font-weight: bold;
            color: #0a1317;
            font-size: 12px;
            margin-top: 2px;
        }

        /* ============ TABEL DATA ============ */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data thead {
            background: #0a1317;
            color: #ffffff;
        }

        table.data th {
            padding: 7px 5px;
            font-size: 9px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #0a1317;
            color: #ffffff;
        }

        table.data td {
            padding: 5px;
            font-size: 9px;
            border: 1px solid #dee3e9;
            vertical-align: middle;
            color: #1c1e21;
        }

        table.data tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-bold   { font-weight: bold; }
        .text-mono   { font-family: Courier, monospace; font-size: 8.5px; }

        /* ============ BADGE ============ */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #ffffff;
        }

        .badge-dipinjam     { background: #0064e0; }
        .badge-terlambat    { background: #e41e3f; }
        .badge-dikembalikan { background: #31a24c; }

        /* ============ TOTAL ROW ============ */
        tr.total-row {
            background: #0a1317;
            color: #ffffff;
        }

        tr.total-row td {
            font-weight: bold;
            font-size: 10px;
            padding: 8px 5px;
            border-color: #0a1317;
            color: #ffffff;
        }

        /* ============ TANDA TANGAN ============ */
        table.ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table.ttd td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .ttd-block {
            text-align: center;
            width: 200px;
            margin-left: auto;
        }

        .ttd-block p {
            font-size: 10px;
            color: #1c1e21;
            margin: 0 0 3px 0;
            line-height: 1.4;
        }

        .ttd-block .nama {
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #0a1317;
            padding-top: 3px;
            margin-top: 55px;
            display: block;
        }

        .ttd-block .nip {
            font-size: 9px;
            color: #5d6c7b;
            margin-top: 2px;
        }

        /* ============ FOOTER ============ */
        .footer {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #8595a4;
            border-top: 1px solid #dee3e9;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    {{-- ==================== KOP SURAT ==================== --}}
    @php
        $logoFile = setting('school_logo', 'logo-default.png');
        $logoExists = $logoFile 
            && $logoFile !== 'logo-default.png' 
            && file_exists(storage_path('app/public/' . $logoFile));
        $schoolInitial = strtoupper(substr(setting('school_name', 'S'), 0, 1));
    @endphp

    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if($logoExists)
                    <img src="{{ storage_path('app/public/' . $logoFile) }}" alt="Logo">
                @else
                    <div class="logo-circle">{{ $schoolInitial }}</div>
                @endif
            </td>
            <td class="kop-text">
                <div class="school-name">{{ setting('school_name', 'NAMA SEKOLAH') }}</div>
                <div class="school-address">{{ setting('school_address', 'Alamat Sekolah') }}</div>
                <div class="school-contact">
                    @if(setting('school_phone')) Telp: {{ setting('school_phone') }} @endif
                    @if(setting('school_email')) | Email: {{ setting('school_email') }} @endif
                    @if(setting('school_npsn')) | NPSN: {{ setting('school_npsn') }} @endif
                </div>
            </td>
            <td class="kop-spacer"></td>
        </tr>
    </table>

    {{-- ==================== JUDUL ==================== --}}
    <div class="judul">
        <h2>Laporan Peminjaman Barang</h2>
        <p>
            Periode: 
            {{ $filters['date_from'] ? \Carbon\Carbon::parse($filters['date_from'])->translatedFormat('d F Y') : 'Semua' }}
            — 
            {{ $filters['date_to'] ? \Carbon\Carbon::parse($filters['date_to'])->translatedFormat('d F Y') : 'Semua' }}
            <br>
            Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </p>
    </div>

    {{-- ==================== RINGKASAN ==================== --}}
    <table class="ringkasan">
        <tr>
            <td>
                <div class="label">Total</div>
                <div class="value">{{ number_format($summary['total']) }}</div>
            </td>
            <td>
                <div class="label">Dipinjam</div>
                <div class="value" style="color: #0064e0;">{{ number_format($summary['borrowed']) }}</div>
            </td>
            <td>
                <div class="label">Terlambat</div>
                <div class="value" style="color: #e41e3f;">{{ number_format($summary['overdue']) }}</div>
            </td>
            <td>
                <div class="label">Dikembalikan</div>
                <div class="value" style="color: #31a24c;">{{ number_format($summary['returned']) }}</div>
            </td>
            <td>
                <div class="label">Total Denda</div>
                <div class="value">{{ format_currency($summary['total_fines']) }}</div>
            </td>
        </tr>
    </table>

    {{-- ==================== TABEL DATA ==================== --}}
    @if($loans->count() > 0)
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 22px;" class="text-center">No</th>
                    <th style="width: 60px;">Kode</th>
                    <th>Barang</th>
                    <th style="width: 90px;">Peminjam</th>
                    <th style="width: 65px;" class="text-center">Tgl Pinjam</th>
                    <th style="width: 65px;" class="text-center">Jatuh Tempo</th>
                    <th style="width: 65px;" class="text-center">Tgl Kembali</th>
                    <th style="width: 55px;" class="text-center">Status</th>
                    <th style="width: 70px;" class="text-right">Denda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $index => $loan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-mono">{{ $loan->item->code ?? '-' }}</td>
                        <td>
                            <span class="text-bold">{{ $loan->item->name ?? '-' }}</span>
                            @if($loan->purpose)
                                <br><span style="font-size: 8px; color: #5d6c7b;">{{ Str::limit($loan->purpose, 40) }}</span>
                            @endif
                        </td>
                        <td>
                            {{ $loan->borrower->name ?? '-' }}
                            @if($loan->borrower && $loan->borrower->nip)
                                <br><span style="font-size: 8px; color: #5d6c7b;">{{ $loan->borrower->nip }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            {{ $loan->due_date->format('d/m/Y') }}
                            @if($loan->isOverdue() && !$loan->isReturned())
                                <br><span style="font-size: 7.5px; color: #e41e3f; font-weight: bold;">
                                    +{{ $loan->daysOverdue() }} hari
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($loan->status->value) {
                                    'dipinjam' => 'badge-dipinjam',
                                    'terlambat' => 'badge-terlambat',
                                    'dikembalikan' => 'badge-dikembalikan',
                                    default => 'badge-dipinjam',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ $loan->status->label() }}
                            </span>
                        </td>
                        <td class="text-right">
                            @if($loan->fine_amount > 0)
                                <span class="text-bold" style="color: #e41e3f;">
                                    {{ number_format($loan->fine_amount, 0, ',', '.') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- Total --}}
                <tr class="total-row">
                    <td colspan="8" class="text-right">TOTAL DENDA</td>
                    <td class="text-right">{{ format_currency($summary['total_fines']) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 30px; color: #8595a4; font-style: italic;">
            Tidak ada data peminjaman untuk periode yang dipilih.
        </div>
    @endif

    {{-- ==================== TANDA TANGAN ==================== --}}
    <table class="ttd">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <div class="ttd-block">
                    <p>{{ now()->translatedFormat('d F Y') }}</p>
                    <p>Kepala Sekolah,</p>
                    <p class="nama">{{ setting('headmaster_name', '_______________________') }}</p>
                    <p class="nip">
                        @if(setting('headmaster_nip'))
                            NIP. {{ setting('headmaster_nip') }}
                        @else
                            &nbsp;
                        @endif
                    </p>
                </div>
            </td>
        </tr>
    </table>

    {{-- ==================== FOOTER ==================== --}}
    <div class="footer">
        Dicetak dari Sistem Informasi Sarpras — {{ setting('school_name', 'Sekolah') }}
    </div>

</body>
</html>