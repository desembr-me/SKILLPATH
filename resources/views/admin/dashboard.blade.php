@extends('admin.layouts.app')

@section('title', 'Dashboard Admin | SKILLPATH')
@section('page-title', 'Dashboard')

@section('content')
<div class="admin-welcome">
    <div>
        <span class="admin-eyebrow">Ringkasan platform</span>
        <h2>Selamat datang, {{ auth()->user()->name }}.</h2>
        <p>Pantau course, transaksi, pengajar, pengguna, dan aktivitas platform dari satu halaman.</p>
    </div>
    <div class="admin-head-actions"><a class="admin-btn primary" href="{{ route('admin.courses.create') }}">+ Tambah Course</a><a class="admin-btn outline" href="{{ route('admin.courses.index') }}">Kelola Course</a></div>
</div>

<div class="admin-stat-grid">
    <article class="admin-stat-card accent-yellow">
        <span class="admin-stat-label">Total Course</span>
        <strong>{{ number_format($stats['courses']) }}</strong>
        <small>{{ number_format($stats['published_courses']) }} dipublikasikan</small>
    </article>
    <article class="admin-stat-card accent-blue">
        <span class="admin-stat-label">Pengguna Orang Tua</span>
        <strong>{{ number_format($stats['students']) }}</strong>
        <small>{{ number_format($stats['enrollments']) }} enrollment aktif</small>
    </article>
    <article class="admin-stat-card accent-green">
        <span class="admin-stat-label">Pengajar</span>
        <strong>{{ number_format($stats['instructors']) }}</strong>
        <small>{{ number_format($stats['categories']) }} kategori tersedia</small>
    </article>
    <article class="admin-stat-card accent-pink">
        <span class="admin-stat-label">Pendapatan Bulan Ini</span>
        <strong>Rp{{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</strong>
        <small>Total keseluruhan Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</small>
    </article>
</div>

<div class="admin-dashboard-grid">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Transaksi</span>
                <h2>Pesanan terbaru</h2>
            </div>
            <a href="{{ route('admin.orders.index') }}">Lihat semua →</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Pembeli</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td><a class="admin-table-link" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                        <td>{{ $order->user->name }}</td>
                        <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="status-badge {{ $order->payment_status }}">{{ strtoupper($order->payment_status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="admin-empty-cell">Belum ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Course</span>
                <h2>Paling banyak diikuti</h2>
            </div>
        </div>

        <div class="admin-course-rank">
            @forelse($popularCourses as $course)
                <div class="admin-rank-item">
                    <span class="admin-rank-icon">{{ $course->icon }}</span>
                    <div>
                        <strong>{{ $course->title }}</strong>
                        <small>{{ $course->instructor?->name ?? 'Belum ada pengajar' }}</small>
                    </div>
                    <span class="admin-rank-value">{{ $course->enrollments_count }}</span>
                </div>
            @empty
                <p class="admin-muted">Belum ada enrollment.</p>
            @endforelse
        </div>
    </section>
</div>

<section class="admin-panel admin-dashboard-schedule-panel">
    <div class="admin-panel-head">
        <div>
            <span class="admin-eyebrow">Jadwal pengajaran</span>
            <h2>Jadwal pengajaran terdekat</h2>
            <p>{{ $stats['teaching_today'] }} sesi hari ini · {{ $stats['upcoming_teaching'] }} sesi akan datang.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}">Kelola jadwal →</a>
    </div>

    <div class="dashboard-schedule-list">
        @forelse($nextSchedules as $session)
            <div class="dashboard-schedule-item">
                <div class="dashboard-schedule-date">
                    <strong>{{ $session->starts_at->format('d') }}</strong>
                    <span>{{ strtoupper($session->starts_at->translatedFormat('M')) }}</span>
                </div>
                <div>
                    <strong>{{ $session->title }}</strong>
                    <small>{{ $session->learningPath?->title ?? 'Course tidak tersedia' }} · {{ $session->instructor?->name ?? 'Pengajar tidak tersedia' }}</small>
                </div>
                <div class="dashboard-schedule-time">
                    <strong>{{ $session->starts_at->format('H:i') }}</strong>
                    <span class="status-badge {{ $session->status }}">{{ strtoupper($session->status) }}</span>
                </div>
            </div>
        @empty
            <p class="admin-muted">Belum ada jadwal pengajaran yang akan datang.</p>
        @endforelse
    </div>
</section>

<div class="admin-quick-grid">
    <a href="{{ route('admin.courses.create') }}" class="admin-quick-card"><span>＋</span><strong>Tambah Course</strong><small>6 kategori · 3 level</small></a><a href="{{ route('admin.courses.index') }}" class="admin-quick-card"><span>▤</span><strong>Course</strong><small>Publikasi dan katalog</small></a>
    <a href="{{ route('admin.instructors.index') }}" class="admin-quick-card"><span>♙</span><strong>Pengajar</strong><small>Verifikasi profil</small></a>
    <a href="{{ route('admin.progress.index') }}" class="admin-quick-card"><span>↗</span><strong>Progres Siswa</strong><small>{{ $stats['student_profiles'] }} siswa termonitor</small></a>
    <a href="{{ route('admin.schedules.index') }}" class="admin-quick-card"><span>◷</span><strong>Jadwal Pengajaran</strong><small>{{ $stats['upcoming_teaching'] }} sesi akan datang</small></a>
    <a href="{{ route('admin.revenue.index') }}" class="admin-quick-card"><span>Rp</span><strong>Laporan Pendapatan</strong><small>Analisis transaksi PAID</small></a>
    <a href="{{ route('admin.certificates.index') }}" class="admin-quick-card"><span>◇</span><strong>Sertifikat</strong><small>{{ $stats['certificates'] }} sertifikat aktif</small></a>
    <a href="{{ route('admin.statistics.index') }}" class="admin-quick-card"><span>▥</span><strong>Statistik Platform</strong><small>{{ $stats['active_students_30d'] }} siswa aktif 30 hari</small></a>
    <a href="{{ route('admin.orders.index') }}" class="admin-quick-card"><span>▣</span><strong>Pesanan</strong><small>Status transaksi</small></a>
    <a href="{{ route('admin.reviews.index') }}" class="admin-quick-card"><span>★</span><strong>Review</strong><small>{{ $stats['pending_reviews'] }} perlu moderasi</small></a>
    <a href="{{ route('admin.recycle-bin.index') }}" class="admin-quick-card"><span>♲</span><strong>Recycle Bin</strong><small>{{ $stats['recycle_bin'] }} data dapat dipulihkan</small></a>
</div>
@endsection
