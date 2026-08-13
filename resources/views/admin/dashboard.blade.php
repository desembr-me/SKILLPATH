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
    <a class="admin-btn primary" href="{{ route('admin.courses.index') }}">Kelola Course</a>
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
        <span class="admin-stat-label">Pendapatan Demo</span>
        <strong>Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</strong>
        <small>{{ number_format($stats['paid_orders']) }} pesanan dibayar</small>
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

<div class="admin-quick-grid">
    <a href="{{ route('admin.courses.index') }}" class="admin-quick-card"><span>▤</span><strong>Course</strong><small>Publikasi dan katalog</small></a>
    <a href="{{ route('admin.instructors.index') }}" class="admin-quick-card"><span>♙</span><strong>Pengajar</strong><small>Verifikasi profil</small></a>
    <a href="{{ route('admin.orders.index') }}" class="admin-quick-card"><span>▣</span><strong>Pesanan</strong><small>Status transaksi</small></a>
    <a href="{{ route('admin.reviews.index') }}" class="admin-quick-card"><span>★</span><strong>Review</strong><small>{{ $stats['pending_reviews'] }} perlu moderasi</small></a>
    <a href="{{ route('admin.recycle-bin.index') }}" class="admin-quick-card"><span>♲</span><strong>Recycle Bin</strong><small>{{ $stats['recycle_bin'] }} data dapat dipulihkan</small></a>
</div>
@endsection
