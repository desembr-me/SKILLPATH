@extends('layouts.app')
@section('title', $transaction->status === 'paid' ? 'Bukti Pembayaran - ' . $transaction->invoice_code : 'Pembayaran - ' . $transaction->invoice_code)

@section('content')
<section class="dashboard-page payment-page-wrap">
    <div class="dash-title no-print">
        <div>
            <span class="eyebrow">Payment Gateway</span>
            <h1>{{ $transaction->status === 'paid' ? 'Bukti Pembayaran & Invoice' : 'Selesaikan Pembayaran' }}</h1>
            <p>{{ $transaction->status === 'paid' ? 'Pembayaran telah diverifikasi. Kelas anak Anda siap dimulai.' : 'Selesaikan transaksi Anda sebelum batas waktu berakhir.' }}</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('parent.orders') }}">
                <x-icon name="arrow-left" /> Riwayat Pesanan
            </a>
            @if($transaction->status === 'paid')
                <button class="btn btn-ghost" onclick="window.print()">
                    <x-icon name="printer" /> Cetak Invoice
                </button>
            @endif
        </div>
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

    @if($transaction->status === 'paid')
        {{-- PAID INVOICE / RECEIPT VIEW --}}
        <div class="invoice-receipt-card print-target">
            <div class="receipt-header">
                <div class="receipt-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="SkillPath" class="brand-logo" onerror="this.style.display='none'">
                    <div>
                        <h2>PT SkillPath Edukasi Indonesia</h2>
                        <p>Platform Kursus & Pengembangan Bakat Anak Terpercaya</p>
                    </div>
                </div>
                <div class="receipt-status-badge paid">
                    <x-icon name="check" /> LUNAS / PAID
                </div>
            </div>

            <hr class="receipt-divider">

            <div class="receipt-meta-grid">
                <div class="receipt-meta-item">
                    <small>No. Invoice</small>
                    <strong>{{ $transaction->invoice_code }}</strong>
                </div>
                <div class="receipt-meta-item">
                    <small>Tanggal Pembayaran</small>
                    <strong>{{ $transaction->paid_at ? $transaction->paid_at->format('d M Y, H:i') . ' WIB' : $transaction->updated_at->format('d M Y, H:i') . ' WIB' }}</strong>
                </div>
                <div class="receipt-meta-item">
                    <small>Metode Pembayaran</small>
                    <strong>{{ strtoupper(str_replace('_', ' ', $transaction->payment_method ?: 'Virtual Account')) }}</strong>
                </div>
                <div class="receipt-meta-item">
                    <small>Nama Orang Tua</small>
                    <strong>{{ $transaction->parent->name ?? auth()->user()->name }}</strong>
                </div>
            </div>

            <div class="receipt-item-box">
                <div class="receipt-item-head">
                    <span>Rincian Course & Kelas Anak</span>
                    <span>Biaya</span>
                </div>
                <div class="receipt-item-row">
                    <div class="receipt-course-detail">
                        <h4>{{ $course->title ?? $metadata['course_title'] ?? 'Course SkillPath' }}</h4>
                        <div class="receipt-tags">
                            <span class="badge-tag"><x-icon name="child" /> Siswa: <b>{{ $child->name ?? $metadata['child_name'] ?? 'Anak' }}</b></span>
                            <span class="badge-tag"><x-icon name="calendar" /> Jadwal: <b>Hari {{ $schedule->day_of_week ?? $metadata['schedule_day'] ?? '-' }}, {{ $metadata['schedule_time'] ?? '-' }}</b></span>
                            <span class="badge-tag"><x-icon name="location" /> Lokasi: <b>{{ $course->location_name ?? $metadata['location'] ?? 'Offline Hub' }}</b></span>
                        </div>
                    </div>
                    <div class="receipt-item-price">
                        Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}
                    </div>
                </div>
                <div class="receipt-calc-row">
                    <span>Biaya Layanan Platform</span>
                    <span>Rp{{ number_format($transaction->platform_fee, 0, ',', '.') }}</span>
                </div>
                @if(!empty($metadata['discount']) && $metadata['discount'] > 0)
                <div class="receipt-calc-row discount-row">
                    <span>Diskon Promo ({{ $metadata['voucher_code'] ?? 'PROMO' }})</span>
                    <span>-Rp{{ number_format($metadata['discount'], 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="receipt-total-row">
                    <span>Total Pembayaran</span>
                    <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>
                </div>
            </div>

            <div class="receipt-footer-notes">
                <p><b>Catatan Pembayaran:</b> Bukti pembayaran ini adalah dokumen resmi yang sah diterbitkan oleh SkillPath. Akses belajar, jadwal kalender, dan materi kursus sudah langsung aktif untuk akun siswa.</p>
            </div>

            <div class="receipt-action-buttons no-print">
                <a class="btn btn-primary btn-lg" href="{{ route('parent.my-courses') }}">
                    <x-icon name="book" /> Mulai Belajar di Course Saya
                </a>
                <a class="btn btn-soft btn-lg" href="{{ route('parent.schedule') }}">
                    <x-icon name="calendar" /> Cek Jadwal Kelas di Kalender
                </a>
            </div>
        </div>

    @elseif($transaction->status === 'cancelled')
        {{-- CANCELLED VIEW --}}
        <div class="panel payment-cancelled-card">
            <div class="empty-state">
                <x-icon name="recycle-bin" />
                <h2>Pesanan Dibatalkan</h2>
                <p>Invoice <b>{{ $transaction->invoice_code }}</b> telah dibatalkan. Anda dapat memilih course baru dari katalog.</p>
                <div style="margin-top: 18px; display:flex; gap:10px; justify-content:center;">
                    <a class="btn btn-primary" href="{{ route('explore.index') }}">Jelajahi Course</a>
                    <a class="btn btn-soft" href="{{ route('parent.orders') }}">Riwayat Pesanan</a>
                </div>
            </div>
        </div>

    @else
        {{-- PENDING PAYMENT GATEWAY VIEW --}}
        <div class="payment-grid">
            <div class="payment-main-col">
                {{-- Payment Countdown & Status Bar --}}
                <div class="panel payment-countdown-panel">
                    <div class="countdown-copy">
                        <span class="status-chip pending"><x-icon name="clock" /> Menunggu Pembayaran</span>
                        <h3>Batas Waktu Pembayaran</h3>
                        <p>Segera selesaikan pembayaran sebelum batas waktu berakhir agar kuota jadwal anak Anda tidak terlepas.</p>
                    </div>
                    <div class="countdown-timer-box">
                        <span class="timer-label">Sisa Waktu</span>
                        <div class="timer-digits" id="countdownTimer" data-expire="{{ $expiresAt->timestamp }}">
                            23:59:59
                        </div>
                        <small>Jatuh Tempo: {{ $expiresAt->format('d M Y, H:i') }} WIB</small>
                    </div>
                </div>

                {{-- Payment Method Specific Instructions --}}
                @php
                    $method = $transaction->payment_method ?: 'bca_va';
                    $vaNumber = $metadata['va_number'] ?? ('80777' . str_pad((string)auth()->id(), 4, '0', STR_PAD_LEFT) . '889911');
                    $methodName = match($method) {
                        'mandiri_va' => 'Mandiri Virtual Account',
                        'bri_va' => 'BRI Virtual Account (BRIVA)',
                        'bni_va' => 'BNI Virtual Account',
                        'qris' => 'QRIS (Semua Pembayaran QR)',
                        'bank_transfer' => 'Transfer Bank Manual BCA',
                        'instant_demo' => 'Simulasi Pembayaran Instan',
                        default => 'BCA Virtual Account',
                    };
                @endphp

                <div class="panel payment-instruction-panel">
                    <div class="payment-instruction-header">
                        <div class="method-badge-lead">
                            <span class="method-icon-wrap"><x-icon name="{{ $method === 'qris' ? 'qr' : ($method === 'bank_transfer' ? 'wallet' : 'bank') }}" /></span>
                            <div>
                                <span class="eyebrow">Metode Terpilih</span>
                                <h2>{{ $methodName }}</h2>
                            </div>
                        </div>
                    </div>

                    @if(str_contains($method, 'va') || $method === 'instant_demo')
                        {{-- VIRTUAL ACCOUNT BOX --}}
                        <div class="va-copy-box">
                            <div class="va-copy-item">
                                <span class="va-label">Nomor Virtual Account</span>
                                <div class="va-value-row">
                                    <strong class="va-code" id="vaCodeText">{{ $vaNumber }}</strong>
                                    <button class="btn btn-soft btn-sm copy-btn" onclick="copyToClipboard('{{ $vaNumber }}', 'Nomor Virtual Account')">
                                        <x-icon name="copy" /> Salin No. VA
                                    </button>
                                </div>
                            </div>

                            <div class="va-copy-item">
                                <span class="va-label">Total Tagihan Pembayaran</span>
                                <div class="va-value-row">
                                    <strong class="va-price">Rp{{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                    <button class="btn btn-soft btn-sm copy-btn" onclick="copyToClipboard('{{ (int)$transaction->total }}', 'Total Pembayaran')">
                                        <x-icon name="copy" /> Salin Jumlah
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Guides --}}
                        <div class="payment-steps-accordion">
                            <h3>Panduan Pembayaran Virtual Account</h3>

                            <details class="guide-item" open>
                                <summary><b>1. Pembayaran via Mobile Banking (m-Banking)</b> <x-icon name="arrow-right" /></summary>
                                <div class="guide-content">
                                    <ol>
                                        <li>Buka aplikasi Mobile Banking di ponsel Anda dan login.</li>
                                        <li>Pilih menu <b>Transfer</b> &rarr; pilih <b>Virtual Account</b>.</li>
                                        <li>Masukkan nomor Virtual Account: <code>{{ $vaNumber }}</code>.</li>
                                        <li>Periksa detail pembayaran: Penerima <b>PT SkillPath ({{ $child->name ?? 'Anak' }})</b> dengan nominal <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>.</li>
                                        <li>Masukkan PIN m-Banking Anda untuk menyelesaikan transaksi.</li>
                                    </ol>
                                </div>
                            </details>

                            <details class="guide-item">
                                <summary><b>2. Pembayaran via Mesin ATM</b> <x-icon name="arrow-right" /></summary>
                                <div class="guide-content">
                                    <ol>
                                        <li>Masukkan Kartu ATM dan PIN Anda di mesin ATM.</li>
                                        <li>Pilih menu <b>Transaksi Lainnya</b> &rarr; <b>Transfer</b> &rarr; <b>Ke Rekening Virtual Account</b>.</li>
                                        <li>Masukkan nomor Virtual Account: <code>{{ $vaNumber }}</code>.</li>
                                        <li>Pastikan rincian tagihan sudah sesuai, lalu pilih <b>Ya / Benar</b>.</li>
                                        <li>Simpan struk transaksi sebagai bukti pembayaran.</li>
                                    </ol>
                                </div>
                            </details>
                        </div>

                    @elseif($method === 'qris')
                        {{-- QRIS CODE DISPLAY --}}
                        <div class="qris-display-box">
                            <div class="qris-header">
                                <span class="qris-logo">QRIS</span>
                                <small>Standar Pembayaran Nasional QR</small>
                            </div>
                            <div class="qris-frame">
                                <div class="qris-qr-art">
                                    <svg viewBox="0 0 160 160" width="160" height="160" fill="#20283f">
                                        <rect width="160" height="160" fill="#ffffff"/>
                                        <!-- QR Corner top-left -->
                                        <rect x="12" y="12" width="36" height="36" fill="#20283f" rx="4"/>
                                        <rect x="18" y="18" width="24" height="24" fill="#ffffff" rx="2"/>
                                        <rect x="24" y="24" width="12" height="12" fill="#20283f" rx="1"/>
                                        <!-- QR Corner top-right -->
                                        <rect x="112" y="12" width="36" height="36" fill="#20283f" rx="4"/>
                                        <rect x="118" y="18" width="24" height="24" fill="#ffffff" rx="2"/>
                                        <rect x="124" y="24" width="12" height="12" fill="#20283f" rx="1"/>
                                        <!-- QR Corner bottom-left -->
                                        <rect x="12" y="112" width="36" height="36" fill="#20283f" rx="4"/>
                                        <rect x="18" y="118" width="24" height="24" fill="#ffffff" rx="2"/>
                                        <rect x="24" y="124" width="12" height="12" fill="#20283f" rx="1"/>
                                        <!-- QR Matrix Pattern Data -->
                                        <rect x="56" y="12" width="8" height="8" rx="1"/>
                                        <rect x="72" y="12" width="16" height="8" rx="1"/>
                                        <rect x="96" y="12" width="8" height="8" rx="1"/>
                                        <rect x="56" y="28" width="16" height="8" rx="1"/>
                                        <rect x="80" y="28" width="8" height="8" rx="1"/>
                                        <rect x="96" y="28" width="8" height="8" rx="1"/>
                                        <rect x="12" y="56" width="8" height="16" rx="1"/>
                                        <rect x="28" y="56" width="8" height="8" rx="1"/>
                                        <rect x="44" y="56" width="20" height="8" rx="1"/>
                                        <rect x="72" y="44" width="16" height="20" rx="1"/>
                                        <rect x="96" y="56" width="8" height="16" rx="1"/>
                                        <rect x="112" y="56" width="16" height="8" rx="1"/>
                                        <rect x="136" y="56" width="12" height="8" rx="1"/>
                                        <!-- Center logo box -->
                                        <circle cx="80" cy="80" r="14" fill="#6857df"/>
                                        <path d="M76 76h8v8h-8z" fill="#ffffff"/>
                                        <!-- Bottom patterns -->
                                        <rect x="56" y="88" width="8" height="24" rx="1"/>
                                        <rect x="72" y="96" width="16" height="8" rx="1"/>
                                        <rect x="96" y="88" width="8" height="16" rx="1"/>
                                        <rect x="112" y="80" width="8" height="16" rx="1"/>
                                        <rect x="128" y="88" width="20" height="8" rx="1"/>
                                        <rect x="72" y="120" width="24" height="8" rx="1"/>
                                        <rect x="56" y="136" width="16" height="12" rx="1"/>
                                        <rect x="88" y="136" width="16" height="12" rx="1"/>
                                        <rect x="112" y="120" width="8" height="28" rx="1"/>
                                        <rect x="128" y="136" width="20" height="12" rx="1"/>
                                    </svg>
                                </div>
                                <div class="qris-amount">
                                    <small>Total Tagihan</small>
                                    <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>
                                </div>
                            </div>
                            <p class="qris-apps-note">Mendukung: GoPay • OVO • DANA • ShopeePay • LinkAja • BCA Mobile • BRImo • Livin by Mandiri</p>
                        </div>

                    @else
                        {{-- MANUAL BANK TRANSFER --}}
                        <div class="va-copy-box">
                            <div class="va-copy-item">
                                <span class="va-label">Bank Penerima</span>
                                <div class="va-value-row">
                                    <strong>Bank BCA (PT SkillPath Edukasi Indonesia)</strong>
                                </div>
                            </div>
                            <div class="va-copy-item">
                                <span class="va-label">Nomor Rekening Resmi</span>
                                <div class="va-value-row">
                                    <strong class="va-code">8801239999</strong>
                                    <button class="btn btn-soft btn-sm copy-btn" onclick="copyToClipboard('8801239999', 'Nomor Rekening')">
                                        <x-icon name="copy" /> Salin No. Rekening
                                    </button>
                                </div>
                            </div>
                            <div class="va-copy-item">
                                <span class="va-label">Total Transfer Tepat</span>
                                <div class="va-value-row">
                                    <strong class="va-price">Rp{{ number_format($transaction->total, 0, ',', '.') }}</strong>
                                    <button class="btn btn-soft btn-sm copy-btn" onclick="copyToClipboard('{{ (int)$transaction->total }}', 'Jumlah Transfer')">
                                        <x-icon name="copy" /> Salin Jumlah
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Action / Simulation Box --}}
                <div class="panel payment-simulation-panel">
                    <div class="sim-header">
                        <x-icon name="shield-check" />
                        <div>
                            <h3>Simulasi & Konfirmasi Pembayaran</h3>
                            <p>Untuk kemudahan pengujian, tekan tombol di bawah untuk memverifikasi pembayaran secara instan.</p>
                        </div>
                    </div>

                    <div class="sim-actions">
                        <form method="POST" action="{{ route('parent.transactions.pay', $transaction) }}" class="sim-form">
                            @csrf
                            <input type="hidden" name="payment_method" value="{{ $method }}">
                            <button type="submit" class="btn btn-primary btn-lg btn-confirm-pay">
                                <x-icon name="check" /> Bayar Sekarang (Konfirmasi Instan)
                            </button>
                        </form>

                        <form method="POST" action="{{ route('parent.transactions.cancel', $transaction) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm text-danger">
                                <x-icon name="trash" /> Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <aside class="panel payment-summary-aside">
                <span class="panel-kicker">Ringkasan Tagihan</span>
                <h2>Detail Transaksi</h2>

                <div class="payment-summary-meta">
                    <div class="summary-line">
                        <span>Invoice</span>
                        <b>{{ $transaction->invoice_code }}</b>
                    </div>
                    <div class="summary-line">
                        <span>Siswa (Anak)</span>
                        <b>{{ $child->name ?? $metadata['child_name'] ?? 'Anak' }}</b>
                    </div>
                    <div class="summary-line">
                        <span>Jadwal Kelas</span>
                        <b>Hari {{ $schedule->day_of_week ?? $metadata['schedule_day'] ?? '-' }}</b>
                    </div>
                    <div class="summary-line">
                        <span>Jam Sesi</span>
                        <b>{{ $metadata['schedule_time'] ?? '-' }}</b>
                    </div>
                </div>

                <hr class="summary-divider">

                <div class="payment-course-brief">
                    <h4>{{ $course->title ?? $metadata['course_title'] ?? 'Course' }}</h4>
                    <small>{{ $course->category->name ?? 'Kategori' }} • {{ $course->location_name ?? $metadata['location'] ?? 'Offline' }}</small>
                </div>

                <hr class="summary-divider">

                <div class="summary-line">
                    <span>Harga Kursus</span>
                    <span>Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-line">
                    <span>Biaya Platform</span>
                    <span>Rp{{ number_format($transaction->platform_fee, 0, ',', '.') }}</span>
                </div>
                @if(!empty($metadata['discount']) && $metadata['discount'] > 0)
                <div class="summary-line discount-line">
                    <span>Diskon Voucher</span>
                    <span>-Rp{{ number_format($metadata['discount'], 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-total">
                    <span>Total Harus Dibayar</span>
                    <b>Rp{{ number_format($transaction->total, 0, ',', '.') }}</b>
                </div>

                <div class="payment-security-pill">
                    <x-icon name="shield-check" />
                    <span>Pembayaran Terproteksi & Terenkripsi 256-bit</span>
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
            showToast(label + ' berhasil disalin ke clipboard!');
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
    }, 3200);
}

// Live Countdown Timer
document.addEventListener('DOMContentLoaded', () => {
    const timerEl = document.getElementById('countdownTimer');
    if (timerEl) {
        const expireTs = parseInt(timerEl.getAttribute('data-expire'), 10) * 1000;
        function updateTimer() {
            const now = new Date().getTime();
            const distance = expireTs - now;
            if (distance <= 0) {
                timerEl.textContent = '00:00:00 (Kadaluarsa)';
                return;
            }
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

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
