@extends('admin.layouts.app')

@section('title', 'Manajemen Sertifikat | Admin SKILLPATH')
@section('page-title', 'Manajemen Sertifikat')

@section('content')
<x-admin.feature-header
    eyebrow="Dokumen Kelulusan"
    title="Manajemen sertifikat"
    description="Kelola penerbitan, status, pencabutan, pencarian, dan ekspor sertifikat peserta."
>
    <x-slot:actions>
        <a class="admin-btn secondary" href="{{ route('admin.certificates.export', request()->query()) }}">Ekspor CSV</a>
        <a class="admin-btn primary" href="{{ route('admin.certificates.create') }}">Terbitkan Sertifikat</a>
    </x-slot:actions>
</x-admin.feature-header>

<div class="admin-metric-grid">
    <x-admin.metric-card label="Sertifikat Aktif" :value="number_format($stats['active'])" :hint="'Dari '.$stats['total'].' total sertifikat'" tone="green" />
    <x-admin.metric-card label="Terbit Bulan Ini" :value="number_format($stats['issued_this_month'])" :hint="now()->translatedFormat('F Y')" tone="blue" />
    <x-admin.metric-card label="Siap Diterbitkan" :value="number_format($stats['eligible'])" hint="Peserta sudah memenuhi syarat" tone="yellow" />
    <x-admin.metric-card label="Dicabut" :value="number_format($stats['revoked'])" hint="Dapat diaktifkan kembali" tone="red" />
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Arsip Sertifikat"
        title="Daftar sertifikat"
        description="Cari berdasarkan peserta, orang tua, kelas, nomor sertifikat, status, dan tanggal terbit."
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($certificates->total()) }} hasil</span>
        </x-slot:actions>
    </x-admin.section-header>

    <form class="admin-filter-panel" method="GET" action="{{ route('admin.certificates.index') }}">
        <div class="admin-filter-grid certificate-filter-grid">
            <label class="admin-filter-field">
                <span>Cari sertifikat</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Nomor, peserta, orang tua, atau kelas">
            </label>

            <label class="admin-filter-field">
                <span>Status</span>
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="revoked" @selected(request('status') === 'revoked')>Dicabut</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Kelas</span>
                <select name="course_id">
                    <option value="">Semua kelas</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Dari tanggal</span>
                <input type="date" name="from" value="{{ request('from') }}">
            </label>

            <label class="admin-filter-field">
                <span>Sampai tanggal</span>
                <input type="date" name="to" value="{{ request('to') }}">
            </label>
        </div>

        <div class="admin-filter-actions">
            <button class="admin-btn primary" type="submit">Terapkan Filter</button>
            <a class="admin-btn ghost" href="{{ route('admin.certificates.index') }}">Reset</a>
        </div>
    </form>

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table certificate-table">
            <thead>
            <tr>
                <th>Nomor Sertifikat</th>
                <th>Peserta</th>
                <th>Kelas</th>
                <th>Kehadiran</th>
                <th>Tanggal Terbit</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($certificates as $certificate)
                <tr>
                    <td>
                        <a class="admin-inline-link" href="{{ route('admin.certificates.show', $certificate) }}">
                            {{ $certificate->certificate_number }}
                        </a>
                    </td>
                    <td>
                        <div class="admin-identity-cell compact">
                            <span class="admin-avatar-sm">{{ strtoupper(substr($certificate->childProfile?->name ?? 'S', 0, 1)) }}</span>
                            <div>
                                <strong>{{ $certificate->childProfile?->name ?? 'Profil tidak tersedia' }}</strong>
                                <small>{{ $certificate->childProfile?->user?->name ?? 'Orang tua tidak tersedia' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $certificate->learningPath?->title ?? 'Kelas tidak tersedia' }}</strong>
                        <small class="admin-cell-help">{{ $certificate->learningPath?->instructor?->name ?? 'Pengajar tidak tersedia' }}</small>
                    </td>
                    <td>{{ $certificate->final_score !== null ? number_format($certificate->final_score, 1) : '—' }}</td>
                    <td>{{ $certificate->issued_at?->translatedFormat('d M Y') ?? '—' }}</td>
                    <td>
                        <span class="admin-status {{ $certificate->status }}">
                            {{ $certificate->status === 'active' ? 'AKTIF' : 'DICABUT' }}
                        </span>
                    </td>
                    <td>
                        <a class="admin-btn small ghost" href="{{ route('admin.certificates.show', $certificate) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="admin-empty-state">
                            <strong>Belum ada sertifikat.</strong>
                            <span>Terbitkan sertifikat untuk peserta yang sudah memenuhi syarat.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $certificates->links() }}</div>
</section>
@endsection
