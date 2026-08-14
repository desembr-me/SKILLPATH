@extends('layouts.app')
@section('title',$liveSession->title.' | Jadwal Kelas')
@section('content')
<section class="simple-hero">
    <div class="container">
        <span class="eyebrow">{{ $liveSession->learningPath->title }}</span>
        <h1>{{ $liveSession->title }}</h1>
        <p>{{ $liveSession->starts_at->format('d M Y H:i') }}–{{ $liveSession->ends_at->format('H:i') }} bersama {{ $liveSession->instructor->name }}</p>
        @if($liveSession->location)<p><strong>Lokasi:</strong> {{ $liveSession->location }}</p>@endif
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <div class="content-card">
            <h2>Detail sesi tatap muka</h2>
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
                @if($liveSession->location)
                    <p class="meeting-note">Lokasi kelas:</p>
                    <p>{{ $liveSession->location }}</p>
                @endif
                @if($liveSession->meeting_url)
                    <a class="btn btn-dark" href="{{ $liveSession->meeting_url }}" target="_blank" rel="noopener">Buka Petunjuk Lokasi</a>
                @else
                    <p>Petunjuk lokasi akan diinformasikan pengajar sebelum kelas dimulai.</p>
                @endif
            @else
                <p>Sesi ini tidak tersedia untuk booking baru.</p>
            @endif
        </div>

        @if($booking && !$liveSession->ends_at->isPast())
            <div class="content-card">
                <h2>Tidak dapat hadir?</h2>
                <p>Konversi booking menjadi kredit sesi. Kredit dapat dipakai untuk menjadwalkan ulang pada kelas yang sama tanpa proses refund dan mengikuti masa aktif pendaftaran.</p>
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
