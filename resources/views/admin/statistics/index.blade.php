@extends('admin.layouts.app')

@section('title', 'Statistik Platform | Admin SKILLPATH')
@section('page-title', 'Statistik Platform')

@section('content')
<div class="admin-welcome statistics-welcome">
    <div>
        <span class="admin-eyebrow">Analitik platform</span>
        <h2>Pahami pertumbuhan dan aktivitas SKILLPATH.</h2>
        <p>Statistik menggabungkan data pengguna, enrollment, aktivitas belajar, course, sertifikat, live class, review, dan transaksi.</p>
    </div>

    <a class="admin-btn secondary" href="{{ route('admin.statistics.export', ['period' => $period]) }}">Ekspor CSV</a>
</div>

<section class="admin-panel statistics-period-panel">
    <form method="GET" class="statistics-period-form">
        <div>
            <strong>Periode analisis</strong>
            <small>{{ $periodLabel }} · {{ $from->translatedFormat('d M Y') }} sampai {{ $to->translatedFormat('d M Y') }}</small>
        </div>

        <select name="period" onchange="this.form.submit()">
            <option value="7d" @selected($period === '7d')>7 hari</option>
            <option value="30d" @selected($period === '30d')>30 hari</option>
            <option value="90d" @selected($period === '90d')>90 hari</option>
            <option value="12m" @selected($period === '12m')>12 bulan</option>
            <option value="all" @selected($period === 'all')>Semua waktu</option>
        </select>
    </form>
</section>

<div class="admin-stat-grid statistics-main-stats">
    <article class="admin-stat-card accent-blue">
        <span class="admin-stat-label">Pengguna</span>
        <strong>{{ number_format($metrics['users']) }}</strong>
        <small>{{ number_format($metrics['parents']) }} orang tua · {{ number_format($metrics['instructors']) }} pengajar</small>
    </article>

    <article class="admin-stat-card accent-yellow">
        <span class="admin-stat-label">Siswa & Enrollment</span>
        <strong>{{ number_format($metrics['children']) }}</strong>
        <small>{{ number_format($metrics['enrollments']) }} total enrollment</small>
    </article>

    <article class="admin-stat-card accent-green">
        <span class="admin-stat-label">Siswa Aktif Periode Ini</span>
        <strong>{{ number_format($metrics['active_students_period']) }}</strong>
        <small>{{ number_format($metrics['completed_activities_period']) }} aktivitas selesai</small>
    </article>

    <article class="admin-stat-card accent-pink">
        <span class="admin-stat-label">Pendapatan Periode Ini</span>
        <strong>Rp{{ number_format($metrics['revenue_period'], 0, ',', '.') }}</strong>
        <small>{{ number_format($metrics['paid_orders_period']) }} pesanan dibayar</small>
    </article>

    <article class="admin-stat-card accent-green">
        <span class="admin-stat-label">Sertifikat Aktif</span>
        <strong>{{ number_format($metrics['certificates_active']) }}</strong>
        <small>{{ number_format($metrics['certificates_period']) }} terbit pada periode ini</small>
    </article>

    <article class="admin-stat-card accent-blue">
        <span class="admin-stat-label">Completion Rate</span>
        <strong>{{ number_format($metrics['completion_rate'], 1) }}%</strong>
        <small>Sertifikat aktif dibanding seluruh enrollment</small>
    </article>

    <article class="admin-stat-card accent-yellow">
        <span class="admin-stat-label">Rating Platform</span>
        <strong>{{ number_format($metrics['average_rating'], 2) }}</strong>
        <small>Rata-rata review yang disetujui</small>
    </article>

    <article class="admin-stat-card accent-pink">
        <span class="admin-stat-label">Course Aktif</span>
        <strong>{{ number_format($metrics['published_courses']) }}</strong>
        <small>Course yang dipublikasikan</small>
    </article>
</div>

<section class="admin-panel statistics-trend-panel">
    <div class="admin-panel-head">
        <div>
            <span class="admin-eyebrow">Tren</span>
            <h2>Aktivitas platform</h2>
            <p>Bandingkan pengguna baru, enrollment, aktivitas selesai, dan pendapatan pada periode yang sama.</p>
        </div>
    </div>

    <div class="statistics-legend">
        <span><i class="legend-users"></i>Pengguna baru</span>
        <span><i class="legend-enrollments"></i>Enrollment</span>
        <span><i class="legend-activities"></i>Aktivitas selesai</span>
        <span><i class="legend-revenue"></i>Pendapatan</span>
    </div>

    <div class="statistics-trend-chart">
        @foreach($trend['points'] as $point)
            <div class="statistics-trend-column">
                <div class="statistics-trend-bars">
                    <span
                        class="stat-bar users"
                        style="height: {{ max(3, ($point['users'] / $trend['max_users']) * 100) }}%"
                        title="Pengguna baru: {{ $point['users'] }}"
                    ></span>
                    <span
                        class="stat-bar enrollments"
                        style="height: {{ max(3, ($point['enrollments'] / $trend['max_enrollments']) * 100) }}%"
                        title="Enrollment: {{ $point['enrollments'] }}"
                    ></span>
                    <span
                        class="stat-bar activities"
                        style="height: {{ max(3, ($point['activities'] / $trend['max_activities']) * 100) }}%"
                        title="Aktivitas selesai: {{ $point['activities'] }}"
                    ></span>
                    <span
                        class="stat-bar revenue"
                        style="height: {{ max(3, ($point['revenue'] / $trend['max_revenue']) * 100) }}%"
                        title="Pendapatan: Rp{{ number_format($point['revenue'], 0, ',', '.') }}"
                    ></span>
                </div>
                <small>{{ $point['label'] }}</small>
            </div>
        @endforeach
    </div>
