@extends('layouts.app')
@section('title',$course->title)
@section('content')
<section class="course-detail section compact">
    <div class="detail-grid">
        <div class="detail-visual"><x-course-art :course="$course" class="detail-art" /></div>
        <div class="detail-copy">
            <div class="detail-head-row">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="eyebrow">{{ $course->category->name }}</span>
                    @php
                        $levelClass = match(strtolower($course->level ?? 'beginner')) {
                            'expert' => 'badge-expert',
                            'intermediate' => 'badge-intermediate',
                            default => 'badge-beginner'
                        };
                    @endphp
                    <span class="course-level-badge {{ $levelClass }}">{{ $course->level ?? 'Beginner' }}</span>
                </div>
                @auth
                    @if(auth()->user()->role==='parent')
                    <form method="POST" action="{{ route('parent.wishlist.toggle',$course) }}">@csrf<button class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" title="{{ $isWishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}"><x-icon name="heart" /></button></form>
                    @endif
                @endauth
            </div>
            <h1>{{ $course->title }}</h1>
            <p class="lead">{{ $course->subtitle }}</p>
            <div class="meta meta-lg">
                <span><x-icon name="child" /> {{ $course->age_min }}-{{ $course->age_max }} tahun</span>
                <span><x-icon name="star" /> Tingkat {{ $course->level ?? 'Beginner' }}</span>
                <span><x-icon name="location" /> {{ $course->location_name }}, {{ $course->city }}</span>
                <span><x-icon name="sessions" /> {{ $course->sessions_count }} sesi</span>
                <span><x-icon name="clock" /> {{ $course->duration_minutes }} menit</span>
            </div>
            <p>{{ $course->description }}</p>
            <div class="mentor-panel">
                <div class="mentor-avatar" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
                    @if($course->instructor->avatar_url)
                        <img src="{{ $course->instructor->avatar_url }}" alt="Foto {{ $course->instructor->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    @else
                        {{ $course->instructor->initial }}
                    @endif
                </div>
                <div>
                    <small>Mentor Pengajar</small>
                    <div style="display:inline-flex; align-items:center; gap:4px;">
                        <b>{{ $course->instructor->name }}</b>
                        <span class="mentor-verified-badge" title="Mentor Terverifikasi SkillPath" aria-label="Terverifikasi">
                            <x-icon name="verified" />
                        </span>
                    </div>
                </div>
                <span class="rating"><x-icon name="star" /> 4.9</span>
            </div>
            <div class="course-price">
                <div>
                    <small>Mulai dari (Paket 3 Bulan)</small>
                    <b>Rp{{ number_format($course->price,0,',','.') }}</b>
                </div>
                <span>Tersedia paket 3 Bulan, 6 Bulan, & 1 Tahun</span>
            </div>
        </div>
    </div>

    {{-- SECTION: Paket Pilihan Belajar --}}
    <div class="course-packages-section" id="pilihanPaket">
        <div class="packages-section-head">
            <span class="eyebrow">Pilihan Durasi Paket</span>
            <h2>Pilih Paket Belajar yang Tepat untuk Anak</h2>
            <p>Tersedia pilihan paket 3 Bulan, 6 Bulan, dan 1 Tahun dengan kurikulum berjenjang, pendampingan mentor ahli, dan sertifikasi kelulusan.</p>
        </div>

        <div class="packages-grid">
            @foreach($course->packages as $months => $pkg)
                <label class="package-card {{ $pkg['highlight'] ? 'featured' : '' }} {{ $months == 6 ? 'selected' : '' }}" data-duration="{{ $months }}" data-price="{{ $pkg['price'] }}" data-original-price="{{ $pkg['original_price'] }}" data-sessions="{{ $pkg['sessions'] }}" data-title="{{ $pkg['title'] }}" data-savings="{{ $pkg['savings'] }}">
                    <input type="radio" name="package_selector" value="{{ $months }}" {{ $months == 6 ? 'checked' : '' }} class="package-radio-input">
                    
                    <div class="pkg-header">
                        @if($pkg['discount_percent'] > 0)
                            <span class="pkg-badge {{ $pkg['badge_type'] }}">{{ $pkg['badge'] }}</span>
                        @else
                            <span class="pkg-badge normal">{{ $pkg['badge'] }}</span>
                        @endif
                        <h3 class="pkg-title">{{ $pkg['title'] }}</h3>
                        <div class="pkg-duration-meta">
                            <span><x-icon name="sessions" /> {{ $pkg['sessions'] }} Sesi Pertemuan</span>
                            <span><x-icon name="calendar" /> {{ $pkg['duration_months'] }} Bulan</span>
                        </div>
                    </div>

                    <div class="pkg-pricing">
                        @if($pkg['discount_percent'] > 0)
                            <div class="pkg-orig-price">Rp{{ number_format($pkg['original_price'], 0, ',', '.') }}</div>
                        @endif
                        <div class="pkg-price">
                            <span class="currency">Rp</span>
                            <span class="amount">{{ number_format($pkg['price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="pkg-per-month">≈ Rp{{ number_format($pkg['price_per_month'], 0, ',', '.') }} / bulan</div>
                        @if($pkg['savings'] > 0)
                            <div class="pkg-savings-pill">Hemat Rp{{ number_format($pkg['savings'], 0, ',', '.') }}</div>
                        @endif
                    </div>

                    <ul class="pkg-features">
                        @foreach($pkg['features'] as $feature)
                            <li><x-icon name="check" /> <span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>

                    <div class="pkg-select-btn">
                        <span class="select-indicator-text">{{ $months == 6 ? '✓ Paket Terpilih' : 'Pilih Paket Ini' }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div class="course-benefits">
        <article><x-icon name="check" /><div><b>Belajar langsung di studio</b><small>Interaksi nyata dengan mentor dan kelompok kecil.</small></div></article>
        <article><x-icon name="path" /><div><b>Progress terpantau berkala</b><small>Orang tua dapat mengikuti perkembangan belajar dari dashboard.</small></div></article>
        <article><x-icon name="certificate" /><div><b>Sertifikat berbasis kelulusan</b><small>Sertifikat resmi terbit setelah anak mencapai passing grade.</small></div></article>
    </div>

    <div class="booking-panel">
        <div class="booking-copy">
            <span class="eyebrow">Booking Offline Class</span>
            <h2>Tambahkan ke Keranjang</h2>
            <p>Pilih anak, jadwal kelas offline mingguan, dan konfirmasi paket belajar Anda.</p>
            <div class="booking-note">
                <x-icon name="conflict" />
                <span>Pengecekan jadwal bentrok dilakukan otomatis saat checkout.</span>
            </div>
        </div>

        @auth
            @if(auth()->user()->role==='parent')
            <form method="POST" action="{{ route('parent.cart.store') }}" class="booking-form" id="bookingCourseForm">
                @csrf
                <input type="hidden" name="package_duration" id="selectedPackageDuration" value="6">

                <div class="booking-selected-pkg" id="bookingPackageInfo">
                    <div>
                        <small>Paket Belajar Dipilih:</small>
                        <b id="summaryPkgTitle">Paket 6 Bulan (24 Sesi)</b>
                        <small id="summaryPkgSavings" style="color:#059669; font-weight:600;">Hemat Rp{{ number_format($course->getPackage(6)['savings'], 0, ',', '.') }}</small>
                    </div>
                    <div class="pkg-summary-price" id="summaryPkgPrice">
                        Rp{{ number_format($course->getPackage(6)['price'], 0, ',', '.') }}
                    </div>
                </div>

                <label>
                    Anak yang Mengikuti Kelas
                    <select name="child_id" required>
                        @foreach(auth()->user()->children as $child)
                            <option value="{{ $child->id }}">{{ $child->name }} • {{ $child->age }} tahun</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    Pilihan Jadwal Rutin Mingguan
                    <select name="schedule_id" required>
                        @foreach($course->schedules as $schedule)
                            <option value="{{ $schedule->id }}">Hari {{ $schedule->day_name }} • {{ substr($schedule->start_time,0,5) }}-{{ substr($schedule->end_time,0,5) }} WIB • {{ $schedule->room }}</option>
                        @endforeach
                    </select>
                </label>

                <button class="btn btn-primary btn-lg" id="btnAddToCart">
                    Tambah ke Keranjang • <span id="btnPriceText">Rp{{ number_format($course->getPackage(6)['price'], 0, ',', '.') }}</span> <x-icon name="cart" />
                </button>
            </form>
            @else
                <p class="muted-box">Booking hanya dapat dilakukan melalui akun orang tua.</p>
            @endif
        @else
            <div>
                <p style="margin-bottom: 12px; font-size: 13px; color: #475569;">Silakan masuk ke akun orang tua untuk mendaftarkan anak Anda ke kelas ini.</p>
                <a class="btn btn-primary btn-lg" href="{{ route('login') }}">Masuk sebagai Orang Tua <x-icon name="arrow-right" /></a>
            </div>
        @endauth
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const packageCards = document.querySelectorAll('.package-card');
    const durationInput = document.getElementById('selectedPackageDuration');
    const summaryTitle = document.getElementById('summaryPkgTitle');
    const summarySavings = document.getElementById('summaryPkgSavings');
    const summaryPrice = document.getElementById('summaryPkgPrice');
    const btnPriceText = document.getElementById('btnPriceText');

    function formatRupiah(number) {
        return 'Rp' + new Intl.NumberFormat('id-ID').format(number);
    }

    packageCards.forEach(card => {
        card.addEventListener('click', function(e) {
            packageCards.forEach(c => {
                c.classList.remove('selected');
                const radio = c.querySelector('.package-radio-input');
                if (radio) radio.checked = false;
                const btnText = c.querySelector('.select-indicator-text');
                if (btnText) btnText.textContent = 'Pilih Paket Ini';
            });

            this.classList.add('selected');
            const radio = this.querySelector('.package-radio-input');
            if (radio) radio.checked = true;
            const btnText = this.querySelector('.select-indicator-text');
            if (btnText) btnText.textContent = '✓ Paket Terpilih';

            const duration = this.dataset.duration;
            const price = parseInt(this.dataset.price, 10);
            const sessions = this.dataset.sessions;
            const title = this.dataset.title;
            const savings = parseInt(this.dataset.savings, 10);

            if (durationInput) durationInput.value = duration;
            if (summaryTitle) summaryTitle.textContent = `${title} (${sessions} Sesi)`;
            if (summaryPrice) summaryPrice.textContent = formatRupiah(price);
            if (btnPriceText) btnPriceText.textContent = formatRupiah(price);

            if (summarySavings) {
                if (savings > 0) {
                    summarySavings.textContent = `Hemat ${formatRupiah(savings)}`;
                    summarySavings.style.display = 'block';
                } else {
                    summarySavings.style.display = 'none';
                }
            }
        });
    });
});
</script>
@endpush
@endsection
