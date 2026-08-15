@extends('layouts.app')
@section('title','Dashboard Admin')
@section('content')
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Dashboard Admin</span><h1>Ringkasan SkillPath</h1><p>Pantau pertumbuhan pengguna, transaksi, kualitas mentor, dan pengalaman platform.</p></div></div>
    <div class="stat-grid">
        <article><span class="stat-icon"><x-icon name="users" /></span><div><span>Orang tua</span><b>{{ $parents }}</b><small>Akun keluarga terdaftar</small></div></article>
        <article><span class="stat-icon"><x-icon name="co-design" /></span><div><span>Pengajar</span><b>{{ $mentors }}</b><small>Mentor di platform</small></div></article>
        <article><span class="stat-icon"><x-icon name="sessions" /></span><div><span>Course aktif</span><b>{{ $courses }}</b><small>Course tersedia</small></div></article>
        <article><span class="stat-icon"><x-icon name="payment" /></span><div><span>Transaksi</span><b>{{ $transactions }}</b><small>Total transaksi tercatat</small></div></article>
    </div>
    <div class="dashboard-grid">
        <div class="panel"><div class="panel-heading"><div><span class="panel-kicker">Quality monitoring</span><h2>Kualitas Layanan</h2></div></div><div class="rating-split"><article><span class="rating-label">Rating Mentor</span><b>{{ $mentorRating ?: '0.00' }}</b><div class="rating-stars"><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /></div><p>Menilai pengalaman belajar bersama pengajar.</p></article><article><span class="rating-label">Rating Platform</span><b>{{ $platformRating ?: '0.00' }}</b><div class="rating-stars"><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /><x-icon name="star" /></div><p>Menilai booking, jadwal, transaksi, dan sistem.</p></article></div></div>
        <div class="panel review-insight"><span class="principle-icon"><x-icon name="review" /></span><h2>Review dipisahkan</h2><p>Admin dapat mengidentifikasi apakah keluhan berasal dari pengalaman belajar bersama mentor atau dari sistem SkillPath.</p></div>
    </div>
    <div class="panel admin-table-panel"><div class="panel-heading"><div><span class="panel-kicker">Aktivitas finansial</span><h2>Transaksi Terbaru</h2></div></div><div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Orang Tua</th><th>Course</th><th>Total</th><th>Status</th></tr></thead><tbody>@foreach($latest as $trx)<tr><td>{{ $trx->invoice_code }}</td><td>{{ $trx->parent->name }}</td><td>{{ $trx->enrollment->course->title }}</td><td>Rp{{ number_format($trx->total,0,',','.') }}</td><td><span class="status-chip {{ $trx->status }}">{{ ucfirst($trx->status) }}</span></td></tr>@endforeach</tbody></table></div></div>
</section>
@endsection
