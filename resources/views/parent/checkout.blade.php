@extends('layouts.app')
@section('title','Checkout Kursus')

@section('content')
<section class="dashboard-page checkout-page-wrap">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Checkout Booking</span>
            <h1>Periksa & Selesaikan Checkout</h1>
            <p>Pastikan kursus, siswa, dan jadwal yang dipilih sudah sesuai sebelum melanjutkan ke pembayaran.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('parent.cart') }}">
                <x-icon name="arrow-left" /> Kembali ke Keranjang
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="flash error">
            <x-icon name="conflict" />
            <div>
                <strong>Perhatian:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($hasConflict)
        <div class="conflict-alert-banner">
            <div class="conflict-alert-icon">
                <x-icon name="conflict" />
            </div>
            <div class="conflict-alert-copy">
                <h4>Peringatan Bentrok Jadwal Terdeteksi!</h4>
                <p>Beberapa jadwal yang Anda pilih bertabrakan dengan jadwal kelas aktif anak Anda. Item yang bentrok tidak dapat diproses secara bersamaan. Silakan ubah jadwal atau pilih alternatif di bawah.</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('parent.checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="checkout-grid">
            <div class="checkout-main-col">
                {{-- SECTION 1: Course List --}}
                <div class="panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-kicker">Rincian Kursus</span>
                            <h2>Kursus yang Akan Didaftarkan ({{ $items->count() }})</h2>
                        </div>
                    </div>

                    <div class="checkout-items-stack">
                        @foreach($items as $item)
                            <article class="checkout-course-card {{ !empty($item->conflicts) ? 'item-conflict' : '' }}">
                                <div class="checkout-thumb-box">
                                    <x-course-art :course="$item->schedule->course" />
                                </div>
                                <div class="checkout-course-body">
                                    <div class="checkout-course-header">
                                        <div>
                                            <span class="category-pill">{{ $item->schedule->course->category->name }}</span>
                                            <h3>{{ $item->schedule->course->title }}</h3>
                                        </div>
                                        <div class="checkout-course-price">
                                            Rp{{ number_format($item->schedule->course->price, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="checkout-meta-pills">
                                        <span class="meta-pill child-pill">
                                            <x-icon name="child" /> Siswa: <b>{{ $item->child->name }}</b>
                                        </span>
                                        <span class="meta-pill">
                                            <x-icon name="calendar" /> Hari {{ $item->schedule->day_of_week }}, {{ substr($item->schedule->start_time, 0, 5) }} - {{ substr($item->schedule->end_time, 0, 5) }} WIB
                                        </span>
                                        <span class="meta-pill">
                                            <x-icon name="location" /> {{ $item->schedule->course->location_name }}, {{ $item->schedule->course->city }}
                                        </span>
                                        @if($item->schedule->course->instructor)
                                            <span class="meta-pill">
                                                <x-icon name="user" /> Mentor: {{ $item->schedule->course->instructor->name }}
                                            </span>
                                        @endif
                                    </div>

                                    @if(!empty($item->conflicts))
                                        <div class="item-conflict-box">
                                            <x-icon name="conflict" />
                                            <div>
                                                <b>Bentrok Jadwal dengan Kursus Aktif:</b>
                                                <ul>
                                                    @foreach($item->conflicts as $c)
                                                        <li>{{ $c['course'] }} ({{ $c['schedule'] }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- SECTION 2: Payment Method Selection --}}
                <div class="panel payment-method-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-kicker">Metode Pembayaran</span>
                            <h2>Pilih Jalur Pembayaran</h2>
                        </div>
                    </div>

                    <div class="payment-methods-grid">
                        @foreach($paymentMethods as $index => $pm)
                            <label class="payment-method-card {{ $index === 0 ? 'selected' : '' }}">
                                <input type="radio" name="payment_method" value="{{ $pm['id'] }}" {{ $index === 0 ? 'checked' : '' }} class="payment-radio">
                                <div class="method-card-content">
                                    <div class="method-icon-badge">
                                        <x-icon name="{{ $pm['icon'] }}" />
                                    </div>
                                    <div class="method-card-details">
                                        <div class="method-title-row">
                                            <h4>{{ $pm['name'] }}</h4>
                                            <span class="method-tag">{{ $pm['badge'] }}</span>
                                        </div>
                                        <p>{{ $pm['desc'] }}</p>
                                    </div>
                                    <div class="method-check-circle">
                                        <x-icon name="check" />
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- SECTION 3: Special Notes for Mentor --}}
                <div class="panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-kicker">Catatan Tambahan (Opsional)</span>
                            <h2>Pesan Khusus untuk Mentor</h2>
                        </div>
                    </div>
                    <div class="notes-field-wrap">
                        <textarea name="parent_notes" rows="3" class="form-control" placeholder="Contoh: Anak saya sedikit pemalu di awal sesi, atau memiliki preferensi belajar visual..."></textarea>
                        <small class="text-muted">Catatan ini akan diteruskan ke pengajar untuk membantu pendekatan belajar yang lebih personal.</small>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR: Order Summary --}}
            <aside class="checkout-summary-aside">
                <div class="panel checkout-summary-card">
                    <span class="panel-kicker">Ringkasan Biaya</span>
                    <h2>Total Tagihan</h2>

                    <div class="voucher-box">
                        <label for="voucherInput">Kode Voucher / Promo</label>
                        <div class="voucher-input-group">
                            <input type="text" id="voucherInput" name="voucher_code" placeholder="Contoh: SKILLHEMAT" class="form-control" style="text-transform:uppercase">
                            <button type="button" class="btn btn-soft btn-sm" id="btnApplyVoucher">Pakai</button>
                        </div>
                        <small class="voucher-hint">Gunakan <b>SKILLHEMAT</b> untuk potongan Rp25.000</small>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-line">
                        <span>Subtotal ({{ $items->count() }} kursus)</span>
                        <b id="subtotalVal">Rp{{ number_format($subtotal, 0, ',', '.') }}</b>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Platform (Rp15.000/kursus)</span>
                        <b>Rp{{ number_format($platformFee, 0, ',', '.') }}</b>
                    </div>
                    <div class="summary-line discount-line" id="discountLine" style="display:none;">
                        <span>Potongan Diskon</span>
                        <b id="discountVal" class="text-success">-Rp0</b>
                    </div>

                    <div class="summary-total">
                        <span>Total Pembayaran</span>
                        <b id="totalVal">Rp{{ number_format($total, 0, ',', '.') }}</b>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg checkout-submit-btn">
                        <span>Lanjutkan ke Pembayaran</span> <x-icon name="arrow-right" />
                    </button>

                    <div class="checkout-security-box">
                        <x-icon name="shield-check" />
                        <div>
                            <b>Jaminan SkillPath</b>
                            <p>Jadwal otomatis terverifikasi. Jika terjadi kendala kuota, saldo dikembalikan utuh 100%.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Payment method selection styling
    const methodCards = document.querySelectorAll('.payment-method-card');
    methodCards.forEach(card => {
        card.addEventListener('click', () => {
            methodCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
        });
    });

    // Voucher simulator logic
    const voucherInput = document.getElementById('voucherInput');
    const btnApply = document.getElementById('btnApplyVoucher');
    const discountLine = document.getElementById('discountLine');
    const discountVal = document.getElementById('discountVal');
    const totalVal = document.getElementById('totalVal');

    const baseSubtotal = {{ $subtotal }};
    const platformFee = {{ $platformFee }};
    let baseTotal = baseSubtotal + platformFee;

    if (btnApply && voucherInput) {
        btnApply.addEventListener('click', () => {
            const code = voucherInput.value.trim().toUpperCase();
            let discount = 0;
            if (code === 'SKILLHEMAT') {
                discount = 25000;
            } else if (code === 'ANAKHEBAT') {
                discount = 50000;
            }

            if (discount > 0) {
                discountLine.style.display = 'flex';
                discountVal.textContent = '-Rp' + discount.toLocaleString('id-ID');
                const newTotal = Math.max(0, baseTotal - discount);
                totalVal.textContent = 'Rp' + newTotal.toLocaleString('id-ID');
                alert('Voucher ' + code + ' berhasil diterapkan! Potongan Rp' + discount.toLocaleString('id-ID'));
            } else if (code !== '') {
                alert('Kode voucher tidak valid atau sudah kadaluarsa.');
                discountLine.style.display = 'none';
                totalVal.textContent = 'Rp' + baseTotal.toLocaleString('id-ID');
            }
        });
    }
});
</script>
@endsection
