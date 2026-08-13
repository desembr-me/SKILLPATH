@extends('admin.layouts.app')

@section('title', 'Pesanan | Admin SKILLPATH')
@section('page-title', 'Pesanan')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Transaksi marketplace</span>
            <h2>Riwayat pesanan</h2>
            <p>Pantau pembayaran dan status pemrosesan transaksi.</p>
        </div>
        <span class="admin-total-pill">{{ $orders->total() }} pesanan</span>
    </div>

    <form class="admin-filter-bar" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nomor pesanan, nama, atau email...">
        <select name="payment_status">
            <option value="">Semua pembayaran</option>
            <option value="pending" @selected(request('payment_status') === 'pending')>Pending</option>
            <option value="paid" @selected(request('payment_status') === 'paid')>Paid</option>
            <option value="failed" @selected(request('payment_status') === 'failed')>Failed</option>
            <option value="refunded" @selected(request('payment_status') === 'refunded')>Refunded</option>
        </select>
        <button class="admin-btn dark" type="submit">Terapkan</button>
        <a class="admin-text-link" href="{{ route('admin.orders.index') }}">Reset</a>
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table wide">
            <thead><tr><th>Pesanan</th><th>Pembeli</th><th>Item</th><th>Total</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong><small class="table-subtext">{{ $order->created_at->format('d M Y H:i') }}</small></td>
                    <td>{{ $order->user->name }}<small class="table-subtext">{{ $order->user->email }}</small></td>
                    <td>{{ $order->items_count }}</td>
                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                    <td><span class="status-badge {{ $order->payment_status }}">{{ strtoupper($order->payment_status) }}</span></td>
                    <td><span class="status-badge neutral">{{ strtoupper($order->status) }}</span></td>
                    <td><a class="admin-icon-btn" href="{{ route('admin.orders.show', $order) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="admin-empty-cell">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-pagination">{{ $orders->links() }}</div>
</section>
@endsection
