@extends('layouts.app')
@section('title', $transaction->status === 'paid' ? 'Bukti Pembayaran - ' . $transaction->invoice_code : 'Pembayaran - ' . $transaction->invoice_code)

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }
}
</style>
@endpush

@section('content')
<section class="dashboard-page payment-page-wrap">
    <div class="dash-title no-print">
        <div>
            <span class="eyebrow">Payment Gateway</span>
            <h1>{{ $transaction->status === 'paid' ? 'Bukti Pembayaran & Invoice' : 'Instruksi Pembayaran' }}</h1>
            <p>{{ $transaction->status === 'paid' ? 'Pembayaran telah diverifikasi. Kelas anak Anda siap dimulai.' : 'Selesaikan pembayaran 1x transfer sebelum batas waktu berakhir untuk mengaktifkan reservasi kelas.' }}</p>
        </div>
        @if($transaction->status === 'paid')
            <div class="dash-actions">
                <button class="btn btn-ghost" onclick="window.print()">
                    <x-icon name="printer" /> Cetak Invoice
                </button>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="flash success">
            <x-icon name="check" />
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flash error">
            <x-icon name="conflict" />
            <div>
                <strong>Perhatian:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

@php
if (!function_exists('penyebut_nominal')) {
    function penyebut_nominal($nilai) {
        $nilai = abs((int)$nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut_nominal($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = penyebut_nominal(intval($nilai / 10)). " Puluh" . penyebut_nominal($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . penyebut_nominal($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut_nominal(intval($nilai / 100)) . " Ratus" . penyebut_nominal($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . penyebut_nominal($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut_nominal(intval($nilai / 1000)) . " Ribu" . penyebut_nominal($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut_nominal(intval($nilai / 1000000)) . " Juta" . penyebut_nominal($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut_nominal(intval($nilai / 1000000000)) . " Milyar" . penyebut_nominal(fmod($nilai, 1000000000));
        }
        return $temp;
    }
}
if (!function_exists('terbilang_rupiah')) {
    function terbilang_rupiah($nilai) {
        if ($nilai < 0) {
            $hasil = "Minus " . trim(penyebut_nominal($nilai));
        } else {
            $hasil = trim(penyebut_nominal($nilai));
        }
        return $hasil ? $hasil . " Rupiah" : "Nol Rupiah";
    }
}
@endphp

    @if($transaction->status === 'paid')
        {{-- OFFICIAL COMMERCIAL INVOICE (STANDARD FAKTUR PEMBAYARAN) --}}
        <div class="official-invoice-sheet print-target">
            {{-- Stamp Watermark --}}
            <div class="invoice-stamp-watermark paid">
                <span>LUNAS</span>
                <small>VERIFIED</small>
            </div>

            {{-- 1. Header Section --}}
            <div class="invoice-header-row">
                <div class="invoice-company-brand">
                    <div class="company-logo-badge">
                        <span class="logo-symbol">SP</span>
                        <div class="company-brand-text">
                            <h2>SKILLPATH</h2>
                            <span>PT SKILLPATH EDUKASI NUSANTARA</span>
                        </div>
                    </div>
                    <div class="company-address-block">
                        <p>Gedung EduTech Tower Lt. 8, SCBD Bisnis Park</p>
                        <p>Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan 12190</p>
                        <p>NPWP: 01.345.678.9-012.000 &bull; Email: billing@skillpath.id &bull; www.skillpath.id</p>
                    </div>
                </div>

                <div class="invoice-doc-title-block">
                    <h1 class="invoice-main-title">INVOICE</h1>
                    <div class="invoice-status-pill paid">
                        <x-icon name="check" /> LUNAS / PAID
                    </div>
                    <table class="invoice-meta-table">
                        <tr>
                            <td class="meta-label">No. Invoice</td>
                            <td class="meta-separator">:</td>
                            <td class="meta-val"><strong>{{ $transaction->invoice_code }}</strong></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tgl. Terbit</td>
                            <td class="meta-separator">:</td>
                            <td class="meta-val">{{ $transaction->created_at->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tgl. Lunas</td>
                            <td class="meta-separator">:</td>
                            <td class="meta-val">{{ $transaction->paid_at ? $transaction->paid_at->format('d F Y, H:i') . ' WIB' : $transaction->updated_at->format('d F Y, H:i') . ' WIB' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Metode Bayar</td>
                            <td class="meta-separator">:</td>
                            <td class="meta-val">{{ strtoupper(str_replace('_', ' ', $transaction->payment_method ?: 'Virtual Account')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="invoice-divider-line"></div>

            {{-- 2. Customer / Billing Details (2 Columns) --}}
            <div class="invoice-parties-grid">
                <div class="party-card billed-from">
                    <span class="party-caption">DITERBITKAN OLEH:</span>
                    <h3 class="party-name">PT SKILLPATH EDUKASI NUSANTARA</h3>
                    <p class="party-detail">Divisi Penagihan & Keuangan Digital</p>
                    <p class="party-detail">Layanan Bantuan: finance@skillpath.id &bull; (021) 5088-7766</p>
                </div>
                <div class="party-card billed-to">
                    <span class="party-caption">DITAGIHKAN KEPADA:</span>
                    <h3 class="party-name">{{ $transaction->parent->name ?? auth()->user()->name }}</h3>
                    <p class="party-detail"><strong>Email:</strong> {{ $transaction->parent->email ?? auth()->user()->email }}</p>
                    <p class="party-detail"><strong>No. Telepon:</strong> {{ $transaction->parent->phone ?? '-' }}</p>
                    <p class="party-detail"><strong>ID Pelanggan:</strong> SP-USER-{{ str_pad($transaction->parent_id ?? 1, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            {{-- 3. Itemized Products / Services Table --}}
            <div class="invoice-table-wrap">
                <table class="invoice-items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">No.</th>
                            <th style="width: 44%;">Deskripsi Kursus & Layanan</th>
                            <th style="width: 22%;">Jadwal & Lokasi Belajar</th>
                            <th style="width: 14%; text-align: center;">Paket / Sesi</th>
                            <th style="width: 15%; text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @if(isset($enrollments) && $enrollments->count() > 0)
                            @foreach($enrollments as $enr)
                                <tr>
                                    <td style="text-align: center;" class="row-num">{{ $no++ }}</td>
                                    <td>
                                        <div class="item-course-title">{{ $enr->course->title }}</div>
                                        <div class="item-meta-tags">
                                            <span class="meta-tag student"><x-icon name="child" /> Siswa: <b>{{ $enr->child->name ?? '-' }}</b></span>
                                            <span class="meta-tag cat"><x-icon name="spark" /> {{ $enr->course->category->name ?? 'Kursus' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-detail-text">
                                            <div class="sched-day"><x-icon name="calendar" /> Hari {{ $enr->schedule->day_name ?? '-' }}, {{ substr($enr->schedule->start_time ?? '', 0, 5) }} - {{ substr($enr->schedule->end_time ?? '', 0, 5) }} WIB</div>
                                            <div class="loc-text"><x-icon name="location" /> {{ $enr->course->location_name ?? 'SkillPath Learning Center' }}</div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="package-pill">{{ $enr->package_info['title'] ?? 'Paket Pilihan' }}</span>
                                        <small class="session-count-text">{{ $enr->package_info['sessions'] ?? $enr->total_sessions }} Sesi Pertemuan</small>
                                    </td>
                                    <td style="text-align: right;" class="item-price-val">
                                        Rp{{ number_format($enr->package_info['price'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @elseif(!empty($metadata['items']))
                            @foreach($metadata['items'] as $itemMeta)
                                <tr>
                                    <td style="text-align: center;" class="row-num">{{ $no++ }}</td>
                                    <td>
                                        <div class="item-course-title">{{ $itemMeta['course_title'] ?? 'Kursus SkillPath' }}</div>
                                        <div class="item-meta-tags">
                                            <span class="meta-tag student"><x-icon name="child" /> Siswa: <b>{{ $itemMeta['child_name'] ?? 'Anak' }}</b></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="schedule-detail-text">
                                            <div class="sched-day"><x-icon name="calendar" /> Hari {{ $itemMeta['schedule_day'] ?? '-' }}, {{ $itemMeta['schedule_time'] ?? '-' }}</div>
                                            <div class="loc-text"><x-icon name="location" /> {{ $itemMeta['location'] ?? 'SkillPath Center' }}</div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="package-pill">{{ $itemMeta['package_title'] ?? 'Paket Pilihan' }}</span>
                                        <small class="session-count-text">{{ $itemMeta['package_sessions'] ?? 12 }} Sesi Pertemuan</small>
                                    </td>
                                    <td style="text-align: right;" class="item-price-val">
                                        Rp{{ number_format($itemMeta['price'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td style="text-align: center;" class="row-num">{{ $no++ }}</td>
                                <td>
                                    <div class="item-course-title">{{ $course->title ?? $metadata['course_title'] ?? 'Kursus SkillPath' }}</div>
                                    <div class="item-meta-tags">
                                        <span class="meta-tag student"><x-icon name="child" /> Siswa: <b>{{ $child->name ?? $metadata['child_name'] ?? 'Anak' }}</b></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="schedule-detail-text">
                                        <div class="sched-day"><x-icon name="calendar" /> Hari {{ $schedule->day_name ?? $metadata['schedule_day'] ?? '-' }}, {{ $metadata['schedule_time'] ?? '-' }}</div>
                                        <div class="loc-text"><x-icon name="location" /> {{ $course->location_name ?? $metadata['location'] ?? 'SkillPath Center' }}</div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="package-pill">{{ $metadata['package_title'] ?? 'Paket Pilihan' }}</span>
                                    <small class="session-count-text">{{ $metadata['package_sessions'] ?? 12 }} Sesi Pertemuan</small>
                                </td>
                                <td style="text-align: right;" class="item-price-val">
                                    Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- 4. Calculations & Financial Summary --}}
            <div class="invoice-summary-container">
                <div class="invoice-terbilang-box">
                    <span class="terbilang-title">TERBILANG:</span>
                    <p class="terbilang-text"># {{ terbilang_rupiah($transaction->total) }} #</p>
                    <div class="payment-verification-badge">
                        <x-icon name="check" />
                        <div>
                            <strong>Pembayaran Terverifikasi Resmi</strong>
                            <small>ID Ref: REF-{{ strtoupper(substr(md5($transaction->invoice_code), 0, 10)) }}</small>
                        </div>
                    </div>
                </div>

                <div class="invoice-totals-table-box">
                    <table class="invoice-totals-table">
                        <tr>
                            <td class="tot-label">Subtotal Kursus</td>
                            <td class="tot-val">Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="tot-label">Biaya Layanan Platform</td>
                            <td class="tot-val">Rp{{ number_format($transaction->platform_fee, 0, ',', '.') }}</td>
                        </tr>
                        @if(!empty($metadata['discount']) && $metadata['discount'] > 0)
                            <tr class="discount-row">
                                <td class="tot-label">Diskon Promo ({{ $metadata['voucher_code'] ?? 'PROMO' }})</td>
                                <td class="tot-val">-Rp{{ number_format($metadata['discount'], 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td class="tot-label">TOTAL PEMBAYARAN</td>
                            <td class="tot-val">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- 5. Terms & Legal / Digital Stamp --}}
            <div class="invoice-footer-section">
                <div class="invoice-terms-notes">
                    <h4>Ketentuan & Informasi Resmi:</h4>
                    <ol>
                        <li>Faktur ini adalah bukti pembayaran yang sah dan diterbitkan secara komputerisasi resmi oleh sistem PT SkillPath Edukasi Nusantara.</li>
                        <li>Hak akses kelas, materi pembelajaran, dan reservasi jadwal mentor telah aktif secara otomatis untuk profil siswa tertera.</li>
                        <li>Simpan atau cetak faktur ini sebagai dokumen tanda terima pembayaran Anda.</li>
                    </ol>
                </div>

                <div class="invoice-signature-block">
                    <p class="sig-city-date">Jakarta, {{ $transaction->paid_at ? $transaction->paid_at->format('d F Y') : $transaction->updated_at->format('d F Y') }}</p>
                    <div class="digital-stamp-badge">
                        <div class="stamp-inner">
                            <span class="stamp-org">PT SKILLPATH EDUKASI NUSANTARA</span>
                            <span class="stamp-status">LUNAS / VERIFIED</span>
                            <span class="stamp-dept">FINANCE & BILLING</span>
                        </div>
                    </div>
                    <p class="sig-name">SkillPath Billing Dept.</p>
                    <small class="sig-sub">Dokumen Sah Komputerisasi</small>
                </div>
            </div>

            {{-- 6. Interactive Action Buttons (Screen Only) --}}
            <div class="invoice-bottom-actions no-print">
                <button class="btn btn-primary btn-lg" onclick="window.print()">
                    <x-icon name="printer" /> Cetak / Unduh PDF Invoice
                </button>
                <a class="btn btn-soft btn-lg" href="{{ route('parent.my-courses') }}">
                    <x-icon name="book" /> Masuk ke Kursus Saya
                </a>
                <a class="btn btn-soft btn-lg" href="{{ route('parent.schedule') }}">
                    <x-icon name="calendar" /> Cek Jadwal Kelas
                </a>
            </div>
        </div>

    @elseif($transaction->status === 'cancelled')
        {{-- CANCELLED VIEW --}}
        <div class="panel payment-cancelled-card">
            <div class="empty-state empty-state-full">
                <div class="empty-state-icon-wrap danger">
                    <x-icon name="recycle-bin" />
                </div>
                <h2>Pesanan Dibatalkan</h2>
                <p>Invoice <b>{{ $transaction->invoice_code }}</b> telah dibatalkan. Anda dapat memilih course baru dari katalog kursus kami.</p>
                <div class="empty-state-actions">
                    <a class="btn btn-primary" href="{{ route('explore.index') }}">
                        <x-icon name="search" /> Jelajahi Course
                    </a>
                    <a class="btn btn-soft" href="{{ route('parent.orders') }}">
                        <x-icon name="receipt" /> Riwayat Pesanan
                    </a>
                </div>
            </div>
        </div>

    @else
        {{-- PENDING PAYMENT GATEWAY VIEW --}}
        @php
            $rawMethod = $transaction->payment_method ?: 'bca_va';
            $bankCode = str_replace(['_va', '_transfer'], '', $rawMethod);
            if ($rawMethod === 'qris') $bankCode = 'qris';
            if ($rawMethod === 'bank_transfer') $bankCode = 'manual';

            $bankName = match($bankCode) {
                'bca' => 'BCA',
                'mandiri' => 'MANDIRI',
                'bri' => 'BRI',
                'bni' => 'BNI',
                'qris' => 'QRIS',
                default => 'BANK'
            };

            $methodTitle = match($rawMethod) {
                'qris' => 'QRIS Standar Nasional Indonesia',
                'bank_transfer' => 'Transfer Rekening Bank BCA',
                default => 'Virtual Account ' . $bankName
            };

            $vaNumber = $metadata['va_number'] ?? ('80777' . str_pad($transaction->parent_id, 4, '0', STR_PAD_LEFT) . rand(100000, 999999));
            $courseCount = isset($enrollments) && $enrollments->count() > 0 ? $enrollments->count() : (!empty($metadata['items']) ? count($metadata['items']) : 1);
        @endphp

        {{-- Full-width Payment Countdown & Status Bar --}}
        <div class="payment-countdown-banner">
            <div class="countdown-banner-left">
                <div class="invoice-pill-row">
                    <span class="status-chip pending"><x-icon name="clock" /> Menunggu Pembayaran (1x Bayar)</span>
                    <span class="invoice-number-chip"><x-icon name="receipt" /> {{ $transaction->invoice_code }}</span>
                    <span class="bundle-count-chip"><x-icon name="spark" /> {{ $courseCount }} Kursus Sekaligus</span>
                </div>
                <h2>Selesaikan Pembayaran Anda</h2>
                <p>Transfer sebelum batas waktu berakhir agar reservasi jadwal {{ $courseCount }} kelas anak Anda langsung aktif otomatis.</p>
            </div>
            @php
                $diffSec = max(0, $expiresAt->timestamp - now()->timestamp);
                $initH = str_pad((string)floor($diffSec / 3600), 2, '0', STR_PAD_LEFT);
                $initM = str_pad((string)floor(($diffSec % 3600) / 60), 2, '0', STR_PAD_LEFT);
                $initS = str_pad((string)($diffSec % 60), 2, '0', STR_PAD_LEFT);
            @endphp
            <div class="countdown-banner-right">
                <span class="countdown-caption"><x-icon name="clock" /> Batas Waktu Bayar</span>
                <div class="countdown-digits-box">
                    <div class="countdown-val" id="countdownTimer" data-expire="{{ $expiresAt->timestamp }}">
                        {{ $initH }}:{{ $initM }}:{{ $initS }}
                    </div>
                </div>
                <small class="countdown-deadline">{{ $expiresAt->format('d M Y, H:i') }} WIB</small>
            </div>
        </div>

        <div class="payment-grid">
            <div class="payment-main-col">
                {{-- Unified Payment Method Information Container --}}
                <div class="panel payment-box-card">
                    @if(str_contains($rawMethod, 'va'))
                        {{-- VIRTUAL ACCOUNT SECTION --}}
                        <div class="method-header-bar">
                            <div class="method-header-left">
                                <div class="method-brand-pill {{ $bankCode }}">
                                    {{ $bankName }}
                                </div>
                                <div class="method-header-info">
                                    <span class="panel-kicker" style="font-size: 10.5px; margin-bottom: 2px;">Metode Transfer</span>
                                    <h3>Nomor Virtual Account {{ $bankName }}</h3>
                                    <p>Verifikasi instan otomatis 24/7 • Bebas biaya admin transaksi</p>
                                </div>
                            </div>
                            <div class="verified-merchant-badge">
                                <x-icon name="shield-check" />
                                <span>Official Merchant</span>
                            </div>
                        </div>

                        <div class="payment-details-card">
                            <div class="detail-row highlight-box">
                                <div class="detail-label-group">
                                    <span class="detail-label"><x-icon name="bank" /> NOMOR VIRTUAL ACCOUNT {{ $bankName }}</span>
                                    <span class="detail-sub">Tujuan transfer dari ATM / Mobile Banking</span>
                                </div>
                                <div class="detail-value-group">
                                    <span class="detail-code-large" id="vaNumberDisplay">{{ $vaNumber }}</span>
                                    <button type="button" class="btn-copy-interactive" onclick="copyToClipboard('{{ $vaNumber }}', 'Nomor VA')">
                                        <x-icon name="copy" /> Salin No. VA
                                    </button>
                                </div>
                            </div>

                            <div class="detail-row highlight-box amount-box">
                                <div class="detail-label-group">
                                    <span class="detail-label"><x-icon name="wallet" /> TOTAL TAGIHAN (1X BAYAR)</span>
                                    <span class="detail-sub">Satu kali bayar untuk seluruh {{ $courseCount }} kursus</span>
                                </div>
                                <div class="detail-value-group">
                                    <span class="detail-amount-large">Rp{{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    <button type="button" class="btn-copy-interactive" onclick="copyToClipboard('{{ (int)$transaction->total }}', 'Total Pembayaran')">
                                        <x-icon name="copy" /> Salin Jumlah
                                    </button>
                                </div>
                            </div>

                            <div class="detail-row info-row">
                                <div class="info-cell">
                                    <small>Nama Penerima</small>
                                    <strong>PT SkillPath Edukasi Indonesia</strong>
                                </div>
                                <div class="info-cell">
                                    <small>Nama Orang Tua</small>
                                    <strong>{{ $transaction->parent->name ?? auth()->user()->name }}</strong>
                                </div>
                                <div class="info-cell">
                                    <small>Status Transaksi</small>
                                    <span class="badge-status-pending">Menunggu Transfer</span>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Action & Simulation --}}
                        <div class="payment-action-card">
                            <div class="action-card-text">
                                <div class="action-pulse-icon">
                                    <x-icon name="spark" />
                                </div>
                                <div>
                                    <h4>Sudah Melakukan Transfer VA?</h4>
                                    <p>Klik tombol konfirmasi di bawah untuk verifikasi pembayaran dan langsung aktifkan seluruh kelas anak.</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('parent.payment.pay', $transaction) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg btn-pay-now">
                                    <x-icon name="wallet" /> Konfirmasi / Bayar Sekarang (1x Bayar)
                                </button>
                            </form>
                        </div>

                        {{-- How to Pay Accordion Steps --}}
                        <div class="payment-guide-section">
                            <h4><x-icon name="info" /> Panduan Pembayaran Virtual Account {{ $bankName }}</h4>
                            <div class="guide-accordion-stack">
                                <details class="guide-accordion" open>
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="mobile" /> Mobile Banking (M-Banking {{ $bankName }})</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Buka aplikasi Mobile Banking bank Anda dan lakukan login.</li>
                                            <li>Pilih menu <b>Transfer / Pembayaran</b> lalu pilih <b>Virtual Account</b>.</li>
                                            <li>Masukkan Nomor VA: <code class="code-badge">{{ $vaNumber }}</code>.</li>
                                            <li>Pastikan detail tagihan muncul sebagai <b>SkillPath - {{ $transaction->parent->name ?? 'Orang Tua' }}</b> dengan total <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                            <li>Masukkan PIN m-Banking Anda untuk menyelesaikan pembayaran.</li>
                                        </ol>
                                    </div>
                                </details>

                                <details class="guide-accordion">
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="bank" /> ATM Bank</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Masukkan kartu ATM dan 6 digit PIN kartu Anda.</li>
                                            <li>Pilih menu <b>Transaksi Lainnya &gt; Transfer &gt; ke Rekening Virtual Account</b>.</li>
                                            <li>Ketik nomor VA <code class="code-badge">{{ $vaNumber }}</code> lalu tekan <b>Benar</b>.</li>
                                            <li>Periksa rincian pembayaran di layar monitor lalu konfirmasi <b>Ya / Bayar</b>.</li>
                                            <li>Ambil struk transaksi ATM sebagai bukti bayar sah.</li>
                                        </ol>
                                    </div>
                                </details>

                                <details class="guide-accordion">
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="globe" /> Internet Banking</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Login ke portal Internet Banking bank pilihan Anda.</li>
                                            <li>Navigasi ke menu <b>Bayar Tagihan &gt; Virtual Account</b>.</li>
                                            <li>Input kode Virtual Account <code class="code-badge">{{ $vaNumber }}</code> dan verifikasi dengan token autentikasi Anda.</li>
                                        </ol>
                                    </div>
                                </details>
                            </div>
                        </div>

                    @elseif($rawMethod === 'qris')
                        {{-- QRIS CODE SECTION --}}
                        <div class="method-header-bar">
                            <div class="method-header-left">
                                <div class="method-brand-pill qris">QRIS</div>
                                <div class="method-header-info">
                                    <span class="panel-kicker" style="font-size: 10.5px; margin-bottom: 2px;">Metode Transfer</span>
                                    <h3>Scan QRIS Standar Nasional</h3>
                                    <p>Dapat di-scan melalui GoPay, OVO, DANA, ShopeePay, BCA Mobile, Livin Mandiri, dll</p>
                                </div>
                            </div>
                            <div class="verified-merchant-badge">
                                <x-icon name="shield-check" />
                                <span>Official Merchant</span>
                            </div>
                        </div>

                        <div class="qris-card-center">
                            <span class="qris-badge-tag">QRIS</span>
                            <div class="qris-frame-box">
                                <svg class="qris-svg" viewBox="0 0 200 200" width="200" height="200">
                                    <rect width="200" height="200" fill="#ffffff"/>
                                    <!-- Corner Squares -->
                                    <rect x="15" y="15" width="45" height="45" fill="#0f172a" rx="4"/>
                                    <rect x="23" y="23" width="29" height="29" fill="#ffffff" rx="2"/>
                                    <rect x="29" y="29" width="17" height="17" fill="#6366f1" rx="2"/>

                                    <rect x="140" y="15" width="45" height="45" fill="#0f172a" rx="4"/>
                                    <rect x="148" y="23" width="29" height="29" fill="#ffffff" rx="2"/>
                                    <rect x="154" y="29" width="17" height="17" fill="#6366f1" rx="2"/>

                                    <rect x="15" y="140" width="45" height="45" fill="#0f172a" rx="4"/>
                                    <rect x="23" y="148" width="29" height="29" fill="#ffffff" rx="2"/>
                                    <rect x="29" y="154" width="17" height="17" fill="#6366f1" rx="2"/>

                                    <!-- Inner QR Matrix Patterns -->
                                    <rect x="70" y="20" width="10" height="10" fill="#0f172a"/>
                                    <rect x="85" y="20" width="15" height="10" fill="#0f172a"/>
                                    <rect x="110" y="20" width="10" height="10" fill="#0f172a"/>
                                    <rect x="70" y="35" width="25" height="10" fill="#0f172a"/>
                                    <rect x="105" y="35" width="15" height="10" fill="#0f172a"/>
                                    <rect x="70" y="50" width="10" height="25" fill="#0f172a"/>
                                    <rect x="90" y="50" width="20" height="10" fill="#0f172a"/>
                                    <rect x="120" y="50" width="10" height="10" fill="#0f172a"/>

                                    <rect x="20" y="70" width="15" height="10" fill="#0f172a"/>
                                    <rect x="45" y="70" width="10" height="20" fill="#0f172a"/>
                                    <rect x="70" y="80" width="60" height="40" fill="#0f172a" rx="6"/>
                                    <text x="100" y="105" fill="#ffffff" font-size="11" font-weight="bold" font-family="sans-serif" text-anchor="middle">SKILLPATH</text>
                                    
                                    <rect x="140" y="70" width="20" height="10" fill="#0f172a"/>
                                    <rect x="170" y="70" width="15" height="20" fill="#0f172a"/>
                                    <rect x="140" y="90" width="15" height="15" fill="#0f172a"/>
                                    <rect x="165" y="100" width="20" height="10" fill="#0f172a"/>

                                    <rect x="70" y="130" width="20" height="10" fill="#0f172a"/>
                                    <rect x="100" y="130" width="10" height="20" fill="#0f172a"/>
                                    <rect x="120" y="130" width="20" height="10" fill="#0f172a"/>
                                    <rect x="70" y="150" width="10" height="35" fill="#0f172a"/>
                                    <rect x="90" y="160" width="20" height="10" fill="#0f172a"/>
                                    <rect x="120" y="150" width="10" height="20" fill="#0f172a"/>
                                    <rect x="140" y="145" width="45" height="10" fill="#0f172a"/>
                                    <rect x="140" y="165" width="20" height="20" fill="#0f172a"/>
                                    <rect x="170" y="165" width="15" height="20" fill="#0f172a"/>
                                </svg>
                            </div>
                            <div class="qris-supported-wallets">
                                <span>Mendukung Pembayaran Seluruh E-Wallet & Mobile Banking:</span>
                                <div class="wallet-pills-row">
                                    <span class="w-pill">GoPay</span>
                                    <span class="w-pill">OVO</span>
                                    <span class="w-pill">DANA</span>
                                    <span class="w-pill">ShopeePay</span>
                                    <span class="w-pill">LinkAja</span>
                                    <span class="w-pill">BCA Mobile</span>
                                    <span class="w-pill">Livin Mandiri</span>
                                    <span class="w-pill">BRImo</span>
                                </div>
                            </div>
                        </div>

                        {{-- QRIS How to Pay Accordion Steps --}}
                        <div class="payment-guide-section">
                            <h4><x-icon name="info" /> Panduan Pembayaran Scan QRIS</h4>
                            <div class="guide-accordion-stack">
                                <details class="guide-accordion" open>
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="mobile" /> Melalui Aplikasi E-Wallet (GoPay, OVO, DANA, ShopeePay, dll)</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Buka aplikasi e-wallet pilihan Anda (GoPay / OVO / DANA / ShopeePay / LinkAja).</li>
                                            <li>Pilih menu <b>Bayar / Scan QR</b> pada layar utama aplikasi.</li>
                                            <li>Arahkan kamera smartphone Anda ke kode QRIS di atas.</li>
                                            <li>Periksa nama penerima <b>SKILLPATH EDUKASI INDONESIA</b> dan total tagihan <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                            <li>Konfirmasi pembayaran dan masukkan PIN/keamanan akun e-wallet Anda.</li>
                                        </ol>
                                    </div>
                                </details>

                                <details class="guide-accordion">
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="bank" /> Melalui Mobile Banking (BCA Mobile, Livin, BRImo, dll)</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Buka aplikasi mobile banking bank Anda dan login.</li>
                                            <li>Pilih fitur <b>QRIS / Scan QR</b> di halaman utama m-banking.</li>
                                            <li>Scan kode QR di atas dan pastikan nominal sesuai: <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                            <li>Masukkan PIN m-banking Anda untuk menyelesaikan pembayaran.</li>
                                        </ol>
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="payment-action-card">
                            <div class="action-card-text">
                                <div class="action-pulse-icon">
                                    <x-icon name="qr" />
                                </div>
                                <div>
                                    <h4>Sudah Berhasil Melakukan Scan QRIS?</h4>
                                    <p>Klik tombol konfirmasi untuk verifikasi dan langsung aktifkan seluruh kelas anak.</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('parent.payment.pay', $transaction) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg btn-pay-now">
                                    <x-icon name="check" /> Saya Sudah Scan & Bayar (1x Bayar)
                                </button>
                            </form>
                        </div>

                    @else
                        {{-- MANUAL TRANSFER / DEFAULT VIEW --}}
                        <div class="method-header-bar">
                            <div class="method-header-left">
                                <div class="method-brand-pill manual">BCA</div>
                                <div class="method-header-info">
                                    <span class="panel-kicker" style="font-size: 10.5px; margin-bottom: 2px;">Metode Transfer</span>
                                    <h3>Rekening Resmi Operasional SkillPath</h3>
                                    <p>Transfer manual ke rekening resmi PT SkillPath Edukasi Indonesia</p>
                                </div>
                            </div>
                            <div class="verified-merchant-badge">
                                <x-icon name="shield-check" />
                                <span>Official Merchant</span>
                            </div>
                        </div>

                        <div class="payment-details-card">
                            <div class="detail-row highlight-box">
                                <div class="detail-label-group">
                                    <span class="detail-label"><x-icon name="bank" /> NOMOR REKENING BANK BCA</span>
                                    <span class="detail-sub">BCA KCP Sudirman Jakarta</span>
                                </div>
                                <div class="detail-value-group">
                                    <span class="detail-code-large">541-098-7721</span>
                                    <button type="button" class="btn-copy-interactive" onclick="copyToClipboard('5410987721', 'Nomor Rekening BCA')">
                                        <x-icon name="copy" /> Salin No. Rekening
                                    </button>
                                </div>
                            </div>

                            <div class="detail-row highlight-box amount-box">
                                <div class="detail-label-group">
                                    <span class="detail-label"><x-icon name="wallet" /> TOTAL TAGIHAN (1X BAYAR)</span>
                                    <span class="detail-sub">Satu kali bayar untuk seluruh {{ $courseCount }} kursus</span>
                                </div>
                                <div class="detail-value-group">
                                    <span class="detail-amount-large">Rp{{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    <button type="button" class="btn-copy-interactive" onclick="copyToClipboard('{{ (int)$transaction->total }}', 'Total Pembayaran')">
                                        <x-icon name="copy" /> Salin Jumlah
                                    </button>
                                </div>
                            </div>

                            <div class="detail-row info-row">
                                <div class="info-cell">
                                    <small>Nama Penerima</small>
                                    <strong>PT SkillPath Edukasi Indonesia</strong>
                                </div>
                                <div class="info-cell">
                                    <small>Nama Orang Tua</small>
                                    <strong>{{ $transaction->parent->name ?? auth()->user()->name }}</strong>
                                </div>
                                <div class="info-cell">
                                    <small>Status Transaksi</small>
                                    <span class="badge-status-pending">Menunggu Transfer</span>
                                </div>
                            </div>
                        </div>

                        <div class="payment-action-card">
                            <div class="action-card-text">
                                <div class="action-pulse-icon">
                                    <x-icon name="check" />
                                </div>
                                <div>
                                    <h4>Konfirmasi Transfer Bank</h4>
                                    <p>Klik tombol konfirmasi untuk verifikasi transaksi dan aktifkan kelas secara instan.</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('parent.payment.pay', $transaction) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg btn-pay-now">
                                    <x-icon name="check" /> Konfirmasi Pembayaran (1x Bayar)
                                </button>
                            </form>
                        </div>

                        {{-- How to Pay Accordion Steps for Manual Transfer --}}
                        <div class="payment-guide-section">
                            <h4><x-icon name="info" /> Panduan Pembayaran Transfer Bank BCA</h4>
                            <div class="guide-accordion-stack">
                                <details class="guide-accordion" open>
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="mobile" /> Melalui BCA Mobile (m-BCA)</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Buka aplikasi BCA mobile dan pilih menu <b>m-BCA</b> lalu masukkan kode akses Anda.</li>
                                            <li>Pilih menu <b>m-Transfer &gt; Antar Rekening</b>.</li>
                                            <li>Masukkan nomor rekening BCA: <code class="code-badge">541-098-7721</code> (a/n PT SkillPath Edukasi Indonesia).</li>
                                            <li>Masukkan nominal transfer tepat sebesar: <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                            <li>Periksa rincian transfer dan masukkan 6 digit PIN m-BCA Anda.</li>
                                        </ol>
                                    </div>
                                </details>

                                <details class="guide-accordion">
                                    <summary>
                                        <span class="guide-sum-title"><x-icon name="bank" /> Melalui ATM BCA</span>
                                        <span class="guide-chevron"><x-icon name="arrow-right" /></span>
                                    </summary>
                                    <div class="guide-body">
                                        <ol>
                                            <li>Masukkan kartu ATM BCA dan 6 digit PIN Anda.</li>
                                            <li>Pilih menu <b>Transaksi Lainnya &gt; Transfer &gt; ke Rek BCA</b>.</li>
                                            <li>Ketik nomor rekening <code class="code-badge">541-098-7721</code> dan masukkan jumlah <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                            <li>Periksa nama penerima <b>PT SKILLPATH EDUKASI INDONESIA</b> lalu tekan <b>Ya / Benar</b>.</li>
                                        </ol>
                                    </div>
                                </details>
                            </div>
                        </div>
                    @endif

                    {{-- Cancel Transaction Form --}}
                    <div class="payment-cancel-bar">
                        <form method="POST" action="{{ route('parent.payment.cancel', $transaction) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            @csrf
                            <button type="submit" class="btn btn-ghost text-danger btn-sm">
                                <x-icon name="recycle-bin" /> Batalkan Pesanan Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <aside class="panel payment-summary-sidebar">
                <div class="summary-header">
                    <div>
                        <span class="panel-kicker" style="margin-bottom: 2px;">Ringkasan Biaya</span>
                        <h3 style="margin: 0;"><x-icon name="receipt" /> Total Tagihan</h3>
                    </div>
                    <span class="order-bundle-pill"><x-icon name="spark" /> {{ $courseCount }} Kursus (1x Bayar)</span>
                </div>

                {{-- Selected Payment Method Mini Banner --}}
                <div class="summary-method-banner">
                    <div class="method-mini-pill {{ $bankCode }}">{{ $bankName }}</div>
                    <div class="method-mini-info">
                        <strong>{{ $methodTitle }}</strong>
                        <span>Verifikasi otomatis 24/7 tanpa antre</span>
                    </div>
                </div>

                <div class="payment-courses-stack">
                    @if(isset($enrollments) && $enrollments->count() > 0)
                        @foreach($enrollments as $enr)
                            <div class="summary-course-card">
                                <div class="summary-course-thumb">
                                    <x-course-art :course="$enr->course ?? null" />
                                </div>
                                <div class="summary-course-body">
                                    <div class="summary-course-title-row">
                                        <h4 title="{{ $enr->course->title }}">{{ $enr->course->title }}</h4>
                                        <span class="course-item-price">Rp{{ number_format($enr->package_info['price'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="summary-course-chips">
                                        <span class="summary-chip child"><x-icon name="child" /> {{ $enr->child->name ?? '-' }}</span>
                                        <span class="summary-chip package"><x-icon name="spark" /> {{ $enr->package_info['title'] ?? 'Paket' }} ({{ $enr->package_info['sessions'] ?? $enr->total_sessions }} Sesi)</span>
                                        <span class="summary-chip schedule"><x-icon name="calendar" /> {{ $enr->schedule->day_name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif(!empty($metadata['items']))
                        @foreach($metadata['items'] as $itemMeta)
                            <div class="summary-course-card">
                                <div class="summary-course-thumb">
                                    <div class="course-art">
                                        <div class="art-icon"><x-icon name="book" /></div>
                                    </div>
                                </div>
                                <div class="summary-course-body">
                                    <div class="summary-course-title-row">
                                        <h4 title="{{ $itemMeta['course_title'] ?? 'Kursus' }}">{{ $itemMeta['course_title'] ?? 'Kursus' }}</h4>
                                        <span class="course-item-price">Rp{{ number_format($itemMeta['price'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="summary-course-chips">
                                        <span class="summary-chip child"><x-icon name="child" /> {{ $itemMeta['child_name'] ?? '-' }}</span>
                                        <span class="summary-chip package"><x-icon name="spark" /> {{ $itemMeta['package_title'] ?? 'Paket' }} ({{ $itemMeta['package_sessions'] ?? 12 }} Sesi)</span>
                                        <span class="summary-chip schedule"><x-icon name="calendar" /> {{ $itemMeta['schedule_day'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="summary-course-card">
                            <div class="summary-course-thumb">
                                <x-course-art :course="$course ?? null" />
                            </div>
                            <div class="summary-course-body">
                                <div class="summary-course-title-row">
                                    <h4 title="{{ $course->title ?? $metadata['course_title'] ?? 'Course' }}">{{ $course->title ?? $metadata['course_title'] ?? 'Course' }}</h4>
                                    <span class="course-item-price">Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="summary-course-chips">
                                    <span class="summary-chip child"><x-icon name="child" /> {{ $child->name ?? $metadata['child_name'] ?? 'Anak' }}</span>
                                    <span class="summary-chip package"><x-icon name="spark" /> {{ $course->category->name ?? 'Kategori' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="summary-calc-box">
                    <div class="summary-line">
                        <span>Subtotal Kursus ({{ $courseCount }} Kursus)</span>
                        <strong>Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Layanan Platform</span>
                        <strong>Rp{{ number_format($transaction->platform_fee, 0, ',', '.') }}</strong>
                    </div>
                    @if(!empty($metadata['discount']) && $metadata['discount'] > 0)
                        <div class="summary-line discount-line">
                            <span>Diskon Voucher</span>
                            <strong>-Rp{{ number_format($metadata['discount'], 0, ',', '.') }}</strong>
                        </div>
                    @endif
                    <div class="summary-divider"></div>
                    <div class="summary-total-row">
                        <div>
                            <span class="total-label">Total 1x Bayar</span>
                            <small class="total-sub">Termasuk seluruh kursus & PPN</small>
                        </div>
                        <strong class="total-price-large">Rp{{ number_format($transaction->total, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="payment-trust-badge">
                    <x-icon name="shield-check" />
                    <div>
                        <strong>Pembayaran Terproteksi & Terenkripsi SSL</strong>
                        <span>Verifikasi instan • Akses langsung terbuka</span>
                    </div>
                </div>

                <div class="payment-help-box">
                    <x-icon name="spark" />
                    <div>
                        <strong>Butuh bantuan pembayaran?</strong>
                        <div>CS kami siap membantu Anda melalui WhatsApp Support</div>
                    </div>
                </div>
            </aside>
        </div>
    @endif
</section>

{{-- Interactive Toast & Countdown Script --}}
<script>
function copyToClipboard(text, label) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast(label + ' berhasil disalin!');
        }).catch(() => {
            fallbackCopy(text, label);
        });
    } else {
        fallbackCopy(text, label);
    }
}

function fallbackCopy(text, label) {
    const input = document.createElement('textarea');
    input.value = text;
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.focus();
    input.select();
    try {
        document.execCommand('copy');
        showToast(label + ' berhasil disalin!');
    } catch (err) {
        showToast('Gagal menyalin otomatis. Silakan salin manual.');
    }
    document.body.removeChild(input);
}

function showToast(msg) {
    let toast = document.getElementById('paymentToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'paymentToast';
        toast.className = 'payment-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Live Real-Time Countdown Timer
document.addEventListener('DOMContentLoaded', () => {
    const timerEl = document.getElementById('countdownTimer');
    if (timerEl) {
        const expireTs = parseInt(timerEl.getAttribute('data-expire'), 10) * 1000;
        function updateTimer() {
            const now = Date.now();
            const distance = expireTs - now;
            if (distance <= 0) {
                timerEl.textContent = '00:00:00';
                timerEl.style.color = '#f87171';
                return;
            }
            const totalSeconds = Math.floor(distance / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            timerEl.textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    }
});
</script>
@endsection
