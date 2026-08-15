@extends('layouts.app')
@section('title','Wishlist')
@section('content')
<x-parent-nav />
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Wishlist</span><h1>Course Diminati</h1><p>Simpan course yang menarik untuk dilihat lagi sebelum booking.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    <div class="panel">
        @forelse($wishlists as $wishlist)
            <div class="credit-row">
                <div class="row-icon-vector" style="--row-accent:{{ $wishlist->course->accent }}"><x-icon :name="$wishlist->course->category->slug" /></div>
                <div><h3>{{ $wishlist->course->title }}</h3><p>{{ $wishlist->course->category->name }} • Mentor {{ $wishlist->course->instructor->name }}</p><small>Rp{{ number_format($wishlist->course->price,0,',','.') }} / paket</small></div>
                <div class="row-actions"><a class="btn btn-soft" href="{{ route('courses.show',$wishlist->course) }}">Lihat Detail</a><form method="POST" action="{{ route('parent.wishlist.toggle',$wishlist->course) }}">@csrf<button class="pay-link">Hapus</button></form></div>
            </div>
        @empty<div class="empty-state"><x-icon name="heart" /><div><b>Wishlist masih kosong</b><span>Tandai course yang menarik dari halaman detail course.</span></div></div>@endforelse
    </div>
</section>
@endsection
