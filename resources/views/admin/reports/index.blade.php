@extends('layouts.admin')
@section('title', 'Laporan Pendapatan - Tahun ' . $selectedYear)

@section('content')
<section class="admin-reports-view">
    {{-- Header Row with Year Switcher Dropdown & Excel Export --}}
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">REKAPITULASI FINANSIAL</span>
            <h1>Laporan Pendapatan Platform</h1>
            <p>Rincian omset bruto, bagi hasil pengajar (80%), dan margin keuntungan operasional SkillPath (20%) untuk <b>Tahun {{ $selectedYear }}</b>.</p>
        </div>
        
        <div class="admin-action-group" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            {{-- Year Select Dropdown --}}
            <div class="admin-year-select-wrap" style="position:relative; display:inline-flex; align-items:center;">
                <span style="position:absolute; left:12px; pointer-events:none; color:#5b36f5; display:flex; align-items:center;">
                    <x-icon name="calendar" style="width:15px; height:15px;" />
                </span>
                <select id="yearSelect" 
                        name="year" 
                        onchange="if(this.value) window.location.href = '{{ route('admin.reports.index') }}?year=' + this.value;" 
                        class="admin-select" 
                        style="padding:10px 38px 10px 36px; font-size:13px; font-weight:800; border-radius:11px; border:1.5px solid #dcdbe7; background:#ffffff; color:#1e1b4b; cursor:pointer; outline:none; appearance:none; -webkit-appearance:none; box-shadow:0 2px 6px rgba(0,0,0,0.04); transition:all 0.2s;">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (int)$selectedYear === (int)$yr ? 'selected' : '' }}>
                            Tahun {{ $yr }}
                        </option>
                    @endforeach
                </select>
                <span style="position:absolute; right:12px; pointer-events:none; color:#8a84ab; display:flex; align-items:center;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>

            {{-- Excel Export Button --}}
            <a href="{{ route('admin.reports.export', ['year' => $selectedYear]) }}" 
               class="btn-admin-primary" 
               title="Unduh laporan Excel untuk tahun {{ $selectedYear }}"
               style="background:#107c41; border-color:#107c41; color:#ffffff; display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:11px; font-size:12px; font-weight:800; text-decoration:none; box-shadow:0 4px 12px rgba(16,124,65,0.25);">
                <x-icon name="excel" style="width:16px; height:16px;" />
                <span>Ekspor Excel {{ $selectedYear }} (.xls)</span>
            </a>
        </div>
    </div>

    {{-- 3 Stat Cards Summary for Selected Year --}}
    <div class="admin-stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-green-admin">
                <x-icon name="wallet" />
            </div>
            <div class="admin-stat-data">
                <span class="label">PENDAPATAN BRUTO (TAHUN {{ $selectedYear }})</span>
                <b class="value">Rp{{ number_format($grossRevenue, 0, ',', '.') }}</b>
                <small class="desc">Dari {{ $totalOrders }} transaksi terverifikasi di {{ $selectedYear }}</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-purple-admin">
                <x-icon name="person" />
            </div>
            <div class="admin-stat-data">
                <span class="label">BAGI HASIL PENGAJAR (80%)</span>
                <b class="value">Rp{{ number_format($mentorShare, 0, ',', '.') }}</b>
                <small class="desc">Hak remunerasi mentor tahun {{ $selectedYear }}</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-orange-admin">
                <x-icon name="earnings" />
            </div>
            <div class="admin-stat-data">
                <span class="label">KEUNTUNGAN PLATFORM (20%)</span>
                <b class="value">Rp{{ number_format($platformShare, 0, ',', '.') }}</b>
                <small class="desc">Laba bersih SkillPath tahun {{ $selectedYear }}</small>
            </div>
        </article>
    </div>

    {{-- 1. Consistent 12-Month Revenue Breakdown Table --}}
    <div class="admin-panel" style="padding: 0; overflow:hidden; margin-bottom: 28px;">
        <div class="admin-panel-head" style="padding: 20px 24px; margin-bottom:0; border-bottom:1px solid #eaebf4; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span class="kicker">REKAPITULASI BULANAN</span>
                <h2>Rincian Pendapatan 12 Bulan (Tahun {{ $selectedYear }})</h2>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:12px; color:#5c567e; font-weight:700;">Periode: <b>1 Jan {{ $selectedYear }} - 31 Des {{ $selectedYear }}</b></span>
                <a href="{{ route('admin.reports.export', ['year' => $selectedYear]) }}" style="font-size:12px; font-weight:800; color:#107c41; text-decoration:none; display:inline-flex; align-items:center; gap:5px; background:#f0fdf4; border:1px solid #bbf7d0; padding:6px 12px; border-radius:8px;">
                    <x-icon name="download" style="width:14px; height:14px;" /> Unduh Excel Tahun {{ $selectedYear }}
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table" style="table-layout: fixed; width: 100%; min-width: 780px;">
                <colgroup>
                    <col style="width: 22%;">
                    <col style="width: 14%;">
                    <col style="width: 20%;">
                    <col style="width: 18%;">
                    <col style="width: 16%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align:left; padding-left:24px;">Bulan</th>
                        <th style="text-align:center;">Pesanan</th>
                        <th style="text-align:right;">Pendapatan Bruto</th>
                        <th style="text-align:right;">Bagi Hasil Mentor (80%)</th>
                        <th style="text-align:right;">Margin Platform (20%)</th>
                        <th style="text-align:center; padding-right:24px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandOrders = 0;
                        $grandGross = 0;
                        $grandMentor = 0;
                        $grandPlatform = 0;
                    @endphp
                    @foreach($monthlyReport as $m)
                        @php
                            $grandOrders += $m['orders_count'];
                            $grandGross += $m['gross'];
                            $grandMentor += $m['mentor_payout'];
                            $grandPlatform += $m['platform_profit'];
                            $hasRevenue = $m['gross'] > 0;
                        @endphp
                        <tr style="{{ $hasRevenue ? 'background: #ffffff;' : 'background: #fafbfe; opacity: 0.85;' }}">
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; font-size:11px; font-weight:800; {{ $hasRevenue ? 'background:#eeebff; color:#5b36f5;' : 'background:#eff0f6; color:#9ca3af;' }}">
                                        {{ str_pad($m['month_num'], 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <b style="color: {{ $hasRevenue ? '#120e2e' : '#6b7280' }}; font-size:13px;">{{ $m['month_name'] }}</b>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-weight:700; color: {{ $hasRevenue ? '#5c567e' : '#9ca3af' }};">
                                    {{ $m['orders_count'] }} Pesanan
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <b style="color: {{ $hasRevenue ? '#120e2e' : '#9ca3af' }}; font-size:13px;">
                                    Rp{{ number_format($m['gross'], 0, ',', '.') }}
                                </b>
                            </td>
                            <td style="text-align:right;">
                                <span style="color: {{ $hasRevenue ? '#5b36f5' : '#9ca3af' }}; font-weight:700;">
                                    Rp{{ number_format($m['mentor_payout'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <span style="color: {{ $hasRevenue ? '#166534' : '#9ca3af' }}; font-weight:800;">
                                    Rp{{ number_format($m['platform_profit'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center; padding-right:24px;">
                                <span class="status-pill {{ $hasRevenue ? 'paid' : '' }}" style="{{ !$hasRevenue ? 'background:#f1f2f8; color:#9ca3af;' : '' }}">
                                    {{ $hasRevenue ? 'Tercatat' : 'Nihil' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f4f3fb; font-weight:800; border-top:2px solid #dcd7fa;">
                        <td style="padding-left:24px; color:#120e2e; font-size:12px; font-weight:900;">TOTAL TAHUN {{ $selectedYear }}</td>
                        <td style="text-align:center; color:#5c567e; font-size:12px; font-weight:800;">{{ $grandOrders }} Transaksi</td>
                        <td style="text-align:right; color:#120e2e; font-size:13px; font-weight:900;">Rp{{ number_format($grandGross, 0, ',', '.') }}</td>
                        <td style="text-align:right; color:#5b36f5; font-size:13px; font-weight:900;">Rp{{ number_format($grandMentor, 0, ',', '.') }}</td>
                        <td style="text-align:right; color:#166534; font-size:13px; font-weight:900;">Rp{{ number_format($grandPlatform, 0, ',', '.') }}</td>
                        <td style="text-align:center; padding-right:24px;">
                            <span class="status-pill {{ $grandGross > 0 ? 'paid' : '' }}">
                                {{ $grandGross > 0 ? 'Aktif' : 'Nihil' }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 2. Individual Verified Transactions Table for Selected Year --}}
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div class="admin-panel-head" style="padding: 20px 24px; margin-bottom:0; border-bottom:1px solid #eaebf4; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <span class="kicker">DETAIL TRANSAKSI TERVERIFIKASI</span>
                <h2>Daftar Riwayat Transaksi Tahun {{ $selectedYear }}</h2>
            </div>
            <span class="status-pill paid" style="font-size:11px;">
                {{ $transactions->count() }} Transaksi Lunas di {{ $selectedYear }}
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table" style="table-layout: fixed; width: 100%; min-width: 900px;">
                <colgroup>
                    <col style="width: 14%;">
                    <col style="width: 13%;">
                    <col style="width: 14%;">
                    <col style="width: 13%;">
                    <col style="width: 18%;">
                    <col style="width: 14%;">
                    <col style="width: 14%;">
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align:left; padding-left:24px;">Invoice</th>
                        <th style="text-align:left;">Tanggal Bayar</th>
                        <th style="text-align:left;">Orang Tua</th>
                        <th style="text-align:left;">Siswa (Anak)</th>
                        <th style="text-align:left;">Kursus & Mentor</th>
                        <th style="text-align:right;">Total (Rp)</th>
                        <th style="text-align:right; padding-right:24px;">Bagi Hasil (80/20)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        @php
                            $tTotal = (float) $trx->total;
                            $tMentor = round($tTotal * 0.80);
                            $tPlatform = round($tTotal * 0.20);
                            $childName = optional(optional($trx->enrollment)->child)->name ?? 'Anak';
                            $courseTitle = optional(optional($trx->enrollment)->course)->title ?? 'Course';
                            $mentorName = optional(optional(optional($trx->enrollment)->course)->instructor)->name ?? 'Mentor';
                            $parentName = optional($trx->parent)->name ?? 'Orang Tua';
                            $payDate = $trx->paid_at ? $trx->paid_at->translatedFormat('d M Y, H:i') : $trx->created_at->translatedFormat('d M Y, H:i');
                        @endphp
                        <tr>
                            <td style="padding-left:24px;">
                                <b style="font-family:monospace; font-size:12px; color:#5b36f5;">{{ $trx->invoice_code }}</b>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#5c567e;">{{ $payDate }}</span>
                            </td>
                            <td>
                                <b style="color:#120e2e; font-size:12.5px;">{{ $parentName }}</b>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#4b5563;">{{ $childName }}</span>
                            </td>
                            <td>
                                <div>
                                    <b style="color:#120e2e; font-size:12px; display:block;">{{ $courseTitle }}</b>
                                    <small style="color:#8a84ab; font-size:11px;">Mentor: {{ $mentorName }}</small>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <b style="color:#120e2e; font-size:13px;">Rp{{ number_format($tTotal, 0, ',', '.') }}</b>
                            </td>
                            <td style="text-align:right; padding-right:24px;">
                                <div>
                                    <span style="color:#5b36f5; font-size:11.5px; font-weight:700; display:block;">Mentor: Rp{{ number_format($tMentor, 0, ',', '.') }}</span>
                                    <span style="color:#166534; font-size:11px; font-weight:800; display:block;">SkillPath: Rp{{ number_format($tPlatform, 0, ',', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px 20px; color:#8a84ab;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                    <x-icon name="wallet" style="width:32px; height:32px; color:#cbd5e1;" />
                                    <b style="font-size:14px; color:#4b5563;">Tidak Ada Transaksi di Tahun {{ $selectedYear }}</b>
                                    <p style="font-size:12px; margin:0;">Belum ada pesanan terverifikasi yang tercatat pada tahun anggaran {{ $selectedYear }}.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
