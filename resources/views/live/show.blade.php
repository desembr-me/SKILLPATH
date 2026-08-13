@extends('layouts.app')
@section('title',$liveSession->title.' | Live Class')
@section('content')
<section class="simple-hero">
    <div class="container">
        <span class="eyebrow">{{ $liveSession->learningPath->title }}</span>
        <h1>{{ $liveSession->title }}</h1>
        <p>{{ $liveSession->starts_at->format('d M Y H:i') }}–{{ $liveSession->ends_at->format('H:i') }} bersama {{ $liveSession->instructor->name }}</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <div class="content-card">
            <h2>Detail sesi</h2>
            <p>{{ $liveSession->description }}</p>

            @if(!$booking && $liveSession->status === 'scheduled' && $liveSession->starts_at->isFuture())
                @if($conflicts->isNotEmpty())
                    <p><strong>Jadwal ini bentrok dengan sesi lain yang sudah dimiliki siswa.</strong></p>
                    @foreach($conflicts as $conflict)
                        <div class="schedule-row">
                            <div>
                                <strong>{{ $conflict->liveSession->title }}</strong>
                                <span>{{ $conflict->liveSession->starts_at->format('d M Y H:i') }}–{{ $conflict->liveSession->ends_at->format('H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
                <a class="btn btn-blue" href="{{ route('live.confirm',$liveSession) }}">Periksa & Pesan Kursi</a>
            @elseif($booking)
                <div class="done-label">✓ Kursi sudah dipesan</div>
                @if($liveSession->meeting_url)
                    <p class="meeting-note">Tautan kelas:</p>
                    <a class="btn btn-dark" href="{{ $liveSession->meeting_url }}" target="_blank" rel="noopener">Masuk Ruang Live</a>
                @else
                    <p>Tautan live akan ditambahkan pengajar sebelum sesi dimulai.</p>
                @endif
            @else
                <p>Sesi ini tidak tersedia untuk booking baru.</p>
            @endif
        </div>

        @if($booking && !$liveSession->ends_at->isPast())
            <div class="content-card">
                <h2>Tidak dapat mengikuti sesi?</h2>
                <p>Konversi booking menjadi kredit sesi. Kredit dapat dipakai untuk menjadwalkan ulang pada course yang sama tanpa proses refund dan mengikuti masa aktif akses course.</p>
                <form method="POST" action="{{ route('live.credit',$liveSession) }}" class="form-stack">
                    @csrf
                    <label>
                        <span>Alasan</span>
                        <select name="reason" required>
                            <option value="sakit">Sakit</option>
                            <option value="bentrok">Bentrok jadwal</option>
                            <option value="keluarga">Keperluan keluarga</option>
                            <option value="lainnya">Alasan lainnya</option>
                        </select>
                    </label>
                    <label>
                        <span>Catatan opsional</span>
                        <textarea name="reason_note" rows="3" maxlength="500" placeholder="Tambahkan keterangan singkat bila diperlukan."></textarea>
                    </label>
                    <button class="btn btn-ghost" type="submit">Ubah Menjadi Kredit Sesi</button>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection
