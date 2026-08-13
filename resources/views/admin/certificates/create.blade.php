@extends('admin.layouts.app')

@section('title', 'Terbitkan Sertifikat | Admin SKILLPATH')
@section('page-title', 'Terbitkan Sertifikat')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.certificates.index') }}">← Manajemen Sertifikat</a>
</div>

<x-admin.feature-header
    eyebrow="Validasi Kelulusan"
    title="Siswa siap menerima sertifikat"
    description="Hanya siswa dengan enrollment aktif dan seluruh aktivitas course telah selesai yang ditampilkan."
/>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Kandidat"
        title="Daftar siswa memenuhi syarat"
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($eligible->count()) }} kandidat</span>
        </x-slot:actions>
    </x-admin.section-header>

    <div class="admin-stack-list">
        @forelse($eligible as $item)
            @php($enrollment = $item['enrollment'])
            @php($evaluation = $item['evaluation'])

            <article class="admin-list-card certificate-candidate-card">
                <span class="admin-avatar-sm large">{{ strtoupper(substr($enrollment->childProfile->name, 0, 1)) }}</span>

                <div class="admin-list-content">
                    <div class="admin-list-heading">
                        <div>
                            <strong>{{ $enrollment->childProfile->name }}</strong>
                            <small>{{ $enrollment->learningPath->title }} · {{ $enrollment->learningPath->instructor?->name ?? 'Pengajar tidak tersedia' }}</small>
                        </div>
                        <span class="admin-status completed">SIAP</span>
                    </div>

                    <div class="admin-meta-row">
                        <span>Usia {{ $enrollment->childProfile->age }} tahun</span>
                        <span>{{ $evaluation['completed_activities'] }}/{{ $evaluation['total_activities'] }} aktivitas</span>
                        <span>Progres {{ $evaluation['progress_percent'] }}%</span>
                        <span>Nilai {{ $evaluation['final_score'] !== null ? number_format($evaluation['final_score'], 1) : '—' }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.certificates.store') }}">
                    @csrf
                    <input type="hidden" name="child_profile_id" value="{{ $enrollment->child_profile_id }}">
                    <input type="hidden" name="learning_path_id" value="{{ $enrollment->learning_path_id }}">
                    <button class="admin-btn primary" type="submit">Terbitkan</button>
                </form>
            </article>
        @empty
            <div class="admin-empty-state">
                <strong>Belum ada siswa yang siap diterbitkan sertifikat.</strong>
                <span>Siswa yang belum 100% atau sudah memiliki sertifikat tidak ditampilkan.</span>
            </div>
        @endforelse
    </div>
</section>
@endsection
