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
            @php
                $allEnrollments = $order->all_enrollments;
                $isMulti = $allEnrollments->count() > 1 || (!empty($order->metadata['items']) && count($order->metadata['items']) > 1);
                $itemCount = max($allEnrollments->count(), !empty($order->metadata['items']) ? count($order->metadata['items']) : 1);
            @endphp
            <div class="panel order-card {{ $order->status }}">
                <div class="order-card-header">
                    <div class="order-header-left">
                        <span class="invoice-tag"><x-icon name="receipt" /> {{ $order->invoice_code }}</span>
                        <span class="order-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                        @if($order->payment_method)
                            <span class="order-method-badge">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                        @endif
                        @if($isMulti)
                            <span class="order-bundle-pill"><x-icon name="spark" /> {{ $itemCount }} Kursus (1x Bayar)</span>
                        @endif
                    </div>
                    <div class="order-header-right">
                        @if($order->status === 'paid')
                            <span class="status-chip paid"><x-icon name="check" /> Lunas</span>
                        @elseif($order->status === 'pending')
                            <span class="status-chip pending"><x-icon name="clock" /> Menunggu 1x Pembayaran</span>
                        @else
                            <span class="status-chip cancelled"><x-icon name="recycle-bin" /> Dibatalkan</span>
                        @endif
                    </div>
                </div>

                @if($isMulti)
                    {{-- Multi-Course Group Layout --}}
                    <div class="order-card-body multi" style="flex-direction: column; gap: 16px;">
                        <div class="multi-course-list">
                            @if($allEnrollments->isNotEmpty())
                                @foreach($allEnrollments as $enr)
                                    <div class="multi-course-item">
                                        <div class="multi-course-thumb">
                                            <x-course-art :course="$enr->course" />
                                        </div>
                                        <div class="multi-course-detail">
                                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                <span class="category-pill" style="font-size: 10px; padding: 2px 8px;">{{ $enr->course->category->name ?? 'Kursus' }}</span>
                                                <span class="status-chip soft" style="font-size: 10px; padding: 2px 8px;">
                                                    {{ $enr->package_info['title'] ?? 'Paket Pilihan' }} ({{ $enr->package_info['sessions'] ?? $enr->total_sessions }} Sesi)
                                                </span>
                                            </div>
                                            <h4>{{ $enr->course->title }}</h4>
                                            <div class="order-meta-row">
                                                <span class="order-meta-item child-pill"><x-icon name="child" /> Siswa: <b>{{ $enr->child->name ?? '-' }}</b></span>
                                                <span class="order-meta-item"><x-icon name="calendar" /> Hari {{ $enr->schedule->day_name ?? '-' }}, {{ substr($enr->schedule->start_time ?? '', 0, 5) }} - {{ substr($enr->schedule->end_time ?? '', 0, 5) }} WIB</span>
                                                <span class="order-meta-item"><x-icon name="location" /> {{ $enr->course->location_name ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="multi-course-price">
                                            <small style="display:block; font-size:10px; color:var(--muted);">Biaya Paket</small>
                                            <b>Rp{{ number_format($enr->package_info['price'] ?? 0, 0, ',', '.') }}</b>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif(!empty($order->metadata['items']))
                                @foreach($order->metadata['items'] as $itemMeta)
                                    <div class="multi-course-item">
                                        <div class="multi-course-thumb">
                                            <div class="course-art">📚</div>
                                        </div>
                                        <div class="multi-course-detail">
                                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                <span class="category-pill" style="font-size: 10px; padding: 2px 8px;">{{ $itemMeta['category_name'] ?? 'Kursus' }}</span>
                                                <span class="status-chip soft" style="font-size: 10px; padding: 2px 8px;">
                                                    {{ $itemMeta['package_title'] ?? 'Paket' }} ({{ $itemMeta['package_sessions'] ?? 12 }} Sesi)
                                                </span>
                                            </div>
                                            <h4>{{ $itemMeta['course_title'] ?? 'Kursus' }}</h4>
                                            <div class="order-meta-row">
                                                <span class="order-meta-item child-pill"><x-icon name="child" /> Siswa: <b>{{ $itemMeta['child_name'] ?? '-' }}</b></span>
                                                <span class="order-meta-item"><x-icon name="calendar" /> Hari {{ $itemMeta['schedule_day'] ?? '-' }}, {{ $itemMeta['schedule_time'] ?? '-' }}</span>
                                                <span class="order-meta-item"><x-icon name="location" /> {{ $itemMeta['location'] ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="multi-course-price">
                                            <small style="display:block; font-size:10px; color:var(--muted);">Biaya Paket</small>
                                            <b>Rp{{ number_format($itemMeta['price'] ?? 0, 0, ',', '.') }}</b>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div style="display: flex; justify-content: flex-end; align-items: center; padding-top: 10px; border-top: 1px dashed #e2e8f0; width: 100%;">
                            <div class="order-price-box">
                                <small>Total Tagihan ({{ $itemCount }} Kursus)</small>
                                <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Single Course Layout --}}
                    @php
                        $primaryEnr = $allEnrollments->first() ?: $order->enrollment;
                    @endphp
                    <div class="order-card-body">
                        <div class="order-thumb-box">
                            <x-course-art :course="$primaryEnr->course ?? null" />
                        </div>
                        <div class="order-course-info">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                <span class="category-pill">{{ $primaryEnr->course->category->name ?? 'Kursus' }}</span>
                                @if(isset($order->metadata['package_title']))
                                    <span class="status-chip recommended" style="font-size: 10px;">
                                        {{ $order->metadata['package_title'] }} ({{ $order->metadata['package_sessions'] ?? ($primaryEnr->total_sessions ?? 12) }} Sesi)
                                    </span>
                                @elseif($primaryEnr && $primaryEnr->package_info)
                                    <span class="status-chip soft" style="font-size: 10px;">
                                        {{ $primaryEnr->package_info['title'] }} ({{ $primaryEnr->package_info['sessions'] }} Sesi)
                                    </span>
                                @endif
                            </div>
                            <h3>{{ $primaryEnr->course->title ?? $order->metadata['course_title'] ?? 'Kursus SkillPath' }}</h3>
                            
                            <div class="order-meta-row">
                                <span class="order-meta-item child-pill"><x-icon name="child" /> Siswa: <b>{{ $primaryEnr->child->name ?? $order->metadata['child_name'] ?? '-' }}</b></span>
                                <span class="order-meta-item"><x-icon name="calendar" /> Hari {{ $primaryEnr->schedule->day_name ?? $order->metadata['schedule_day'] ?? '-' }}, {{ substr($primaryEnr->schedule->start_time ?? '', 0, 5) }} - {{ substr($primaryEnr->schedule->end_time ?? '', 0, 5) }} WIB</span>
                                <span class="order-meta-item"><x-icon name="location" /> {{ $primaryEnr->course->location_name ?? $order->metadata['location'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="order-price-box">
                            <small>Total Tagihan</small>
                            <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @endif

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
                                <x-icon name="wallet" /> Bayar Sekarang (1x Bayar)
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
                <div class="empty-state empty-state-full">
                    <div class="empty-state-icon-wrap">
                        <x-icon name="receipt" />
                    </div>
                    <h3>Belum Ada Pesanan yang Sesuai</h3>
                    <p>Pesanan akan muncul di sini setelah Anda melakukan checkout kursus untuk anak.</p>
                    <div class="empty-state-actions">
                        <a class="btn btn-primary" href="{{ route('explore.index') }}">
                            <x-icon name="search" /> Jelajahi Katalog Kursus
                        </a>
                    </div>
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
