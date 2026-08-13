@extends('admin.layouts.app')

@section('title', 'Detail Pesanan | Admin SKILLPATH')
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="admin-breadcrumb"><a href="{{ route('admin.orders.index') }}">Pesanan</a><span>/</span><strong>{{ $order->order_number }}</strong></div>

<div class="admin-detail-grid">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div><span class="admin-eyebrow">Invoice</span><h2>{{ $order->order_number }}</h2></div>
            <span class="status-badge {{ $order->payment_status }}">{{ strtoupper($order->payment_status) }}</span>
        </div>

        <div class="admin-order-customer">
            <div><span>Pembeli</span><strong>{{ $order->user->name }}</strong><small>{{ $order->user->email }}</small></div>
            <div><span>Tanggal</span><strong>{{ $order->created_at->format('d M Y') }}</strong><small>{{ $order->created_at->format('H:i') }}</small></div>
            <div><span>Metode pembayaran</span><strong>{{ $order->payment_method ?: '-' }}</strong><small>{{ $order->paid_at ? 'Dibayar '.$order->paid_at->format('d M Y H:i') : 'Belum dibayar' }}</small></div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Kelas</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td><strong>{{ $item->title_snapshot }}</strong></td>
                        <td>Rp{{ number_format($item->price,0,',','.') }}</td>
                        <td>Rp{{ number_format($item->discount,0,',','.') }}</td>
                        <td>Rp{{ number_format($item->final_price,0,',','.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-order-total">
            <div><span>Subtotal</span><strong>Rp{{ number_format($order->subtotal,0,',','.') }}</strong></div>
            <div><span>Diskon</span><strong>Rp{{ number_format($order->discount,0,',','.') }}</strong></div>
            <div class="grand"><span>Total</span><strong>Rp{{ number_format($order->total,0,',','.') }}</strong></div>
        </div>
    </section>

    <aside class="admin-panel admin-side-form">
        <span class="admin-eyebrow">Administrasi</span>
        <h2>Ubah status</h2>
        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
            @csrf
            @method('PATCH')
            <label><span>Status pembayaran</span><select name="payment_status" required>
                @foreach(['pending'=>'Pending','paid'=>'Paid','failed'=>'Failed','refunded'=>'Refunded'] as $value=>$label)
                    <option value="{{ $value }}" @selected($order->payment_status === $value)>{{ $label }}</option>
                @endforeach
            </select></label>
            <label><span>Status pesanan</span><select name="status" required>
                @foreach(['pending'=>'Pending','processing'=>'Processing','completed'=>'Completed','cancelled'=>'Cancelled'] as $value=>$label)
                    <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                @endforeach
            </select></label>
            <button class="admin-btn primary full" type="submit">Simpan Status</button>
        </form>
    </aside>
</div>
@endsection
