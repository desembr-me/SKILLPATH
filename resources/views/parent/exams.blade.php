@extends('layouts.app')
@section('title','Ujian & Sertifikat')
@section('content')
<x-parent-nav />
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Exam Based Certificate</span><h1>Ujian & Sertifikat</h1><p>Sertifikat hanya terbit setelah anak mencapai passing grade. Retake mengikuti batas percobaan course.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    <div class="info-banner certificate-banner"><span><x-icon name="certificate" /></span><div><b>Sertifikat berbasis kelulusan</b><p>Menyelesaikan seluruh sesi belum otomatis menghasilkan sertifikat. Anak harus mencapai passing grade pada ujian akhir sesuai aturan course.</p></div></div>
    <div class="panel">
        @forelse($enrollments as $enrollment)
            @php($exam = $enrollment->course->exams->first())
            @php($attempts = $exam ? $enrollment->examAttempts->where('exam_id',$exam->id) : collect())
            @php($lastAttempt = $attempts->sortByDesc('attempt_no')->first())
            <div class="exam-row">
                <div class="row-icon-vector" style="--row-accent:{{ $enrollment->course->accent }}"><x-icon :name="$enrollment->course->category->slug" /></div>
                <div class="exam-main">
                    <h3>{{ $enrollment->course->title }}</h3>
                    @if($exam)
                        <p>Passing grade {{ $exam->passing_score }} • Maksimal {{ $exam->max_attempts }} percobaan</p>
                        @if($attempts->count())
                            <div class="mini-tags exam-attempt-list">@foreach($attempts->sortBy('attempt_no') as $attempt)<span>Percobaan {{ $attempt->attempt_no }}: {{ $attempt->score }} • {{ ucfirst($attempt->status) }}</span>@endforeach</div>
                            @if(!$enrollment->certificate && $lastAttempt && $lastAttempt->status==='failed' && $lastAttempt->mentor_feedback)
                                <small class="exam-feedback">Catatan mentor: {{ $lastAttempt->mentor_feedback }}</small>
                            @endif
                            @if(!$enrollment->certificate && $attempts->count() < $exam->max_attempts)
                                <small>Retake dinilai langsung oleh mentor pada sesi berikutnya ({{ $exam->max_attempts - $attempts->count() }} kesempatan tersisa).</small>
                            @endif
                        @else<small>Belum ada percobaan ujian.</small>@endif
                    @else<small>Ujian belum tersedia.</small>@endif
                </div>
                <div class="exam-status">
                    @if($enrollment->certificate)
                        <a class="status-chip paid" href="{{ route('parent.certificates.show',$enrollment->certificate) }}">Lihat Sertifikat</a>
                        <small>{{ $enrollment->certificate->certificate_no }}</small>
                    @elseif($exam && $attempts->count() < $exam->max_attempts)
                        <span class="status-chip pending">Retake tersedia</span>
                    @else
                        <span class="status-chip locked">Menunggu</span>
                    @endif
                </div>
            </div>
        @empty<div class="empty-state"><x-icon name="certificate" /><div><b>Belum ada enrollment</b><span>Informasi ujian akan muncul setelah anak mengikuti course.</span></div></div>@endforelse
    </div>
</section>
@endsection
