@extends('layouts.app')
@section('title','Sertifikat '.$certificate->enrollment->course->title)
@section('content')
<x-parent-nav />
<section class="dashboard-page certificate-page">
    <div class="dash-title no-print"><div><span class="eyebrow">Sertifikat Kelulusan</span><h1>{{ $certificate->enrollment->course->title }}</h1><p>Diterbitkan untuk {{ $certificate->enrollment->child->name }} setelah mencapai passing grade.</p></div>
        <div class="dash-actions"><a class="btn btn-soft" href="{{ route('parent.exams') }}"><x-icon name="arrow-left" /> Ujian & Sertifikat</a><button class="btn btn-primary" onclick="window.print()"><x-icon name="certificate" /> Cetak / Simpan PDF</button></div>
    </div>

    <div class="certificate-sheet">
        <div class="certificate-border">
            <div class="certificate-brand"><img src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath"></div>
            <span class="certificate-kicker">Sertifikat Kelulusan</span>
            <h2 class="certificate-child">{{ $certificate->enrollment->child->name }}</h2>
            <p class="certificate-desc">telah menyelesaikan course <b>{{ $certificate->enrollment->course->title }}</b> ({{ $certificate->enrollment->course->category->name }}) dan dinyatakan lulus ujian akhir dengan nilai <b>{{ $certificate->examAttempt->score }}</b> dari passing grade <b>{{ $certificate->examAttempt->exam->passing_score }}</b>.</p>
            <div class="certificate-meta">
                <div><small>Nomor Sertifikat</small><b>{{ $certificate->certificate_no }}</b></div>
                <div><small>Tanggal Terbit</small><b>{{ $certificate->issued_at->translatedFormat('d F Y') }}</b></div>
                <div><small>Mentor</small><b>{{ $certificate->enrollment->course->instructor->name }}</b></div>
            </div>
        </div>
    </div>
</section>
@endsection
