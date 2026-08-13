@extends('layouts.app')
@section('title',$classSession->title.' | Jadwal Kelas')
@section('content')
<section class="simple-hero"><div class="container"><span class="eyebrow">{{ $classSession->learningPath->title }}</span><h1>{{ $classSession->title }}</h1><p>{{ $classSession->starts_at->translatedFormat('d M Y H:i') }}–{{ $classSession->ends_at->format('H:i') }} bersama {{ $classSession->instructor->name }}</p></div></section>
<section class="section"><div class="container narrow"><div class="content-card"><h2>Lokasi kelas</h2><h3>{{ $classSession->venue_name }}</h3><p>{{ $classSession->address }}</p>@if($classSession->room)<p><strong>Ruangan / titik temu:</strong> {{ $classSession->room }}</p>@endif @if($classSession->map_url)<a class="btn btn-ghost" href="{{ $classSession->map_url }}" target="_blank" rel="noopener">Buka Peta</a>@endif</div>
<div class="content-card"><h2>Detail sesi</h2><p>{{ $classSession->description ?: 'Kegiatan akan dipandu langsung oleh pengajar di lokasi.' }}</p>@if($classSession->preparation_notes)<h3>Yang perlu dipersiapkan</h3><p>{{ $classSession->preparation_notes }}</p>@endif
@if(!$booking || $booking->status==='cancelled')<form method="POST" action="{{ route('class-schedules.book',$classSession) }}">@csrf<button class="btn btn-blue" type="submit">Pesan Kursi</button></form>
@else<div class="done-label">✓ Kursi sudah dipesan untuk {{ auth()->user()->childProfile?->name }}</div>@if($booking->status==='booked' && $classSession->starts_at->isFuture())<form method="POST" action="{{ route('class-schedules.cancel',$classSession) }}" class="mt-10">@csrf @method('PATCH')<button class="btn btn-ghost" type="submit">Batalkan Kursi</button></form>@endif @endif
</div></div></section>
@endsection
