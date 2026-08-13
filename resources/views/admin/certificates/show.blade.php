@extends('admin.layouts.app')

@section('title', 'Detail Sertifikat | Admin SKILLPATH')
@section('page-title', 'Detail Sertifikat')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.certificates.index') }}">← Manajemen Sertifikat</a>

    <div class="admin-row-actions">
        <span class="admin-status {{ $certificate->status }}">
            {{ $certificate->status === 'active' ? 'AKTIF' : 'DICABUT' }}
        </span>
        <button class="admin-btn primary" type="button" onclick="window.print()">Cetak Sertifikat</button>
    </div>
</div>

<div class="certificate-detail-layout">
    <section class="admin-section-card certificate-preview-panel">
        <div class="certificate-preview certificate-preview-landscape {{ $certificate->isRevoked() ? 'is-revoked' : '' }}">
            <div class="certificate-corner corner-top-left"></div>
            <div class="certificate-corner corner-top-right"></div>
            <div class="certificate-corner corner-bottom-left"></div>
            <div class="certificate-corner corner-bottom-right"></div>

            <div class="certificate-preview-header">
                <div class="certificate-preview-brand">
                    <span class="admin-brand-mark">S</span>
                    <strong>SKILLPATH</strong>
                </div>

                <div class="certificate-preview-header-copy">
                    <span class="certificate-preview-kicker">SERTIFIKAT PENYELESAIAN</span>
                    <small>Platform Upskilling Nonakademik untuk Anak Usia 5–14 Tahun</small>
                </div>
            </div>

            <div class="certificate-preview-content">
                <div class="certificate-preview-main">
                    <p>Sertifikat ini diberikan kepada</p>
                    <h2>{{ $certificate->childProfile?->name ?? 'Siswa' }}</h2>

                    <p>atas keberhasilan menyelesaikan course</p>
                    <h3>{{ $certificate->learningPath?->title ?? 'Course SKILLPATH' }}</h3>

                    <div class="certificate-preview-meta">
                        <div>
                            <span>Pengajar</span>
                            <strong>{{ $certificate->learningPath?->instructor?->name ?? 'Tim SKILLPATH' }}</strong>
                        </div>
                        <div>
                            <span>Nilai Akhir</span>
                            <strong>{{ $certificate->final_score !== null ? number_format($certificate->final_score, 1) : '—' }}</strong>
                        </div>
                        <div>
                            <span>Tanggal Terbit</span>
                            <strong>{{ $certificate->issued_at?->translatedFormat('d F Y') ?? '—' }}</strong>
                        </div>
                    </div>
                </div>

                <aside class="certificate-preview-side">
                    <div class="certificate-side-box">
                        <span>Nomor Sertifikat</span>
                        <strong>{{ $certificate->certificate_number }}</strong>
                    </div>

                    <div class="certificate-side-box">
                        <span>Status</span>
                        <strong>{{ $certificate->status === 'active' ? 'Aktif' : 'Dicabut' }}</strong>
                    </div>

                    <div class="certificate-side-box signature">
                        <span>Diterbitkan oleh</span>
                        <strong>{{ $certificate->issuedBy?->name ?? 'Otomatis oleh sistem' }}</strong>
                        <small>SKILLPATH</small>
                    </div>
                </aside>
            </div>

            @if($certificate->isRevoked())
                <div class="certificate-revoked-watermark">DICABUT</div>
            @endif
        </div>
    </section>

    <aside class="admin-section-card certificate-management-panel">
        <x-admin.section-header
            eyebrow="Administrasi"
            title="Informasi sertifikat"
        />

        <div class="admin-detail-list">
            <div>
                <span>Siswa</span>
                <strong>{{ $certificate->childProfile?->name ?? '—' }}</strong>
            </div>
            <div>
                <span>Orang Tua</span>
                <strong>{{ $certificate->childProfile?->user?->name ?? '—' }}</strong>
            </div>
            <div>
                <span>Course</span>
                <strong>{{ $certificate->learningPath?->title ?? '—' }}</strong>
            </div>
            <div>
                <span>Pengajar</span>
                <strong>{{ $certificate->learningPath?->instructor?->name ?? '—' }}</strong>
            </div>
            <div>
                <span>Diterbitkan oleh</span>
                <strong>{{ $certificate->issuedBy?->name ?? 'Otomatis oleh sistem' }}</strong>
            </div>
        </div>

        @if($certificate->isActive())
            <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" class="admin-form-stack">
                @csrf
                @method('PATCH')

                <label class="admin-form-field">
                    <span>Alasan pencabutan</span>
                    <textarea
                        name="revoked_reason"
                        rows="4"
                        required
                        minlength="5"
                        maxlength="500"
                        placeholder="Tuliskan alasan administratif pencabutan sertifikat."
                    >{{ old('revoked_reason') }}</textarea>
                    @error('revoked_reason') <span class="admin-field-error">{{ $message }}</span> @enderror
                </label>

                <button class="admin-btn danger full" type="submit">Cabut Sertifikat</button>
            </form>
        @else
            <div class="admin-alert-box danger">
                <strong>Dicabut {{ $certificate->revoked_at?->translatedFormat('d M Y H:i') ?? '' }}</strong>
                <span>{{ $certificate->revoked_reason }}</span>
            </div>

            <form method="POST" action="{{ route('admin.certificates.reactivate', $certificate) }}">
                @csrf
                @method('PATCH')
                <button class="admin-btn primary full" type="submit">Aktifkan Kembali</button>
            </form>
        @endif
    </aside>
</div>
@endsection
