<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e1b4b; }
        .title { font-size: 16pt; font-weight: bold; color: #1e1b4b; text-align: left; }
        .subtitle { font-size: 11pt; color: #555555; }
        .meta-label { font-weight: bold; color: #333333; }
        
        table { border-collapse: collapse; width: 100%; margin-top: 10px; margin-bottom: 20px; }
        th { background-color: #201c4b; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 8px; text-align: center; }
        th.sub-th { background-color: #6857df; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 6px; }
        td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: middle; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .currency { mso-number-format: "\Rp\#\,\#\#0"; text-align: right; }
        .number { mso-number-format: "\#\,\#\#0"; text-align: center; }
        .date { mso-number-format: "dd\/mm\/yyyy\ hh\:mm"; text-align: center; }
        
        .total-row td { background-color: #f1efff; font-weight: bold; border-top: 2px solid #201c4b; border-bottom: 2px solid #201c4b; }
    </style>
</head>
<body>
    {{-- Header Section --}}
    <table>
        <tr>
            <td colspan="7" class="title">LAPORAN KEUANGAN & PENDAPATAN PLATFORM SKILLPATH</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">PT SkillPath Edukasi Indonesia • Tahun Anggaran {{ $year }}</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">Waktu Ekspor: {{ $generatedAt }}</td>
        </tr>
        <tr><td colspan="7"></td></tr>
    </table>

    {{-- Executive Summary Table --}}
    <table>
        <tr>
            <th colspan="4" style="background-color:#1e1b4b; color:#ffffff; text-align:left; font-size:12pt;">RINGKASAN EKSEKUTIF PENDAPATAN</th>
        </tr>
        <tr>
            <td class="meta-label" style="background-color:#f8fafc; width:220px;">Total Transaksi Terverifikasi</td>
            <td class="text-right" style="font-weight:bold; width:180px;">{{ $totalOrders }} Transaksi</td>
            <td class="meta-label" style="background-color:#f8fafc; width:220px;">Total Omset Bruto (100%)</td>
            <td class="currency" style="font-weight:bold; color:#1e1b4b; width:180px;">Rp{{ number_format($grossRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="meta-label" style="background-color:#f8fafc;">Bagi Hasil Seluruh Mentor (80%)</td>
            <td class="currency" style="font-weight:bold; color:#5b36f5;">Rp{{ number_format($mentorShare, 0, ',', '.') }}</td>
            <td class="meta-label" style="background-color:#f8fafc;">Margin Bersih SkillPath (20%)</td>
            <td class="currency" style="font-weight:bold; color:#166534;">Rp{{ number_format($platformShare, 0, ',', '.') }}</td>
        </tr>
    </table>

    <br/>

    {{-- Monthly Breakdown Table --}}
    <table>
        <thead>
            <tr>
                <th colspan="6" style="background-color:#1e1b4b; color:#ffffff; text-align:left; font-size:12pt;">REKAPITULASI PENDAPATAN BULANAN TAHUN {{ $year }}</th>
            </tr>
            <tr>
                <th class="sub-th" style="width:50px;">No</th>
                <th class="sub-th" style="width:140px;">Bulan</th>
                <th class="sub-th" style="width:120px;">Jumlah Pesanan</th>
                <th class="sub-th" style="width:180px;">Pendapatan Bruto (Rp)</th>
                <th class="sub-th" style="width:180px;">Bagi Hasil Mentor 80% (Rp)</th>
                <th class="sub-th" style="width:180px;">Keuntungan SkillPath 20% (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totOrders = 0;
                $totGross = 0;
                $totMentor = 0;
                $totPlatform = 0;
            @endphp
            @foreach($monthlyReport as $idx => $m)
                @php
                    $totOrders += $m['orders_count'];
                    $totGross += $m['gross'];
                    $totMentor += $m['mentor_payout'];
                    $totPlatform += $m['platform_profit'];
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-left"><b>{{ $m['month_name'] }}</b></td>
                    <td class="text-center">{{ $m['orders_count'] }}</td>
                    <td class="currency">Rp{{ number_format($m['gross'], 0, ',', '.') }}</td>
                    <td class="currency">Rp{{ number_format($m['mentor_payout'], 0, ',', '.') }}</td>
                    <td class="currency">Rp{{ number_format($m['platform_profit'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-center"><b>TOTAL KESELURUHAN (TAHUN {{ $year }})</b></td>
                <td class="text-center"><b>{{ $totOrders }} Pesanan</b></td>
                <td class="currency"><b>Rp{{ number_format($totGross, 0, ',', '.') }}</b></td>
                <td class="currency"><b>Rp{{ number_format($totMentor, 0, ',', '.') }}</b></td>
                <td class="currency"><b>Rp{{ number_format($totPlatform, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <br/>

    {{-- Individual Transaction Details Table --}}
    <table>
        <thead>
            <tr>
                <th colspan="10" style="background-color:#1e1b4b; color:#ffffff; text-align:left; font-size:12pt;">DAFTAR RINCIAN TRANSAKSI TERVERIFIKASI</th>
            </tr>
            <tr>
                <th class="sub-th" style="width:40px;">No</th>
                <th class="sub-th" style="width:140px;">Kode Invoice</th>
                <th class="sub-th" style="width:120px;">Tanggal Bayar</th>
                <th class="sub-th" style="width:140px;">Nama Orang Tua</th>
                <th class="sub-th" style="width:120px;">Siswa (Anak)</th>
                <th class="sub-th" style="width:180px;">Kursus / Program</th>
                <th class="sub-th" style="width:130px;">Mentor Kursus</th>
                <th class="sub-th" style="width:150px;">Total Bayar (Rp)</th>
                <th class="sub-th" style="width:150px;">Bagi Hasil Mentor 80% (Rp)</th>
                <th class="sub-th" style="width:150px;">Komisi Platform 20% (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tIdx => $trx)
                @php
                    $tTotal = (float) $trx->total;
                    $tMentor = round($tTotal * 0.80);
                    $tPlatform = round($tTotal * 0.20);
                    $childName = optional(optional($trx->enrollment)->child)->name ?? 'Anak';
                    $courseTitle = optional(optional($trx->enrollment)->course)->title ?? 'Course';
                    $mentorName = optional(optional(optional($trx->enrollment)->course)->instructor)->name ?? 'Mentor';
                    $parentName = optional($trx->parent)->name ?? 'Orang Tua';
                @endphp
                <tr>
                    <td class="text-center">{{ $tIdx + 1 }}</td>
                    <td class="text-center" style="font-family:monospace; font-weight:bold;">{{ $trx->invoice_code }}</td>
                    <td class="text-center">{{ $trx->paid_at ? $trx->paid_at->format('d/m/Y H:i') : $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $parentName }}</td>
                    <td>{{ $childName }}</td>
                    <td>{{ $courseTitle }}</td>
                    <td>{{ $mentorName }}</td>
                    <td class="currency">Rp{{ number_format($tTotal, 0, ',', '.') }}</td>
                    <td class="currency">Rp{{ number_format($tMentor, 0, ',', '.') }}</td>
                    <td class="currency">Rp{{ number_format($tPlatform, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="color:#64748b; padding:18px;">Tidak ada riwayat transaksi pada periode ini.</td>
                </tr>
            @endforelse
            @if($transactions->isNotEmpty())
                <tr class="total-row">
                    <td colspan="7" class="text-center"><b>TOTAL DARI {{ $transactions->count() }} TRANSAKSI</b></td>
                    <td class="currency"><b>Rp{{ number_format($transactions->sum('total'), 0, ',', '.') }}</b></td>
                    <td class="currency"><b>Rp{{ number_format(round($transactions->sum('total') * 0.80), 0, ',', '.') }}</b></td>
                    <td class="currency"><b>Rp{{ number_format(round($transactions->sum('total') * 0.20), 0, ',', '.') }}</b></td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
