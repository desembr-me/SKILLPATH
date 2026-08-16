@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<section class="admin-dashboard-view">
    <!-- Header -->
    <div class="admin-header-row">
        <div class="admin-header-copy">
            <span class="eyebrow">DASHBOARD ADMIN</span>
            <h1>Pusat kendali SkillPath</h1>
            <p>Pantau operasional platform, kualitas layanan, aktivitas siswa, serta performa pendapatan dari satu halaman.</p>
        </div>
        <div class="admin-action-group">
            <a href="{{ route('admin.reports.index') }}" class="btn-admin-white">
                <x-icon name="report" />
                <span>Lihat Laporan</span>
            </a>
            <a href="{{ route('admin.courses.create') }}" class="btn-admin-primary">
                <x-icon name="plus" />
                <span>Tambah Course</span>
            </a>
        </div>
    </div>

    <!-- 4 Top Stat Cards -->
    <div class="admin-stat-grid">
        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-purple-admin">
                <x-icon name="users" />
            </div>
            <div class="admin-stat-data">
                <span class="label">ORANG TUA AKTIF</span>
                <b class="value">{{ $parentsCount }}</b>
                <small class="desc">Akun keluarga aktif</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-blue-admin">
                <x-icon name="person" />
            </div>
            <div class="admin-stat-data">
                <span class="label">PENGAJAR AKTIF</span>
                <b class="value">{{ $mentorsCount }}</b>
                <small class="desc">Mentor tersedia</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-green-admin">
                <x-icon name="book" />
            </div>
            <div class="admin-stat-data">
                <span class="label">COURSE AKTIF</span>
                <b class="value">{{ $coursesCount }}</b>
                <small class="desc">{{ $activeEnrollmentsCount }} enrollment aktif</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-orange-admin">
                <x-icon name="credit" />
            </div>
            <div class="admin-stat-data">
                <span class="label">PENDAPATAN BULAN INI</span>
                <b class="value">Rp{{ number_format($thisMonthRevenue, 0, ',', '.') }}</b>
                <small class="desc">{{ $thisMonthOrdersCount }} pesanan bulan ini</small>
            </div>
        </article>
    </div>

    <!-- Middle Section: Financial Trend & Quality Score -->
    <div class="admin-dashboard-row-mid">
        <!-- 6-Month Revenue Trend -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <span class="kicker">PERFORMA FINANSIAL</span>
                    <h2>Tren pendapatan 6 bulan</h2>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="admin-panel-link">
                    <span>Detail laporan</span>
                </a>
            </div>

            <div class="chart-bar-wrap">
                @foreach($revenueTrend as $trend)
                    <div class="chart-bar-col">
                        <span class="chart-bar-val">{{ $trend['label'] }}</span>
                        <div class="chart-bar-pillar" style="height: {{ $trend['height_percent'] }}%;" title="{{ $trend['name'] }}: {{ $trend['label'] }}"></div>
                        <span class="chart-bar-month">{{ $trend['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quality Score -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <span class="kicker">QUALITY SCORE</span>
                    <h2>Kualitas layanan</h2>
                </div>
            </div>

            <div class="quality-score-meters">
                <div class="quality-meter-item">
                    <div class="quality-meter-top">
                        <div class="quality-meter-info">
                            <b>Rating Mentor</b>
                            <small>Pengalaman belajar</small>
                        </div>
                        <span class="quality-meter-score">{{ $mentorRating }}</span>
                    </div>
                    <div class="meter-track">
                        <div class="meter-fill" style="width: {{ min(100, ((float)$mentorRating / 5) * 100) }}%;"></div>
                    </div>
                </div>

                <div class="quality-meter-item">
                    <div class="quality-meter-top">
                        <div class="quality-meter-info">
                            <b>Rating Platform</b>
                            <small>Sistem dan transaksi</small>
                        </div>
                        <span class="quality-meter-score">{{ $platformRating }}</span>
                    </div>
                    <div class="meter-track">
                        <div class="meter-fill" style="width: {{ min(100, ((float)$platformRating / 5) * 100) }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="admin-mini-kpi-grid">
                <div class="admin-mini-kpi">
                    <b>{{ $pendingOrdersCount }}</b>
                    <span>Pesanan pending</span>
                </div>
                <div class="admin-mini-kpi">
                    <b>{{ $activeCertificatesCount }}</b>
                    <span>Sertifikat aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Latest Orders & Low Progress Students -->
    <div class="admin-dashboard-row-bottom">
        <!-- Recent Orders -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <span class="kicker">TRANSAKSI</span>
                    <h2>Pesanan terbaru</h2>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="admin-panel-link">
                    <span>Kelola pesanan</span>
                </a>
            </div>

            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Course</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $trx)
                            <tr>
                                <td><b>{{ $trx->invoice_code }}</b></td>
                                <td>{{ $trx->parent->name ?? 'User' }}</td>
                                <td>{{ optional(optional($trx->enrollment)->course)->title ?? 'Paket Belajar' }}</td>
                                <td><b>Rp{{ number_format($trx->total, 0, ',', '.') }}</b></td>
                                <td>
                                    <span class="status-pill {{ $trx->status }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#8a84ab; padding:24px;">Belum ada riwayat pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Progress Students -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <span class="kicker">PERLU PERHATIAN</span>
                    <h2>Progress siswa rendah</h2>
                </div>
                <a href="{{ route('admin.students.index') }}" class="admin-panel-link">
                    <span>Lihat semua</span>
                </a>
            </div>

            <div class="student-progress-list">
                @forelse($studentsWithProgress as $item)
                    <div class="student-progress-card">
                        <div class="student-card-left">
                            <div class="student-avatar-box">
                                @if($item['child']->avatar_url)
                                    <img src="{{ $item['child']->avatar_url }}" alt="{{ $item['child']->name }}">
                                @else
                                    {{ $item['child']->initial }}
                                @endif
                            </div>
                            <div class="student-card-info">
                                <h4>{{ $item['child']->name }}</h4>
                                <p>{{ $item['course']->title }} • Mentor: {{ $item['course']->instructor->name ?? 'SkillPath' }}</p>
                            </div>
                        </div>
                        <div class="student-progress-track">
                            <b>{{ $item['percent'] }}%</b>
                            <small>Perlu dorongan</small>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; color:#8a84ab; padding:24px;">
                        Semua siswa memiliki progres belajar yang baik.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
