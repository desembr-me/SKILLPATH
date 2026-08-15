@extends('layouts.app')
@section('title','Sertifikat '.$certificate->enrollment->course->title)
@section('content')
<section class="dashboard-page certificate-page">
    <div class="dash-title no-print"><div><span class="eyebrow">Sertifikat Kelulusan</span><h1>{{ $certificate->enrollment->course->title }}</h1><p>Diterbitkan untuk {{ $certificate->enrollment->child->name }} setelah mencapai passing grade.</p></div>
        <div class="dash-actions"><a class="btn btn-soft" href="{{ route('parent.exams') }}"><x-icon name="arrow-left" /> Ujian & Sertifikat</a><button class="btn btn-primary" onclick="window.print()"><x-icon name="certificate" /> Cetak / Simpan PDF</button></div>
    </div>

    <div class="certificate-sheet">
        <div class="certificate-border">
            <span class="certificate-corner corner-tl"></span>
            <span class="certificate-corner corner-tr"></span>
            <span class="certificate-corner corner-bl"></span>
            <span class="certificate-corner corner-br"></span>

            <div class="certificate-zone certificate-head">
                <img class="certificate-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath">
                <span class="certificate-title">Sertifikat Kelulusan</span>
                <span class="certificate-divider"></span>
            </div>

            <div class="certificate-zone certificate-body">
                <span class="certificate-eyebrow">Dengan bangga diberikan kepada</span>
                <h2 class="certificate-child">{{ $certificate->enrollment->child->name }}</h2>
                <p class="certificate-desc">telah menyelesaikan course <b>{{ $certificate->enrollment->course->title }}</b> ({{ $certificate->enrollment->course->category->name }}) dan dinyatakan lulus ujian akhir dengan nilai <b>{{ $certificate->examAttempt->score }}</b> dari passing grade <b>{{ $certificate->examAttempt->exam->passing_score }}</b>.</p>
            </div>

            <div class="certificate-zone certificate-foot">
                <div class="certificate-signatures">
                    <div class="signature-block">
                        <span class="signature-line"></span>
                        <b>{{ $certificate->enrollment->course->instructor->name }}</b>
                        <small>Mentor</small>
                    </div>
                    <div class="certificate-seal"><x-icon name="certificate" /></div>
                    <div class="signature-block">
                        <span class="signature-line"></span>
                        <b>{{ $certificate->issued_at->translatedFormat('d F Y') }}</b>
                        <small>Tanggal Terbit</small>
                    </div>
                </div>
                <span class="certificate-serial">No. Sertifikat {{ $certificate->certificate_no }}</span>
            </div>
        </div>
    </div>
</section>
@endsection
