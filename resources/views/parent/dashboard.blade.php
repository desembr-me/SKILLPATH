@extends('layouts.app')
@section('title','Dashboard Orang Tua')
@section('content')
<section class="dashboard-page">
    <div class="dashboard-hero parent-dashboard-hero">
        <div class="dashboard-hero-decor-circle-1"></div>
        <div class="dashboard-hero-decor-circle-2"></div>
        
        <div class="dashboard-hero-left">
            <div class="dashboard-hero-badge">
                <x-icon name="spark" /> <span>Dashboard Orang Tua</span>
            </div>
            <h1 class="dashboard-hero-title">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="dashboard-hero-subtitle">Pantau perkembangan anak, jadwal kursus, evaluasi sertifikat, dan transaksi dari satu tempat yang nyaman.</p>
            
            <div class="dashboard-hero-actions">
                <a class="btn-hero-action btn-hero-primary" href="{{ route('parent.onboarding') }}">
                    <x-icon name="plus" /> <span>Tambah Anak</span>
                </a>
                <a class="btn-hero-action btn-hero-glass" href="{{ route('parent.children') }}">
                    <x-icon name="child" /> <span>Profil Anak</span>
                </a>
                <a class="btn-hero-action btn-hero-glass" href="{{ route('mentors.index') }}">
                    <x-icon name="users" /> <span>Lihat Semua Mentor</span>
                </a>
            </div>
        </div>

        <div class="dashboard-hero-right">
            <div class="hero-quick-profile-card">
                <div class="quick-profile-header">
                    <a class="quick-profile-avatar-wrap" href="{{ route('parent.profile') }}" title="Kelola Profil Saya">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                        <span class="quick-avatar-badge" title="Terverifikasi"><x-icon name="check" /></span>
                    </a>
                    <div class="quick-profile-info">
                        <span class="quick-profile-role">Keluarga SkillPath</span>
                        <h3 class="quick-profile-name">{{ auth()->user()->name }}</h3>
                        <a class="quick-profile-edit-link" href="{{ route('parent.profile') }}">Kelola Profil Akun &rarr;</a>
                    </div>
                </div>

                @if($children->count() > 0)
                    <div class="quick-children-section">
                        <span class="quick-section-label">Anak Terdaftar ({{ $children->count() }}):</span>
                        <div class="quick-children-pills">
                            @foreach($children->take(3) as $child)
                                <a href="{{ route('parent.learning-path', $child) }}" class="quick-child-pill" title="Lihat Jalur Belajar {{ $child->name }}">
                                    <span class="child-pill-avatar">
                                        @if($child->avatar_url)
                                            <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}">
                                        @elseif($child->avatar && !str_starts_with($child->avatar, 'avatars/'))
                                            {{ $child->avatar }}
                                        @else
                                            {{ $child->initial }}
                                        @endif
                                    </span>
                                    <span class="child-pill-name">{{ Str::limit($child->name, 10) }}</span>
                                    <span class="child-pill-courses">{{ $child->enrollments->where('status', 'active')->count() }} kursus</span>
                                </a>
                            @endforeach
                            @if($children->count() > 3)
                                <a href="{{ route('parent.children') }}" class="quick-child-more">+{{ $children->count() - 3 }} lainnya</a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="quick-children-empty">
                        <p>Belum ada anak terdaftar di akun ini.</p>
                        <a href="{{ route('parent.onboarding') }}" class="btn-quick-add">+ Daftarkan Anak Pertama</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notifikasi / Pengingat Jadwal Hari Ini -->
    @if($todaySchedules->isNotEmpty())
        <div class="schedule-reminder-banner" id="today-schedule-reminder">
            <div class="reminder-banner-header">
                <div class="reminder-header-left">
                    <div class="reminder-icon-bubble">
                        <x-icon name="calendar" />
                    </div>
                    <div class="reminder-header-titles">
                        <h2>
                            <span>Pengingat Jadwal Kelas Hari Ini</span>
                            <span class="pulse-radar-dot"></span>
                        </h2>
                        <p>Ada {{ $todaySchedules->count() }} sesi pembelajaran yang dijadwalkan berlangsung hari ini untuk anak Anda.</p>
                    </div>
                </div>
                <div class="reminder-header-right">
                    <span class="reminder-date-chip">
                        <x-icon name="calendar" style="width:14px; height:14px;" /> {{ $todayDateFormatted }}
                    </span>
                    <span class="reminder-count-pill">
                        <span class="pulse-radar-dot"></span> {{ $todaySchedules->count() }} Kelas Hari Ini
                    </span>
                </div>
            </div>

            <div class="today-classes-grid">
                @foreach($todaySchedules as $sched)
                    <div class="today-class-card">
                        <div class="tcc-top">
                            <div class="tcc-child-info">
                                <div class="tcc-child-avatar">
                                    @if($sched['child']->avatar_url)
                                        <img src="{{ $sched['child']->avatar_url }}" alt="{{ $sched['child']->name }}" style="width:100%; height:100%; object-fit:cover;">
                                    @elseif($sched['child']->avatar && !str_starts_with($sched['child']->avatar, 'avatars/'))
                                        {{ $sched['child']->avatar }}
                                    @else
                                        {{ $sched['child']->initial }}
                                    @endif
                                </div>
                                <span class="tcc-child-name">{{ $sched['child']->name }}</span>
                            </div>
                            <div>
                                @if($sched['is_ongoing'])
                                    <span class="tcc-status-badge status-ongoing">
                                        <span class="pulse-radar-dot" style="background:#059669;"></span> Sedang Berlangsung
                                    </span>
                                @elseif($sched['is_upcoming'])
                                    <span class="tcc-status-badge status-upcoming">
                                        <x-icon name="spark" style="width:12px; height:12px;" /> Akan Datang
                                    </span>
                                @else
                                    <span class="tcc-status-badge status-finished">
                                        <x-icon name="check" style="width:12px; height:12px;" /> Selesai Hari Ini
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="tcc-main-info">
                            <h3 class="tcc-course-title">{{ $sched['course']->title }}</h3>
                            <div class="tcc-details-list">
                                <div class="tcc-detail-item">
                                    <x-icon name="clock" />
                                    <span>Waktu: <strong>{{ $sched['time_label'] }}</strong></span>
                                </div>
                                <div class="tcc-detail-item">
                                    <x-icon name="location" />
                                    <span>Ruang / Lokasi: <strong>{{ $sched['room'] }}</strong></span>
                                </div>
                                @if($sched['session'] && $sched['session']->topic)
                                    <div class="tcc-detail-item">
                                        <x-icon name="book" />
                                        <span>Topik: <strong>{{ $sched['session']->topic }}</strong></span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="tcc-mentor-strip">
                            @if($sched['instructor'] && $sched['instructor']->avatar_url)
                                <img src="{{ $sched['instructor']->avatar_url }}" alt="{{ $sched['instructor']->name }}" class="tcc-mentor-avatar">
                            @else
                                <div class="tcc-mentor-avatar">{{ $sched['instructor'] ? $sched['instructor']->initial : 'M' }}</div>
                            @endif
                            <div class="tcc-mentor-name">
                                <small style="color:#6b7280; font-size:10.5px;">Mentor Pengajar:</small>
                                <div class="tcc-mentor-author">
                                    <b>{{ $sched['instructor'] ? $sched['instructor']->name : 'Mentor SkillPath' }}</b>
                                    <span class="mentor-verified-badge" title="Mentor Terverifikasi SkillPath" aria-label="Terverifikasi">
                                        <x-icon name="verified" />
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="tcc-actions">
                            <a href="{{ route('parent.learning-path', $sched['child']) }}" class="btn btn-primary btn-sm" style="font-size:12px; padding:7px 14px;">
                                <x-icon name="book" style="width:13px; height:13px;" /> Ruang Belajar Anak
                            </a>
                            <a href="{{ route('parent.schedule') }}" class="btn btn-soft btn-sm" style="font-size:12px; padding:7px 12px;">
                                <x-icon name="calendar" style="width:13px; height:13px;" /> Jadwal Lengkap
                            </a>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="copyScheduleReminder('{{ addslashes($sched['course']->title) }}', '{{ addslashes($sched['child']->name) }}', '{{ $sched['time_label'] }}', '{{ addslashes($sched['room']) }}')" style="font-size:12px; padding:7px 10px;" title="Salin Info Pengingat">
                                <x-icon name="spark" style="width:13px; height:13px;" /> Salin Pengingat
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Notifikasi Status Jadwal Hari Ini (Tidak Ada Jadwal) -->
        <div class="schedule-reminder-quiet">
            <div class="srq-left">
                <div class="srq-icon">
                    <x-icon name="calendar" />
                </div>
                <div class="srq-content">
                    <h4>
                        <x-icon name="check" style="width:15px; height:15px; color:#10b981;" /> Tidak Ada Jadwal Kelas Hari Ini ({{ $todayDateFormatted }})
                    </h4>
                    @if($upcomingNextSchedule)
                        <p>
                            Jadwal terdekat berikutnya: <b>{{ $upcomingNextSchedule->course->title }}</b> untuk <b>{{ $upcomingNextSchedule->child->name }}</b> pada hari <b>{{ $upcomingNextSchedule->schedule->day_name }}</b>, {{ $upcomingNextSchedule->schedule->formatted_time }}.
                        </p>
                    @else
                        <p>Semua kelas anak telah terjadwal dengan baik. Anda dapat melihat kalender mingguan atau mendaftar kursus baru.</p>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('parent.schedule') }}" class="btn btn-soft btn-sm" style="font-size:12px; padding:7px 16px;">
                    <x-icon name="calendar" style="width:13px; height:13px;" /> Lihat Kalender Jadwal
                </a>
            </div>
        </div>
    @endif

    <div class="stat-grid">
        <article><span class="stat-icon tone-blue"><x-icon name="child" /></span><div><span>Anak terdaftar</span><b>{{ $children->count() }}</b><small>Profil anak dalam akun keluarga</small></div></article>
        <article><span class="stat-icon tone-green"><x-icon name="sessions" /></span><div><span>Kursus aktif</span><b>{{ $children->sum(fn($c)=>$c->enrollments->where('status','active')->count()) }}</b><small>Kursus yang sedang berjalan</small></div></article>
        <article><span class="stat-icon tone-orange"><x-icon name="certificate" /></span><div><span>Sertifikat diperoleh</span><b>{{ $children->sum(fn($c)=>$c->enrollments->whereNotNull('certificate')->count()) }}</b><small>Kursus yang sudah lulus ujian</small></div></article>
        <article><span class="stat-icon tone-pink"><x-icon name="payment" /></span><div><span>Riwayat transaksi</span><b>{{ $transactions->count() }}</b><small>Total riwayat pembayaran</small></div></article>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Profil keluarga</span><h2>Anak Saya</h2></div><a class="text-link" href="{{ route('parent.children') }}">Kelola profil <x-icon name="arrow-right" /></a></div>
            @forelse($children as $child)
            <div class="child-row">
                <div class="child-avatar">
                    @if($child->avatar_url)
                        <img src="{{ $child->avatar_url }}" alt="Foto {{ $child->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    @elseif($child->avatar && !str_starts_with($child->avatar, 'avatars/'))
                        <span style="font-size:20px;">{{ $child->avatar }}</span>
                    @else
                        {{ $child->initial }}
                    @endif
                </div>
                <div><h3>{{ $child->name }}</h3><p>{{ $child->age }} tahun • {{ implode(', ',$child->interests ?: []) ?: 'Minat belum dipilih' }}</p><div class="mini-tags"><span>{{ $child->enrollments->where('status','active')->count() }} course aktif</span></div></div>
                <a class="btn btn-soft" href="{{ route('parent.learning-path',$child) }}">Jalur Belajar</a>
            </div>
            @empty<div class="empty-state"><x-icon name="child" /><div><b>Belum ada profil anak</b><span>Mulai onboarding bersama anak untuk membuat rekomendasi awal.</span></div></div>@endforelse
        </div>
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Keuangan</span><h2>Transaksi</h2></div></div>
            @forelse($transactions as $trx)
            <div class="transaction-row">
                <div>
                    <b>{{ $trx->invoice_code }}</b>
                    <small>{{ $trx->created_at->format('d M Y') }}</small>
                    <small class="transaction-course">{{ $trx->course_title }} • {{ $trx->child_name }} @if($trx->all_enrollments->count() > 1) <span style="color:#7e22ce; font-weight:700;">(+{{ $trx->all_enrollments->count() - 1 }} lainnya)</span> @endif</small>
                </div>
                <div>
                    <b>Rp{{ number_format($trx->total,0,',','.') }}</b>
                    <span class="status-chip {{ $trx->status }}">{{ ucfirst($trx->status) }}</span>
                    @if($trx->status==='pending')
                        <a href="{{ route('parent.payment.show', $trx) }}" class="pay-link">Bayar</a>
                    @endif
                </div>
            </div>
            @empty<div class="empty-state compact-empty"><x-icon name="payment" /><div><b>Belum ada transaksi</b><span>Transaksi akan muncul setelah course dibooking.</span></div></div>@endforelse
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading"><div><span class="panel-kicker">Pengajar</span><h2>Mentor Pilihan</h2></div><a class="text-link" href="{{ route('mentors.index') }}">Lihat semua <x-icon name="arrow-right" /></a></div>
        @forelse($mentors as $mentor)
        <div class="child-row">
            <div class="child-avatar">
                @if($mentor->avatar_url)
                    <img src="{{ $mentor->avatar_url }}" alt="Foto {{ $mentor->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                @else
                    {{ $mentor->initial }}
                @endif
            </div>
            <div>
                <div class="mentor-title-line">
                    <h3>{{ $mentor->name }}</h3>
                    <span class="mentor-verified-pill" title="Mentor Resmi Terverifikasi SkillPath">
                        <x-icon name="verified" />
                        <span>Terverifikasi</span>
                    </span>
                </div>
                <p>{{ $mentor->headline ?: 'Mentor SkillPath' }}</p>
            </div>
            <a class="btn btn-soft" href="{{ route('mentors.show', $mentor) }}">Lihat Detail</a>
        </div>
        @empty<div class="empty-state compact-empty"><x-icon name="users" /><div><b>Belum ada mentor terdaftar</b><span>Daftar mentor akan muncul di sini.</span></div></div>@endforelse
    </div>

    <!-- Review Section 1: Ulasan Platform SkillPath (Maksimal 1 Kali per Akun) -->
    <div class="review-section-panel" id="platform-review-section">
        <div class="review-panel-top">
            <div>
                <span class="review-panel-kicker"><x-icon name="spark" style="width:13px; height:13px;" /> Pengalaman & Layanan</span>
                <h2 class="review-panel-title">Ulasan Platform SkillPath</h2>
                <p class="review-panel-desc">Bagikan penilaian Anda terhadap kemudahan sistem, proses booking, jadwal, dan layanan SkillPath secara keseluruhan.</p>
            </div>
            <div>
                @if($platformReview)
                    <span class="badge-review-limit badge-success">
                        <x-icon name="check" style="width:13px; height:13px;" /> 1x Ulasan Telah Dikirim
                    </span>
                @else
                    <span class="badge-review-limit badge-active">
                        <x-icon name="info" style="width:13px; height:13px;" /> Maksimal 1 Ulasan per Akun
                    </span>
                @endif
            </div>
        </div>

        @if($platformReview)
            <!-- Showcase Existing Platform Review -->
            <div class="platform-review-showcase" id="platform-review-showcase">
                <div class="platform-showcase-header">
                    <div class="platform-showcase-rating">
                        <span class="rating-label" style="font-size:12px; font-weight:700; color:#475569;">Penilaian Platform:</span>
                        <div class="platform-showcase-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <x-icon name="star" style="width:18px; height:18px; color:{{ $i <= $platformReview->rating ? '#3b82f6' : '#e2e8f0' }};" />
                            @endfor
                        </div>
                        <span class="platform-showcase-score">{{ $platformReview->rating }}.0</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="toggleEdit('platform-review-form-card', 'platform-review-showcase')" style="font-size:12px; padding:6px 14px;">
                        <x-icon name="edit" style="width:13px; height:13px;" /> Ubah Ulasan Platform
                    </button>
                </div>
                <blockquote class="platform-showcase-quote">
                    “{{ $platformReview->review ?: 'Tidak ada catatan tertulis.' }}”
                </blockquote>
                <div class="platform-showcase-footer">
                    <span class="platform-showcase-date">
                        Terakhir diperbarui: {{ $platformReview->updated_at->format('d M Y, H:i') }} WIB
                    </span>
                    <span style="font-size:11.5px; color:#64748b;">Akun Orang Tua: <b>{{ auth()->user()->name }}</b></span>
                </div>
            </div>

            <!-- Hidden Edit Form for Platform Review -->
            <div class="review-form-card" id="platform-review-form-card" style="display: none; margin-top: 14px;">
                <form method="POST" action="{{ route('parent.platform-review.store') }}">
                    @csrf
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h4 style="margin:0; font-size:14px; font-weight:700; color:#1e293b;">Perbarui Ulasan Platform Anda</h4>
                        <button type="button" class="btn btn-sm btn-ghost" onclick="toggleEdit('platform-review-showcase', 'platform-review-form-card')" style="font-size:12px;">
                            Batal
                        </button>
                    </div>
                    <div class="interactive-star-picker">
                        <span class="star-picker-label">Pilih Rating Bintang:</span>
                        <div class="star-rating-options">
                            @foreach([5 => '5 ★ Istimewa', 4 => '4 ★ Sangat Baik', 3 => '3 ★ Cukup Baik', 2 => '2 ★ Kurang', 1 => '1 ★ Sangat Kurang'] as $val => $text)
                                <div class="star-option-item">
                                    <input type="radio" name="platform_rating" id="plat_edit_{{ $val }}" value="{{ $val }}" {{ $platformReview->rating == $val ? 'checked' : '' }} required>
                                    <label for="plat_edit_{{ $val }}">
                                        <x-icon name="star" style="width:14px; height:14px; color:#3b82f6;" /> {{ $text }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="margin-bottom: 14px;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">Ulasan Platform (Opsional):</label>
                        <textarea name="platform_review" class="review-textarea-custom" rows="3" placeholder="Bagikan kesan Anda mengenai aplikasi, jadwal, dan layanan SkillPath...">{{ $platformReview->review }}</textarea>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 20px;">
                            Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-soft btn-sm" onclick="toggleEdit('platform-review-showcase', 'platform-review-form-card')">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- First-time Platform Review Form -->
            <div class="review-form-card">
                <form method="POST" action="{{ route('parent.platform-review.store') }}">
                    @csrf
                    <div class="interactive-star-picker">
                        <span class="star-picker-label">Beri Penilaian Platform Keseluruhan:</span>
                        <div class="star-rating-options">
                            @foreach([5 => '5 ★ Istimewa', 4 => '4 ★ Sangat Baik', 3 => '3 ★ Cukup Baik', 2 => '2 ★ Kurang', 1 => '1 ★ Sangat Kurang'] as $val => $text)
                                <div class="star-option-item">
                                    <input type="radio" name="platform_rating" id="plat_new_{{ $val }}" value="{{ $val }}" {{ $val === 5 ? 'checked' : '' }} required>
                                    <label for="plat_new_{{ $val }}">
                                        <x-icon name="star" style="width:14px; height:14px; color:#3b82f6;" /> {{ $text }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">Tulis Ulasan Anda (Opsional):</label>
                        <textarea name="platform_review" class="review-textarea-custom" rows="3" placeholder="Ceritakan bagaimana kemudahan fitur, transparansi jadwal, atau pengalaman Anda bersama SkillPath..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:9px 24px;">
                        <x-icon name="spark" style="width:14px; height:14px;" /> Kirim Ulasan Platform (1x)
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Review Section 2: Ulasan Mentor Kursus (Hanya Untuk Kelas Yang Diikuti Anak) -->
    <div class="review-section-panel" id="mentor-review-section">
        <div class="review-panel-top">
            <div>
                <span class="review-panel-kicker" style="color:#d97706;"><x-icon name="users" style="width:13px; height:13px;" /> Kualitas Pengajar</span>
                <h2 class="review-panel-title">Ulasan Mentor Kursus</h2>
                <p class="review-panel-desc">Ulasan mentor hanya dapat diberikan jika anak Anda sudah mengikuti atau terdaftar di kelas mentor tersebut.</p>
            </div>
            <div>
                <span class="badge-review-limit badge-active">
                    <x-icon name="child" style="width:13px; height:13px;" /> Berdasarkan Kelas Anak ({{ $mentorEnrollments->count() }})
                </span>
            </div>
        </div>

        @if($mentorEnrollments->count() > 0)
            <div class="mentor-review-grid">
                @foreach($mentorEnrollments as $enrollment)
                    @php
                        $rev = $enrollment->review;
                        $mentor = optional($enrollment->course)->instructor;
                    @endphp
                    <div class="mentor-review-card">
                        <div class="mrc-header">
                            <div>
                                <h3 class="mrc-course-title">{{ $enrollment->course->title }}</h3>
                                <div class="mrc-child-tag">
                                    <x-icon name="child" style="width:12px; height:12px; color:#6366f1;" />
                                    <span>Siswa: <b>{{ $enrollment->child->name ?? 'Anak' }}</b></span>
                                </div>
                            </div>
                            <div>
                                @if($rev)
                                    <span class="mrc-status-pill status-reviewed">Sudah Dinilai</span>
                                @else
                                    <span class="mrc-status-pill status-pending">Perlu Ulasan</span>
                                @endif
                            </div>
                        </div>

                        <!-- Mentor Info Profile -->
                        <div class="mrc-mentor-profile">
                            @if($mentor && $mentor->avatar_url)
                                <img src="{{ $mentor->avatar_url }}" alt="Foto {{ $mentor->name }}" class="mrc-mentor-avatar">
                            @else
                                <div class="mrc-mentor-avatar">{{ $mentor ? $mentor->initial : 'M' }}</div>
                            @endif
                            <div class="mrc-mentor-info">
                                <div class="mrc-mentor-title-row">
                                    <h4>{{ $mentor ? $mentor->name : 'Mentor SkillPath' }}</h4>
                                    <span class="mentor-verified-badge" title="Mentor Terverifikasi SkillPath" aria-label="Terverifikasi">
                                        <x-icon name="verified" />
                                    </span>
                                </div>
                                <p>{{ optional($enrollment->course->category)->name ?: ($mentor->headline ?: 'Mentor Pengajar') }}</p>
                            </div>
                        </div>

                        @if($rev)
                            <!-- Already Reviewed State -->
                            <div class="mrc-showcase-box" id="mentor-showcase-{{ $enrollment->id }}">
                                <div class="mrc-showcase-top">
                                    <div style="display:inline-flex; align-items:center; gap:4px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-icon name="star" style="width:14px; height:14px; color:{{ $i <= $rev->mentor_rating ? '#f59e0b' : '#e2e8f0' }};" />
                                        @endfor
                                        <span style="font-weight:700; font-size:12.5px; color:#b45309; margin-left:3px;">{{ $rev->mentor_rating }}.0</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-ghost" onclick="toggleEdit('mentor-form-{{ $enrollment->id }}', 'mentor-showcase-{{ $enrollment->id }}')" style="font-size:11.5px; padding:4px 8px;">
                                        Ubah Nilai
                                    </button>
                                </div>
                                <p class="mrc-quote">“{{ $rev->mentor_review ?: 'Ulasan mentor tersimpan.' }}”</p>
                            </div>

                            <!-- Edit Form for Mentor Review -->
                            <div id="mentor-form-{{ $enrollment->id }}" style="display: none; background:#f8fafc; padding:14px; border-radius:12px; border:1px solid #e2e8f0;">
                                <form method="POST" action="{{ route('parent.mentor-reviews.store', $enrollment) }}">
                                    @csrf
                                    <div style="margin-bottom: 10px;">
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Pilih Rating Mentor:</label>
                                        <div class="star-rating-options">
                                            @foreach([5 => '5 ★', 4 => '4 ★', 3 => '3 ★', 2 => '2 ★', 1 => '1 ★'] as $val => $text)
                                                <div class="star-option-item">
                                                    <input type="radio" name="mentor_rating" id="m_edit_{{ $enrollment->id }}_{{ $val }}" value="{{ $val }}" {{ $rev->mentor_rating == $val ? 'checked' : '' }} required>
                                                    <label for="m_edit_{{ $enrollment->id }}_{{ $val }}" style="padding:4px 8px; font-size:11.5px;">
                                                        {{ $text }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 10px;">
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Catatan untuk Mentor:</label>
                                        <textarea name="mentor_review" class="review-textarea-custom" rows="2" style="font-size:12px;" placeholder="Masukan pembelajaran untuk mentor...">{{ $rev->mentor_review }}</textarea>
                                    </div>
                                    <div style="display:flex; gap:8px;">
                                        <button type="submit" class="btn btn-primary btn-sm" style="font-size:11.5px; padding:6px 12px;">Simpan</button>
                                        <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('mentor-showcase-{{ $enrollment->id }}', 'mentor-form-{{ $enrollment->id }}')" style="font-size:11.5px; padding:6px 10px;">Batal</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- New Mentor Review Form -->
                            <form method="POST" action="{{ route('parent.mentor-reviews.store', $enrollment) }}" style="background:#f8fafc; padding:14px; border-radius:12px; border:1px solid #e2e8f0; display:flex; flex-direction:column; gap:10px;">
                                @csrf
                                <div>
                                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Beri Rating Pengajaran Mentor:</label>
                                    <div class="star-rating-options">
                                        @foreach([5 => '5 ★', 4 => '4 ★', 3 => '3 ★', 2 => '2 ★', 1 => '1 ★'] as $val => $text)
                                            <div class="star-option-item">
                                                <input type="radio" name="mentor_rating" id="m_new_{{ $enrollment->id }}_{{ $val }}" value="{{ $val }}" {{ $val === 5 ? 'checked' : '' }} required>
                                                <label for="m_new_{{ $enrollment->id }}_{{ $val }}" style="padding:4px 8px; font-size:11.5px;">
                                                    {{ $text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label style="display:block; font-size:11.5px; font-weight:700; color:#334155; margin-bottom:4px;">Ulasan Pembelajaran (Opsional):</label>
                                    <textarea name="mentor_review" class="review-textarea-custom" rows="2" style="font-size:12px;" placeholder="Bagikan kesan bagaimana mentor membimbing anak Anda..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-soft btn-sm" style="align-self:flex-start; font-size:12px;">
                                    <x-icon name="star" style="width:13px; height:13px;" /> Kirim Ulasan Mentor
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty state when child has no enrolled courses with mentors -->
            <div class="review-empty-state">
                <div class="review-empty-icon">
                    <x-icon name="review" />
                </div>
                <h4 class="review-empty-title">Belum Ada Mentor yang Dapat Diulas</h4>
                <p class="review-empty-desc">
                    Ulasan mentor hanya dapat diberikan jika anak Anda sudah mengikuti atau terdaftar pada kelas mentor tersebut. Daftarkan anak ke kelas favorit untuk mulai belajar bersama mentor berpengalaman.
                </p>
                <a href="{{ route('mentors.index') }}" class="btn btn-primary btn-sm" style="padding:8px 20px;">
                    <x-icon name="users" style="width:14px; height:14px;" /> Temukan Mentor & Kelas
                </a>
            </div>
        @endif
    </div>
</section>

<script>
    function toggleEdit(showId, hideId) {
        const showElem = document.getElementById(showId);
        const hideElem = document.getElementById(hideId);
        if (showElem) showElem.style.display = 'block';
        if (hideElem) hideElem.style.display = 'none';
    }

    function copyScheduleReminder(course, child, time, room) {
        const text = `🔔 *PENGINGAT KELAS SKILLPATH HARI INI*\n\n📚 Kursus: ${course}\n👦 Siswa: ${child}\n⏰ Waktu: ${time}\n📍 Ruang/Lokasi: ${room}\n\nSemoga belajarnya menyenangkan dan bermanfaat! ✨`;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Pengingat jadwal berhasil disalin ke clipboard:\n\n' + text);
            }).catch(() => {
                prompt('Salin info jadwal berikut:', text);
            });
        } else {
            prompt('Salin info jadwal berikut:', text);
        }
    }
</script>
@endsection

