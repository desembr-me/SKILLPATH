@extends('layouts.instructor')
@section('title','Pendapatan Pengajar | SKILLPATH')
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Dashboard Pengajar</span>
    <h1>Pendapatan Saya</h1>
    <p>Ringkasan pendapatan dari penjualan course yang Anda ampu.</p>
</div>

<div class="stat-grid" style="margin-top: 0; grid-template-columns: repeat(3, 1fr);">
    <article class="stat-card">
        <span>Total Pendapatan</span>
        <strong>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</strong>
    </article>
    <article class="stat-card">
        <span>Total Penjualan</span>
        <strong>{{ $revenueByCourse->sum('sales') }}</strong>
    </article>
    <article class="stat-card">
        <span>Pendapatan Tertinggi Dari</span>
        <strong style="font-size: 22px;">{{ $revenueByCourse->first()->title ?? '-' }}</strong>
    </article>
</div>

<div class="two-column-section">
    <div class="content-card">
        <h2>Pendapatan per Course</h2>
        @forelse($revenueByCourse as $row)
            <div class="summary-line">
                <span>{{ $row->title }} <small>({{ $row->sales }}x)</small></span>
                <strong>Rp{{ number_format($row->revenue, 0, ',', '.') }}</strong>
            </div>
        @empty
            <p>Belum ada penjualan.</p>
        @endforelse
    </div>

    <div class="content-card">
        <h2>Pendapatan Bulanan</h2>
        @forelse($monthlyRevenue as $month)
            <div class="summary-line">
                <span>{{ \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->translatedFormat('F Y') }}</span>
                <strong>Rp{{ number_format($month->total, 0, ',', '.') }}</strong>
            </div>
        @empty
            <p>Belum ada data bulanan.</p>
        @endforelse
    </div>
</div>

<div class="section-heading" style="margin-top: 34px;"><h2>Penjualan Terbaru</h2></div>
<div class="order-list">
    @forelse($recentSales as $item)
        <div class="order-card">
            <div>
                <small>{{ $item->order->order_number }}</small>
                <h2>{{ $item->title_snapshot }}</h2>
                <span>{{ $item->order->user->name }} · {{ $item->created_at->format('d M Y H:i') }}</span>
            </div>
            <div>
                <strong>Rp{{ number_format($item->final_price, 0, ',', '.') }}</strong>
            </div>
        </div>
    @empty
        <div class="empty-card"><h2>Belum ada penjualan.</h2></div>
    @endforelse
</div>
@endsection