</section>

<div class="statistics-two-column">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Funnel belajar</span>
                <h2>Perjalanan siswa</h2>
            </div>
        </div>

        <div class="statistics-horizontal-list">
            @foreach($funnel as $item)
                <div class="statistics-horizontal-item">
                    <div>
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ number_format($item['value']) }}</strong>
                    </div>
                    <div class="statistics-horizontal-track">
                        <span style="width: {{ ($item['value'] / $maxFunnel) * 100 }}%"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Demografi</span>
                <h2>Distribusi usia</h2>
            </div>
        </div>

        <div class="statistics-horizontal-list age">
            @foreach($ageDistribution as $item)
                <div class="statistics-horizontal-item">
                    <div>
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ number_format($item['count']) }}</strong>
                    </div>
                    <div class="statistics-horizontal-track">
                        <span style="width: {{ ($item['count'] / $maxAgeCount) * 100 }}%"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<div class="statistics-two-column">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Kategori</span>
                <h2>Kategori paling diminati</h2>
            </div>
        </div>

        <div class="statistics-horizontal-list category">
            @forelse($categoryPopularity as $category)
                <div class="statistics-horizontal-item">
                    <div>
                        <span>{{ $category->icon }} {{ $category->name }}</span>
                        <strong>{{ number_format($category->enrollment_count) }}</strong>
                    </div>
                    <div class="statistics-horizontal-track">
                        <span style="width: {{ ($category->enrollment_count / $maxCategoryEnrollments) * 100 }}%"></span>
                    </div>
                </div>
            @empty
                <p class="admin-muted">Belum ada data kategori.</p>
            @endforelse
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Engagement</span>
                <h2>Interaksi periode ini</h2>
            </div>
        </div>

        <div class="statistics-engagement-grid">
            <div>
                <span>Pengguna baru</span>
                <strong>{{ number_format($engagement['new_users']) }}</strong>
            </div>
            <div>
                <span>Enrollment baru</span>
                <strong>{{ number_format($engagement['new_enrollments']) }}</strong>
            </div>
            <div>
                <span>Live class</span>
                <strong>{{ number_format($engagement['live_sessions']) }}</strong>
            </div>
            <div>
                <span>Booking live</span>
                <strong>{{ number_format($engagement['live_bookings']) }}</strong>
            </div>
            <div>
                <span>Keterisian live</span>
                <strong>{{ number_format($engagement['live_fill_rate'], 1) }}%</strong>
            </div>
            <div>
                <span>Review disetujui</span>
                <strong>{{ number_format($engagement['approved_reviews']) }}</strong>
            </div>
        </div>
    </section>
</div>

<div class="statistics-two-column">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Course</span>
                <h2>Course terpopuler</h2>
            </div>
            <a href="{{ route('admin.courses.index') }}">Kelola course →</a>
        </div>

        <div class="statistics-ranking-list">
            @forelse($topCourses as $course)
                <div class="statistics-ranking-item">
                    <span class="statistics-rank-number">{{ $loop->iteration }}</span>
                    <div>
                        <strong>{{ $course->title }}</strong>
                        <small>{{ $course->instructor?->name ?? 'Belum ada pengajar' }}</small>
                    </div>
                    <div class="statistics-rank-value">
                        <strong>{{ number_format($course->enrollments_count) }}</strong>
                        <small>enrollment · ★ {{ number_format((float) ($course->reviews_avg_rating ?? 0), 1) }}</small>
                    </div>
                </div>
            @empty
                <p class="admin-muted">Belum ada enrollment.</p>
            @endforelse
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Pengajar</span>
                <h2>Kontribusi pengajar</h2>
            </div>
            <a href="{{ route('admin.instructors.index') }}">Lihat pengajar →</a>
        </div>

        <div class="statistics-ranking-list">
            @forelse($topInstructors as $instructor)
                <div class="statistics-ranking-item">
                    <span class="statistics-rank-number">{{ $loop->iteration }}</span>
                    <div>
                        <strong>{{ $instructor->name }}</strong>
                        <small>{{ $instructor->instructorProfile?->headline ?? 'Pengajar SKILLPATH' }}</small>
                    </div>
                    <div class="statistics-rank-value">
                        <strong>{{ number_format($instructor->enrollment_count) }}</strong>
                        <small>{{ number_format($instructor->course_count) }} course</small>
                    </div>
                </div>
            @empty
                <p class="admin-muted">Belum ada pengajar.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
