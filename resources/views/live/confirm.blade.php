@extends('layouts.app')
@section('title','Konfirmasi Jadwal | SKILLPATH')
@section('content')
<section class="simple-hero">
    <div class="container">
        <span class="eyebrow">Konfirmasi Booking</span>
        <h1>{{ $liveSession->title }}</h1>
        <p>{{ $liveSession->starts_at->format('d M Y, H:i') }}–{{ $liveSession->ends_at->format('H:i') }} · {{ $liveSession->learningPath->title }}</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <div class="content-card">
            <h2>Pemeriksaan jadwal otomatis</h2>
            @if($conflicts->isNotEmpty())
                <p><strong>Booking belum dapat dikonfirmasi karena jadwal bertabrakan.</strong></p>
                @foreach($conflicts as $conflict)
                    <div class="schedule-row">
                        <div>
                            <strong>{{ $conflict->liveSession->title }}</strong>
                            <span>{{ $conflict->liveSession->learningPath?->title }} · {{ $conflict->liveSession->starts_at->format('d M Y, H:i') }}–{{ $conflict->liveSession->ends_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="done-label">✓ Tidak ada jadwal yang bentrok</div>
                <p>Jadwal aman untuk dikonfirmasi.</p>
                <form method="POST" action="{{ route('live.book',$liveSession) }}">
                    @csrf
                    <button class="btn btn-blue" type="submit">Konfirmasi Booking</button>
                </form>

                @if($availableCredits->isNotEmpty())
                    <hr>
                    <h3>Jadwalkan ulang dengan kredit sesi</h3>
                    <p>Gunakan kredit dari sesi sebelumnya pada course yang sama.</p>
                    @foreach($availableCredits as $credit)
                        <div class="schedule-row">
                            <div>
                                <strong>1 kredit sesi</strong>
                                <span>Dari {{ $credit->sourceLiveSession?->title ?? 'sesi sebelumnya' }}@if($credit->expires_at) · berlaku sampai {{ $credit->expires_at->format('d M Y') }}@endif</span>
                            </div>
                            <form method="POST" action="{{ route('live.book',$liveSession) }}">
                                @csrf
                                <input type="hidden" name="session_credit_id" value="{{ $credit->id }}">
                                <button class="btn btn-ghost" type="submit">Gunakan Kredit</button>
                            </form>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>

        @if($conflicts->isNotEmpty())
            <div class="content-card">
                <h2>Jadwal alternatif</h2>
                @forelse($alternatives as $alternative)
                    <div class="schedule-row">
                        <div>
                            <strong>{{ $alternative->title }}</strong>
                            <span>{{ $alternative->starts_at->format('d M Y, H:i') }}–{{ $alternative->ends_at->format('H:i') }}</span>
                        </div>
                        <a class="btn btn-ghost" href="{{ route('live.confirm',$alternative) }}">Pilih Jadwal</a>
                    </div>
                @empty
                    <p>Belum ada jadwal alternatif yang bebas bentrok dan masih memiliki kursi.</p>
                @endforelse
            </div>
        @endif
    </div>
</section>
@endsection
