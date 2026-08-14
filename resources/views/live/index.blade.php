@extends('layouts.app')
@section('title','Jadwal Kelas | SKILLPATH')
@section('content')
<section class="simple-hero">
    <div class="container">
        <span class="eyebrow">Jadwal Kelas</span>
        <h1>Belajar langsung bersama pengajar.</h1>
        <p>Pilih sesi tatap muka dari kelas yang sudah aktif, cek waktu dan lokasi, lalu pesan kursi.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        @if($availableCredits->isNotEmpty())
            <div class="content-card">
                <h2>Kredit sesi tersedia</h2>
                <p>Anda memiliki {{ $availableCredits->count() }} kredit sesi yang dapat digunakan untuk menjadwalkan ulang sesi pada kelas yang sama.</p>
                @foreach($availableCredits as $credit)
                    <div class="schedule-row">
                        <div>
                            <strong>{{ $credit->learningPath?->title }}</strong>
                            <span>Dari {{ $credit->sourceLiveSession?->title ?? 'sesi sebelumnya' }} · {{ ucfirst(str_replace('_',' ',$credit->reason)) }}@if($credit->expires_at) · Berlaku sampai {{ $credit->expires_at->format('d M Y') }}@endif @if($credit->reactivation_count > 0) · Dijadwalkan ulang {{ $credit->reactivation_count }}×@endif</span>
                        </div>
                        <span>1 kredit</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if($recentCredits->isNotEmpty())
            <div class="content-card">
                <h2>Riwayat kredit sesi</h2>
                @foreach($recentCredits as $credit)
                    <div class="schedule-row">
                        <div>
                            <strong>{{ $credit->learningPath?->title ?? 'Kelas tidak tersedia' }}</strong>
                            <span>
                                Asal: {{ $credit->sourceLiveSession?->title ?? 'sesi sebelumnya' }}
                                @if($credit->usedLiveSession) · Dipakai untuk: {{ $credit->usedLiveSession->title }}@endif
                                @if($credit->expires_at) · Batas: {{ $credit->expires_at->format('d M Y') }}@endif
                            </span>
                        </div>
                        <span>
                            @if($credit->isExpired() && $credit->status === 'available')
                                Kedaluwarsa
                            @elseif($credit->status === 'used')
                                Digunakan
                            @else
                                Tersedia
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="live-grid">
            @forelse($sessions as $s)
                <article class="live-card">
                    <div class="live-date"><strong>{{ $s->starts_at->format('d') }}</strong><span>{{ $s->starts_at->format('M') }}</span></div>
                    <div>
                        <span class="path-skill">{{ $s->learningPath->title }}</span>
                        <h2>{{ $s->title }}</h2>
                        <p>{{ $s->starts_at->format('H:i') }}–{{ $s->ends_at->format('H:i') }} · {{ $s->instructor->name }}</p>
                        @if($s->location)<p><strong>Lokasi:</strong> {{ $s->location }}</p>@endif
                        <small>{{ $s->bookings->where('status','booked')->count() }}/{{ $s->capacity }} kursi terisi</small>
                        @if($conflictIds->contains($s->id))
                            <small> · Jadwal berpotensi bentrok, periksa sebelum booking</small>
                        @endif
                    </div>
                    <div>
                        @if($bookedIds->contains($s->id))
                            <a class="btn btn-dark" href="{{ route('live.show',$s) }}">Detail Kelas</a>
                        @elseif($s->starts_at->isFuture())
                            <a class="btn btn-blue" href="{{ route('live.confirm',$s) }}">Pesan Kursi</a>
                        @else
                            <a class="btn btn-ghost" href="{{ route('live.show',$s) }}">Lihat Detail</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-card"><h2>Belum ada jadwal kelas tatap muka.</h2><p>Jadwal baru akan muncul saat pengajar membuka sesi.</p></div>
            @endforelse
        </div>
    </div>
</section>
@endsection
