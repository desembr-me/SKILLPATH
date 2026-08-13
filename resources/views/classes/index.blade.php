@extends('layouts.app')
@section('title','Jadwal Kelas | SKILLPATH')
@section('content')
<section class="simple-hero"><div class="container"><span class="eyebrow">Jadwal Kelas Offline</span><h1>Pilih waktu dan tempat untuk hadir.</h1><p>Jadwal hanya menampilkan kelas yang sudah terdaftar pada akun anak.</p></div></section>
<section class="section"><div class="container"><div class="live-grid">
@forelse($sessions as $session)
@php($booking=$bookings->get($session->id))
<article class="live-card">
<div class="live-date"><strong>{{ $session->starts_at->format('d') }}</strong><span>{{ $session->starts_at->translatedFormat('M') }}</span></div>
<div><span class="path-skill">{{ $session->learningPath->title }}</span><h2>{{ $session->title }}</h2><p>{{ $session->starts_at->format('H:i') }}–{{ $session->ends_at->format('H:i') }} · {{ $session->instructor->name }}</p><small>📍 {{ $session->venue_name }}{{ $session->room ? ' · '.$session->room : '' }}</small><br><small>{{ $session->bookings->whereIn('status',['booked','attended'])->count() }}/{{ $session->capacity }} kursi terisi</small></div>
<div>@if($booking && in_array($booking->status,['booked','attended']))<a class="btn btn-dark" href="{{ route('class-schedules.show',$session) }}">Detail Kelas</a>@else<form method="POST" action="{{ route('class-schedules.book',$session) }}">@csrf<button class="btn btn-blue" type="submit">Pesan Kursi</button></form>@endif</div>
</article>
@empty<div class="empty-card"><h2>Belum ada jadwal kelas mendatang.</h2><p>Jadwal baru akan muncul setelah pengajar atau admin menentukan sesi tatap muka.</p><a class="btn btn-dark" href="{{ route('my-courses.index') }}">Kembali ke Kelas Saya</a></div>@endforelse
</div></div></section>
@endsection
