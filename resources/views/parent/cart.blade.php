@extends('layouts.app')
@section('title','Keranjang')
@section('content')
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Keranjang</span><h1>Keranjang Booking</h1><p>Tinjau course sebelum checkout. Jadwal diperiksa otomatis saat checkout.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    @if(session('error'))<div class="flash error"><strong>{{ session('error') }}</strong></div>@endif
    <div class="panel">
        @forelse($items as $item)
            <div class="credit-row">
                <div class="row-icon-vector" style="--row-accent:{{ $item->schedule->course->accent }}"><x-icon :name="$item->schedule->course->category->slug" /></div>
                <div class="cart-item-main"><h3>{{ $item->schedule->course->title }}</h3><p>{{ $item->child->name }} • Hari {{ $item->schedule->day_of_week }} • {{ substr($item->schedule->start_time,0,5) }}-{{ substr($item->schedule->end_time,0,5) }}</p><small>Rp{{ number_format($item->schedule->course->price+15000,0,',','.') }} (termasuk biaya platform)</small><a class="text-link" href="{{ route('courses.show',$item->schedule->course) }}">Lihat course <x-icon name="arrow-right" /></a></div>
                <form method="POST" action="{{ route('parent.cart.destroy',$item) }}">@csrf @method('DELETE')<button class="pay-link">Hapus</button></form>
            </div>
        @empty<div class="empty-state"><x-icon name="cart" /><div><b>Keranjang masih kosong</b><span>Tambahkan course dari halaman detail course.</span></div></div>@endforelse
    </div>
    @if($items->count())
    <div class="panel cart-summary">
        <div><small>Total ({{ $items->count() }} course)</small><b>Rp{{ number_format($total,0,',','.') }}</b></div>
        <a class="btn btn-primary btn-lg" href="{{ route('parent.checkout') }}">Lihat & Checkout <x-icon name="arrow-right" /></a>
    </div>
    @endif
</section>
@endsection
