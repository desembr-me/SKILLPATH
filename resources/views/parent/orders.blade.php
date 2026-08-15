@extends('layouts.app')
@section('title','Riwayat Pesanan')
@section('content')
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Riwayat Pesanan</span><h1>Pesanan Saya</h1><p>Lihat status dan detail pembayaran setiap pesanan course.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    <div class="panel admin-table-panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Invoice</th><th>Course</th><th>Anak</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->invoice_code }}</td>
                        <td>{{ $order->enrollment->course->title ?? '-' }}</td>
                        <td>{{ $order->enrollment->child->name ?? '-' }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>Rp{{ number_format($order->total,0,',','.') }}</td>
                        <td><span class="status-chip {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>@if($order->status==='pending')<form method="POST" action="{{ route('parent.transactions.pay',$order) }}">@csrf<button class="pay-link">Bayar Demo</button></form>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state compact-empty"><x-icon name="receipt" /><div><b>Belum ada pesanan</b><span>Pesanan akan muncul setelah checkout.</span></div></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="pagination">{{ $orders->links() }}</div>@endif
    </div>
</section>
@endsection
