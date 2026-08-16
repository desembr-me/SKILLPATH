@extends('layouts.app')
@section('title','Riwayat Pesanan & Tagihan')

@section('content')
<section class="dashboard-page orders-page-wrap">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Riwayat Transaksi</span>
            <h1>Pesanan & Tagihan Saya</h1>
            <p>Kelola status pembayaran, invoice resmi, dan akses kursus anak Anda.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('parent.dashboard') }}">
                <x-icon name="arrow-left" /> Dashboard
            </a>
            <a class="btn btn-primary" href="{{ route('explore.index') }}">
                <x-icon name="plus" /> Cari Kursus Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash success">
            <x-icon name="check" />
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flash error">
            <x-icon name="conflict" />
            <div>
                <strong>Perhatian:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tabs & Search Bar --}}
    <div class="orders-filter-bar">
        <div class="orders-tabs">
            <a href="{{ route('parent.orders', ['search' => $search]) }}" class="order-tab {{ empty($status) ? 'active' : '' }}">
                Semua <span class="tab-badge">{{ $allCount }}</span>
            </a>
            <a href="{{ route('parent.orders', ['status' => 'pending', 'search' => $search]) }}" class="order-tab {{ $status === 'pending' ? 'active' : '' }}">
                Menunggu Pembayaran <span class="tab-badge warning">{{ $pendingCount }}</span>
            </a>
            <a href="{{ route('parent.orders', ['status' => 'paid', 'search' => $search]) }}" class="order-tab {{ $status === 'paid' ? 'active' : '' }}">
                Lunas <span class="tab-badge success">{{ $paidCount }}</span>
            </a>
            <a href="{{ route('parent.orders', ['status' => 'cancelled', 'search' => $search]) }}" class="order-tab {{ $status === 'cancelled' ? 'active' : '' }}">
                Dibatalkan <span class="tab-badge">{{ $cancelledCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('parent.orders') }}" class="orders-search-form">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <div class="search-input-wrap">
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari invoice, kursus, atau anak..." class="form-control">
                @if($search)
                    <a href="{{ route('parent.orders', ['status' => $status]) }}" class="clear-search-btn" title="Reset pencarian">&times;</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Orders List Grid / Cards --}}
    <div class="orders-stack">
        @forelse($orders as $order)
            <div class="panel order-card {{ $order->status }}">
                <div class="order-card-header">
                    <div class="order-header-left">
                        <span class="invoice-tag"><x-icon name="receipt" /> {{ $order->invoice_code }}</span>
                        <span class="order-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                        @if($order->payment_method)
                            <span class="order-method-badge">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                        @endif
                    </div>
                    <div class="order-header-right">
                        @if($order->status === 'paid')
                            <span class="status-chip paid"><x-icon name="check" /> Lunas</span>
                        @elseif($order->status === 'pending')
                            <span class="status-chip pending"><x-icon name="clock" /> Menunggu Pembayaran</span>
                        @else
                            <span class="status-chip cancelled"><x-icon name="recycle-bin" /> Dibatalkan</span>
                        @endif
                    </div>
                </div>

                <div class="order-card-body">
                    <div class="order-thumb-box">
                        <x-course-art :course="$order->enrollment->course ?? null" />
                    </div>
                    <div class="order-course-info">
                        <span class="category-pill">{{ $order->enrollment->course->category->name ?? 'Kursus' }}</span>
                        <h3>{{ $order->enrollment->course->title ?? 'Kursus SkillPath' }}</h3>
                        
                        <div class="order-meta-row">
                            <span class="order-meta-item child-pill"><x-icon name="child" /> Siswa: <b>{{ $order->enrollment->child->name ?? '-' }}</b></span>
                            <span class="order-meta-item"><x-icon name="calendar" /> Hari {{ $order->enrollment->schedule->day_of_week ?? '-' }}, {{ substr($order->enrollment->schedule->start_time ?? '', 0, 5) }} - {{ substr($order->enrollment->schedule->end_time ?? '', 0, 5) }} WIB</span>
                            <span class="order-meta-item"><x-icon name="location" /> {{ $order->enrollment->course->location_name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="order-price-box">
                        <small>Total Tagihan</small>
                        <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="order-card-footer">
                    <div class="order-footer-left">
                        @if($order->status === 'pending')
                            <small class="text-muted"><x-icon name="clock" /> Bayar sebelum {{ $order->created_at->addHours(24)->format('d M Y, H:i') }} WIB</small>
                        @elseif($order->status === 'paid')
                            <small class="text-success"><x-icon name="check" /> Dibayar pada {{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : $order->updated_at->format('d M Y, H:i') }} WIB</small>
                        @endif
                    </div>
                    <div class="order-footer-actions">
                        @if($order->status === 'pending')
                            <a class="btn btn-primary" href="{{ route('parent.payment.show', $order) }}">
                                <x-icon name="wallet" /> Bayar Sekarang
                            </a>
                            <form method="POST" action="{{ route('parent.transactions.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm text-danger">Batalkan</button>
                            </form>
                        @elseif($order->status === 'paid')
                            <a class="btn btn-soft" href="{{ route('parent.payment.show', $order) }}">
                                <x-icon name="receipt" /> Lihat Invoice
                            </a>
                            <a class="btn btn-primary" href="{{ route('parent.my-courses') }}">
                                <x-icon name="book" /> Mulai Belajar
                            </a>
                        @else
                            <a class="btn btn-soft btn-sm" href="{{ route('parent.payment.show', $order) }}">
                                <x-icon name="eye" /> Lihat Detail
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="panel">
                <div class="empty-state">
                    <x-icon name="receipt" />
                    <h3>Belum Ada Pesanan yang Sesuai</h3>
                    <p>Pesanan akan muncul di sini setelah Anda melakukan checkout kursus.</p>
                    <a class="btn btn-primary" href="{{ route('explore.index') }}" style="margin-top: 14px;">
                        <x-icon name="search" /> Jelajahi Katalog Kursus
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="pagination-wrap">
            {{ $orders->links() }}
        </div>
    @endif
</section>
@endsection
