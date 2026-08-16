@extends('layouts.admin')
@section('title', 'Statistik Platform')

@section('content')
<section class="admin-analytics-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">DATA & INSIGHTS</span>
            <h1>Statistik & Analisis Platform</h1>
            <p>Eksplorasi performa distribusi kategori minat bakat anak, konversi pembayaran, dan pertumbuhan ekosistem SkillPath.</p>
        </div>
    </div>

    <!-- 3 Stat Cards Summary -->
    <div class="admin-stat-grid" style="margin-bottom: 24px;">
        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-purple-admin">
                <x-icon name="users" />
            </div>
            <div class="admin-stat-data">
                <span class="label">TOTAL PENGGUNA TERDAFTAR</span>
                <b class="value">{{ $totalUsers }}</b>
                <small class="desc">Orang tua, mentor, & admin</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-green-admin">
                <x-icon name="receipt" />
            </div>
            <div class="admin-stat-data">
                <span class="label">KONVERSI PEMBAYARAN</span>
                <b class="value">{{ $conversionRate }}%</b>
                <small class="desc">{{ $paidTransactions }} lunas dari {{ $totalTransactions }} invoice</small>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-icon tone-blue-admin">
                <x-icon name="analytics" />
            </div>
            <div class="admin-stat-data">
                <span class="label">STATUS EKOSISTEM</span>
                <b class="value">Stabil & Aktif</b>
                <small class="desc">Offline hub operasional normal</small>
            </div>
        </article>
    </div>

    <!-- Category Popularity Breakdown -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <span class="kicker">DISTRIBUSI KATEGORI</span>
                <h2>Sebaran Program Belajar per Bidang</h2>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:18px;">
            @foreach($categoryStats as $cat)
                <div style="background:#f8f9fd; border:1px solid #eaedf6; border-radius:16px; padding:18px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <b style="font-size:15px; color:#120e2e;">{{ $cat['name'] }}</b>
                        <span class="status-pill paid" style="font-size:10px;">{{ $cat['course_percent'] }}% Katalog</span>
                    </div>

                    <div class="meter-track" style="height:6px; margin-bottom:14px;">
                        <div class="meter-fill" style="width: {{ $cat['course_percent'] }}%;"></div>
                    </div>

                    <div style="display:flex; justify-content:space-between; font-size:11.5px; color:#6d6790;">
                        <span style="display:inline-flex; align-items:center; gap:5px;"><x-icon name="book" style="width:13px; height:13px; color:#5b36f5;" /> {{ $cat['courses_count'] }} Course</span>
                        <span style="display:inline-flex; align-items:center; gap:5px;"><x-icon name="person" style="width:13px; height:13px; color:#3b82f6;" /> {{ $cat['mentors_count'] }} Mentor</span>
                        <span style="display:inline-flex; align-items:center; gap:5px;"><x-icon name="child" style="width:13px; height:13px; color:#22c55e;" /> {{ $cat['enrollments_count'] }} Siswa</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
