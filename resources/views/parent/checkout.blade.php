@extends('layouts.app')
@section('title','Checkout')
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div><span class="eyebrow">Checkout</span><h1>Periksa Course Sebelum Checkout</h1><p>Pastikan course, anak, dan jadwal yang dipilih sudah sesuai sebelum melanjutkan.</p></div>
        <a class="btn btn-soft" href="{{ route('parent.cart') }}"><x-icon name="arrow-left" /> Kembali ke Keranjang</a>
    </div>
    <div class="checkout-grid">
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Pesanan</span><h2>Course yang Akan Di-checkout</h2></div></div>
            @foreach($items as $item)
            <article class="checkout-course">
                <div class="checkout-course-art"><x-course-art :course="$item->schedule->course" /></div>
                <div class="checkout-course-main">
                    <span class="eyebrow">{{ $item->schedule->course->category->name }}</span>
                    <h3>{{ $item->schedule->course->title }}</h3>
                    <p>{{ $item->child->name }} • Hari {{ $item->schedule->day_of_week }} • {{ substr($item->schedule->start_time,0,5) }}-{{ substr($item->schedule->end_time,0,5) }}</p>
                    <small>{{ $item->schedule->course->sessions_count }} sesi • {{ $item->schedule->course->location_name }}, {{ $item->schedule->course->city }}</small>
                    <a class="text-link" href="{{ route('courses.show',$item->schedule->course) }}">Lihat detail course <x-icon name="arrow-right" /></a>
                </div>
                <strong>Rp{{ number_format($item->schedule->course->price,0,',','.') }}</strong>
            </article>
            @endforeach
        </div>
        <aside class="panel checkout-summary">
            <span class="panel-kicker">Ringkasan Pembayaran</span>
            <h2>Total Checkout</h2>
            <div class="summary-line"><span>Subtotal course</span><b>Rp{{ number_format($subtotal,0,',','.') }}</b></div>
            <div class="summary-line"><span>Biaya platform</span><b>Rp{{ number_format($platformFee,0,',','.') }}</b></div>
            <div class="summary-total"><span>Total</span><b>Rp{{ number_format($total,0,',','.') }}</b></div>
            <form method="POST" action="{{ route('parent.checkout.store') }}">@csrf<button class="btn btn-primary btn-lg checkout-submit">Konfirmasi Checkout <x-icon name="arrow-right" /></button></form>
            <p class="checkout-note"><x-icon name="conflict" /> Jadwal akan diperiksa otomatis. Jika terjadi bentrok dengan course aktif anak, item tersebut tidak akan diproses.</p>
        </aside>
    </div>
</section>
@endsection
