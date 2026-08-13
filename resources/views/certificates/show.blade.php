@extends('layouts.app')

@section('title', 'Sertifikat | ' . $learningPath->title)

@section('content')
<section class="certificate-section">
    <div class="container">
        <div class="certificate-landscape-sheet">
            <div class="certificate-landscape-frame">
                <div class="certificate-corner corner-top-left"></div>
                <div class="certificate-corner corner-top-right"></div>
                <div class="certificate-corner corner-bottom-left"></div>
                <div class="certificate-corner corner-bottom-right"></div>

                <div class="certificate-header-row">
                    <div class="brand certificate-brand">
                        <span class="brand-mark">S</span>
                        <span>SKILLPATH</span>
                    </div>

                    <div class="certificate-header-copy">
                        <span class="certificate-kicker">SERTIFIKAT PENYELESAIAN</span>
                        <small>Platform Upskilling Nonakademik untuk Anak Usia 5–14 Tahun</small>
                    </div>
                </div>

                <div class="certificate-body-grid">
                    <div class="certificate-body-copy">
                        <p class="certificate-lead">Sertifikat ini diberikan kepada</p>

                        <h1>{{ $child->name }}</h1>

                        <p class="certificate-description">
                            atas keberhasilan menyelesaikan course
                            <strong>{{ $learningPath->title }}</strong>
                            pada platform SKILLPATH.
                        </p>

                        <div class="certificate-highlight-grid">
                            <div>
                                <span>Pengajar</span>
                                <strong>{{ $learningPath->instructor?->name ?? 'Tim SKILLPATH' }}</strong>
                            </div>
                            <div>
                                <span>Nilai Akhir</span>
                                <strong>{{ $certificate->final_score !== null ? number_format($certificate->final_score, 1) : '-' }}</strong>
                            </div>
                            <div>
                                <span>Tanggal Terbit</span>
                                <strong>{{ $certificate->issued_at->translatedFormat('d F Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <aside class="certificate-side-panel">
                        <div class="certificate-badge-box">
                            <span class="certificate-side-label">Nomor Sertifikat</span>
                            <strong>{{ $certificate->certificate_number }}</strong>
                        </div>

                        <div class="certificate-badge-box">
                            <span class="certificate-side-label">Kategori Course</span>
                            <strong>
                                {{ $learningPath->categories->pluck('name')->implode(', ') ?: 'Nonakademik' }}
                            </strong>
                        </div>

                        <div class="certificate-badge-box">
                            <span class="certificate-side-label">Status</span>
                            <strong>{{ $certificate->status === 'active' ? 'Aktif' : 'Dicabut' }}</strong>
                        </div>

                        <div class="certificate-signature-box">
                            <span>SKILLPATH</span>
                            <strong>Tim Akademik & Pengembangan Program</strong>
                            <small>Dokumen ini diterbitkan secara digital melalui sistem SKILLPATH.</small>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
