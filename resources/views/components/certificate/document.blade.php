@props([
    'certificate',
    'child',
    'kelas',
    'issuerName' => null,
])

@php
    $instructorName = $kelas?->instructor?->name ?? 'Tim Pengajar SKILLPATH';
    $issuer = $issuerName ?: ($certificate?->issuedBy?->name ?? 'Tim SKILLPATH');
    $categories = $kelas?->categories?->pluck('name')->implode(' · ') ?: 'Pengembangan Keterampilan Nonakademik';
    $isRevoked = method_exists($certificate, 'isRevoked')
        ? $certificate->isRevoked()
        : ($certificate->status ?? null) === 'revoked';
@endphp

<div class="skill-certificate-print-area">
    <article class="skill-certificate-document {{ $isRevoked ? 'is-revoked' : '' }}">
        <span class="skill-cert-ornament ornament-a"></span>
        <span class="skill-cert-ornament ornament-b"></span>
        <span class="skill-cert-ornament ornament-c"></span>
        <span class="skill-cert-ornament ornament-d"></span>

        <header class="skill-cert-header">
            <div class="skill-cert-brand">
                <span class="skill-cert-logo">S</span>
                <div>
                    <strong>SKILLPATH</strong>
                    <small>Learn · Grow · Create</small>
                </div>
            </div>

            <div class="skill-cert-code">
                <span>Nomor Sertifikat</span>
                <strong>{{ $certificate->certificate_number }}</strong>
            </div>
        </header>

        <div class="skill-cert-title-block">
            <span class="skill-cert-kicker">SERTIFIKAT PENYELESAIAN</span>
            <h1>Certificate of Completion</h1>
            <p>Diberikan kepada</p>
        </div>

        <section class="skill-cert-recipient">
            <h2>{{ $child?->name ?? 'Nama Siswa' }}</h2>
            <span class="skill-cert-name-line"></span>

            <p>
                atas keberhasilan menyelesaikan kelas
            </p>

            <h3>{{ $kelas?->title ?? 'Kelas SKILLPATH' }}</h3>
            <small>{{ $categories }}</small>
        </section>

        <section class="skill-cert-achievement">
            <div>
                <span>Nilai Akhir</span>
                <strong>
                    {{ $certificate->final_score !== null
                        ? number_format((float) $certificate->final_score, 1)
                        : '—' }}
                </strong>
            </div>

            <div>
                <span>Tanggal Terbit</span>
                <strong>{{ $certificate->issued_at?->translatedFormat('d F Y') ?? '—' }}</strong>
            </div>

            <div>
                <span>Status Dokumen</span>
                <strong>{{ $isRevoked ? 'Dicabut' : 'Aktif' }}</strong>
            </div>
        </section>

        <footer class="skill-cert-footer">
            <div class="skill-cert-signature">
                <span class="signature-line"></span>
                <strong>{{ $instructorName }}</strong>
                <small>Pengajar Kelas</small>
            </div>

            <div class="skill-cert-seal" aria-hidden="true">
                <span>S</span>
                <small>SKILLPATH</small>
            </div>

            <div class="skill-cert-signature">
                <span class="signature-line"></span>
                <strong>{{ $issuer }}</strong>
                <small>Penerbit Sertifikat</small>
            </div>
        </footer>

        <div class="skill-cert-footnote">
            <span>Dokumen digital resmi SKILLPATH</span>
            <span>Anak usia 5–14 tahun</span>
        </div>

        @if($isRevoked)
            <div class="skill-cert-revoked-stamp">DICABUT</div>
        @endif
    </article>
</div>
