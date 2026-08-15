@extends('layouts.app')
@section('title','Detail Siswa '.$child->name)
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Detail Siswa</span>
            <h1>{{ $child->name }}</h1>
            <p>{{ $enrollment->course->title }} • {{ ucfirst($enrollment->status) }}</p>
        </div>
        <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Kehadiran</span><h2>Riwayat Kehadiran</h2></div></div>
            @forelse($enrollment->attendance as $att)
                <div class="child-row">
                    <div class="child-avatar"><x-icon name="calendar" /></div>
                    <div>
                        <h3>Sesi {{ $att->courseSession->session_no }} • {{ optional($att->courseSession->session_date)->format('d M Y') }}</h3>
                        <p>{{ $att->courseSession->topic }}</p>
                        @if($att->absence_reason)<div class="mini-tags"><span>{{ $att->absence_reason }}</span></div>@endif
                    </div>
                    <span class="status-chip {{ $att->status }}">{{ ucfirst($att->status) }}</span>
                </div>
            @empty
                <div class="empty-state compact-empty"><x-icon name="calendar" /><div><b>Belum ada catatan kehadiran</b><span>Kehadiran akan muncul setelah sesi berlangsung.</span></div></div>
            @endforelse
        </div>

        <div class="panel student-profile-panel">
            <div class="panel-heading"><div><span class="panel-kicker">Profil anak</span><h2>Tentang {{ $child->name }}</h2></div></div>
            <div class="family-summary">
                <div class="summary-avatar">
                    @if($child->avatar && str_starts_with($child->avatar,'avatars/'))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($child->avatar) }}" alt="">
                    @else
                        {{ $child->avatar ?: strtoupper(substr($child->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <h2>{{ $child->name }}</h2>
                    <p>{{ $child->age }} tahun @if($child->nickname) • {{ $child->nickname }} @endif</p>
                </div>
            </div>
            <div class="mini-tags">
                @forelse($child->interests ?? [] as $interest)<span>{{ $interest }}</span>@empty<span>Minat belum diisi</span>@endforelse
            </div>
            @if($child->notes)
                <p class="mentor-note">"{{ $child->notes }}"</p>
            @endif
        </div>
    </div>

    <div class="panel mentor-tools">
        <div class="panel-heading"><div><span class="panel-kicker">Evaluasi</span><h2>Riwayat Nilai Ujian</h2></div></div>
        @forelse($enrollment->examAttempts as $attempt)
            <div class="exam-row">
                <div class="exam-main">
                    <h3>{{ $attempt->exam->title }} • Percobaan {{ $attempt->attempt_no }}</h3>
                    <p>Nilai {{ $attempt->score }} • Passing {{ $attempt->exam->passing_score }}</p>
                    @if($attempt->mentor_feedback)<small>Feedback: {{ $attempt->mentor_feedback }}</small>@endif
                </div>
                <div class="exam-status"><span class="status-chip {{ $attempt->status }}">{{ ucfirst($attempt->status) }}</span></div>
            </div>
        @empty
            <div class="empty-state compact-empty"><x-icon name="certificate" /><div><b>Belum ada percobaan ujian</b><span>Nilai akan muncul setelah siswa mengikuti ujian.</span></div></div>
        @endforelse
        @if($enrollment->certificate)
            <div class="learning-path-intro certificate-banner student-certificate-banner">
                <span><x-icon name="certificate" /></span>
                <div><b>Sertifikat terbit</b><p>Nomor {{ $enrollment->certificate->certificate_no }} • {{ $enrollment->certificate->issued_at->format('d M Y') }}</p></div>
            </div>
        @endif
    </div>
</section>
@endsection
