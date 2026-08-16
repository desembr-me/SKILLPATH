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
                        @php
                            $totalSavings = $items->sum(fn ($i) => (float) ($i->package_info['savings'] ?? 0));
                        @endphp
                        @foreach($items as $item)
                            @php
                                $pkg = $item->package_info;
                            @endphp
                            <article class="checkout-course-card {{ !empty($item->conflicts) ? 'item-conflict' : '' }}">
                                <div class="checkout-thumb-box">
                                    <x-course-art :course="$item->schedule->course" />
                                </div>
                                <div class="checkout-course-body">
                                    <div class="checkout-course-header">
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                                <span class="category-pill">{{ $item->schedule->course->category->name }}</span>
                                                <span class="status-chip {{ $pkg['badge_type'] === 'popular' ? 'recommended' : ($pkg['badge_type'] === 'best_value' ? 'confirmed' : 'soft') }}" style="font-size: 10px;">
                                                    {{ $pkg['title'] }} • {{ $pkg['sessions'] }} Sesi
                                                </span>
                                            </div>
                                            <h3>{{ $item->schedule->course->title }}</h3>
                                        </div>
                                        <div class="checkout-course-price" style="text-align: right;">
                                            @if($pkg['discount_percent'] > 0)
                                                <small style="text-decoration: line-through; color: var(--muted); font-size: 11px; display: block;">
                                                    Rp{{ number_format($pkg['original_price'], 0, ',', '.') }}
                                                </small>
                                            @endif
                                            <b>Rp{{ number_format($item->calculated_price, 0, ',', '.') }}</b>
                                            @if($pkg['savings'] > 0)
                                                <small style="color: #059669; font-weight: 600; font-size: 10.5px; display: block;">
                                                    Hemat Rp{{ number_format($pkg['savings'], 0, ',', '.') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="checkout-meta-pills">
                                        <span class="meta-pill child-pill">
                                            <x-icon name="child" /> Siswa: <b>{{ $item->child->name }}</b>
                                        </span>
                                        <span class="meta-pill">
                                            <x-icon name="calendar" /> Hari {{ $item->schedule->day_name }}, {{ substr($item->schedule->start_time, 0, 5) }} - {{ substr($item->schedule->end_time, 0, 5) }} WIB
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
                            <p class="panel-subtitle">Pilih metode pembayaran yang paling nyaman. Semua jalur terenkripsi dan diverifikasi sistem secara real-time.</p>
                        </div>
                    </div>

                    <div class="pm-groups-container">
                        @foreach($paymentMethods as $groupKey => $group)
                            <div class="pm-group-section">
                                <div class="pm-group-header">
                                    <span class="pm-group-icon"><x-icon :name="$group['icon']" /></span>
                                    <div>
                                        <h3 class="pm-group-title">{{ $group['title'] }}</h3>
                                        <span class="pm-group-subtitle">{{ $group['subtitle'] }}</span>
                                    </div>
                                </div>

                                <div class="pm-cards-grid {{ count($group['items']) === 1 ? 'single-col' : '' }}">
                                    @foreach($group['items'] as $pm)
                                        @php($isSelected = ($loop->parent->first && $loop->first))
                                        <label class="pm-card {{ $isSelected ? 'selected' : '' }}" for="pm_{{ $pm['id'] }}">
                                            <input type="radio" id="pm_{{ $pm['id'] }}" name="payment_method" value="{{ $pm['id'] }}" {{ $isSelected ? 'checked' : '' }} class="pm-radio-input">
                                            
                                            <div class="pm-card-left">
                                                <div class="pm-brand-badge pm-brand-{{ $pm['brand'] }}">
                                                    <span>{{ $pm['brand_label'] }}</span>
                                                </div>
                                                <div class="pm-card-info">
                                                    <div class="pm-title-row">
                                                        <h4 class="pm-title">{{ $pm['name'] }}</h4>
                                                        <span class="pm-status-pill {{ $pm['badge_type'] }}">{{ $pm['badge'] }}</span>
                                                    </div>
                                                    <p class="pm-desc">{{ $pm['desc'] }}</p>
                                                    @if(!empty($pm['ewallet_tags']))
                                                        <div class="pm-sub-badges">
                                                            @foreach($pm['ewallet_tags'] as $tag)
                                                                <span class="ewallet-mini-pill">{{ $tag }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="pm-card-right">
                                                <div class="pm-radio-indicator">
                                                    <x-icon name="check" class="pm-check-svg" />
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SECTION 3: Notes for Instructor --}}
                <div class="panel parent-notes-panel">
                    <div class="panel-heading">
                        <div>
                            <span class="panel-kicker">Opsional</span>
                            <h2>Catatan Khusus untuk Mentor</h2>
                            <p class="panel-subtitle">Beri tahu mentor tentang karakter, gaya belajar, atau kebutuhan khusus anak agar pendampingan belajar lebih efektif.</p>
                        </div>
                    </div>

                    <div class="notes-field-wrap">
                        <div class="notes-quick-tags">
                            <span class="quick-tag-label">Ide catatan cepat:</span>
                            <button type="button" class="quick-chip" onclick="addNoteSuggestion('Anak sedikit pemalu di awal pertemuan.')">+ Pemalu di awal</button>
                            <button type="button" class="quick-chip" onclick="addNoteSuggestion('Lebih cepat paham melalui visual & praktik.')">+ Suka visual & praktik</button>
                            <button type="button" class="quick-chip" onclick="addNoteSuggestion('Sangat aktif dan antusias bertanya.')">+ Sangat aktif</button>
                            <button type="button" class="quick-chip" onclick="addNoteSuggestion('Perlu dorongan percaya diri saat berpendapat.')">+ Butuh dorongan PD</button>
                        </div>

                        <div class="notes-textarea-container">
                            <textarea 
                                id="parentNotesArea"
                                name="parent_notes" 
                                rows="3" 
                                class="notes-textarea" 
                                placeholder="Contoh: Anak saya sedikit pemalu di awal pertemuan, lebih cepat paham dengan media visual/gambar, atau sangat antusias pada pembuatan game..."
                            ></textarea>
                        </div>

                        <div class="notes-footer-hint">
                            <x-icon name="info" />
                            <span>Catatan ini bersifat privat dan langsung diteruskan kepada pengajar kelas untuk pendekatan personal.</span>
                        </div>
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
                        <span>Subtotal Paket ({{ $items->count() }} kursus)</span>
                        <b id="subtotalVal">Rp{{ number_format($subtotal, 0, ',', '.') }}</b>
                    </div>
                    @if($totalSavings > 0)
                        <div class="summary-line" style="color: #059669;">
                            <span>Total Hemat Paket</span>
                            <b>-Rp{{ number_format($totalSavings, 0, ',', '.') }}</b>
                        </div>
                    @endif
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
    const methodCards = document.querySelectorAll('.pm-card');
    methodCards.forEach(card => {
        card.addEventListener('click', () => {
            methodCards.forEach(c => {
                c.classList.remove('selected');
                const radio = c.querySelector('.pm-radio-input');
                if (radio) radio.checked = false;
            });
            card.classList.add('selected');
            const activeRadio = card.querySelector('.pm-radio-input');
            if (activeRadio) activeRadio.checked = true;
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

function addNoteSuggestion(text) {
    const textarea = document.getElementById('parentNotesArea');
    if (!textarea) return;
    const current = textarea.value.trim();
    if (current.length > 0) {
        if (!current.includes(text)) {
            textarea.value = current + ' ' + text;
        }
    } else {
        textarea.value = text;
    }
    textarea.focus();
}
</script>
@endsection
