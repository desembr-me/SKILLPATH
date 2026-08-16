@extends('layouts.admin')
@section('title', 'Manajemen Pesanan')

@section('content')
<section class="admin-orders-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">TRANSAKSI PLATFORM</span>
            <h1>Manajemen Pesanan</h1>
            <p>Pantau seluruh riwayat transaksi pemesanan kelas oleh orang tua, status pembayaran, dan total omset.</p>
        </div>
        <div class="admin-action-group">
            <div class="admin-mini-kpi" style="padding:10px 18px; border-radius:12px; background:#fff;">
                <span style="font-size:10px; font-weight:900; color:#8a84ab;">TOTAL OMSET SUKSES</span>
                <b style="font-size:18px; color:#166534;">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</b>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.orders.index', ['status' => 'all', 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'all' ? 'active' : '' }}">
                    Semua ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'paid', 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'paid' ? 'active' : '' }}">
                    Lunas ({{ $paidCount }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending', 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'pending' ? 'active' : '' }}">
                    Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled', 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'cancelled' ? 'active' : '' }}">
                    Batal ({{ $cancelledCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="admin-search-input">
                @if($currentStatus !== 'all')
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari invoice, nama orang tua...">
                @if($search)
                    <a href="{{ route('admin.orders.index', ['status' => $currentStatus]) }}" style="color:#8a84ab; font-size:11px; font-weight:800;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Orang Tua</th>
                        <th>Siswa (Anak)</th>
                        <th>Course & Sesi</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Ubah Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <b style="color:#120e2e;">{{ $order->invoice_code }}</b>
                                <small style="color:#8a84ab; display:block;">ID #{{ $order->id }}</small>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ $order->parent->name ?? 'User' }}</b>
                                <small style="color:#8a84ab;">{{ $order->parent->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#5c567e;">{{ optional(optional($order->enrollment)->child)->name ?? '-' }}</span>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ optional(optional($order->enrollment)->course)->title ?? 'Course Belajar' }}</b>
                                <small style="color:#8a84ab;">{{ optional(optional($order->enrollment)->course)->sessions_count ?? '-' }} Pertemuan</small>
                            </td>
                            <td>
                                <b style="color:#120e2e; font-size:13.5px;">Rp{{ number_format($order->total, 0, ',', '.') }}</b>
                            </td>
                            <td>
                                <span class="status-pill {{ $order->status }}">
                                    {{ $order->status === 'paid' ? 'Lunas' : ($order->status === 'pending' ? 'Pending' : 'Batal') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11.5px; color:#8a84ab;">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" style="display:inline-flex; align-items:center; gap:6px;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" style="padding:4px 8px; border-radius:8px; font-size:11px; font-weight:700; border:1px solid #dcdce8; background:#fff;">
                                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Set Lunas</option>
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Set Pending</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Set Batal</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada riwayat pesanan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
