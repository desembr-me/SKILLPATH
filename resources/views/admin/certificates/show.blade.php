@extends('admin.layouts.app')

@section('title', 'Detail Sertifikat | Admin SKILLPATH')
@section('page-title', 'Detail Sertifikat')

@section('content')
<div class="admin-page-toolbar skill-cert-admin-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.certificates.index') }}">← Manajemen Sertifikat</a>

    <div class="admin-row-actions">
        <span class="admin-status {{ $certificate->status }}">
            {{ $certificate->status === 'active' ? 'AKTIF' : 'DICABUT' }}
        </span>
        <button class="admin-btn primary" type="button" onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>
</div>

<div class="skill-cert-admin-layout">
    <section class="admin-section-card skill-cert-admin-preview">
        <x-certificate.document
            :certificate="$certificate"
            :child="$certificate->childProfile"
            :course="$certificate->learningPath"
            :issuer-name="$certificate->issuedBy?->name"
        />
    </section>

    <aside class="admin-section-card skill-cert-admin-panel">
        <x-admin.section-header
            eyebrow="Administrasi"
            title="Informasi sertifikat"
            description="Status dan data penerbitan sertifikat siswa."
        />

        <div class="admin-detail-list">
            <div>
                <span>Nomor Sertifikat</span>
                <strong>{{ $certificate->certificate_number }}</strong>
            </div>
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
                <span>Tanggal Terbit</span>
                <strong>{{ $certificate->issued_at?->translatedFormat('d M Y, H:i') ?? '—' }}</strong>
            </div>
            <div>
                <span>Diterbitkan oleh</span>
                <strong>{{ $certificate->issuedBy?->name ?? 'Otomatis oleh sistem' }}</strong>
            </div>
        </div>

        @if($certificate->isActive())
            <form
                method="POST"
                action="{{ route('admin.certificates.revoke', $certificate) }}"
                class="admin-form-stack skill-cert-admin-form"
            >
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
                        placeholder="Contoh: data pada sertifikat perlu diperbaiki."
                    >{{ old('revoked_reason') }}</textarea>
                    @error('revoked_reason')
                        <span class="admin-field-error">{{ $message }}</span>
                    @enderror
                </label>

                <button class="admin-btn danger full" type="submit">
                    Cabut Sertifikat
                </button>
            </form>
        @else
            <div class="admin-alert-box danger">
                <strong>
                    Dicabut {{ $certificate->revoked_at?->translatedFormat('d M Y, H:i') ?? '' }}
                </strong>
                <span>{{ $certificate->revoked_reason ?: 'Tidak ada alasan pencabutan.' }}</span>
            </div>

            <form method="POST" action="{{ route('admin.certificates.reactivate', $certificate) }}">
                @csrf
                @method('PATCH')

                <button class="admin-btn primary full" type="submit">
                    Aktifkan Kembali
                </button>
            </form>
        @endif
    </aside>
</div>
@endsection
