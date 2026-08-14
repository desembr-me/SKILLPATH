@extends('layouts.instructor')
@section('title','Laporan Progres · '.$enrollment->childProfile->name)
@section('content')
<div class="instructor-page-header">
    <a class="back-link" href="{{ route('instructor.progress.index') }}">← Kembali ke Progres Siswa</a>
    <span class="eyebrow">Laporan Progres Siswa</span>
    <h1>{{ $enrollment->childProfile->name }}</h1>
    <p>{{ $enrollment->learningPath->title }}</p>
</div>

<div class="two-column-section" style="margin-top: 0;">
        <div>
            <div class="content-card">
                <h2>Ringkasan</h2>
                <div class="progress-line">
                    <div><span>Tingkat Penyelesaian</span><strong>{{ $completionRate }}%</strong></div>
                    <div class="progress-track large"><span style="width: {{ $completionRate }}%"></span></div>
                </div>
                <p>{{ $completedActivities }} dari {{ $totalActivities }} aktivitas selesai.</p>
                @if($scores->count())
                    <p>Rata-rata nilai: <strong>{{ number_format($scores->avg(), 1) }}</strong></p>
                @endif
            </div>

            <div class="content-card">
                <h2>Detail Aktivitas</h2>
                @forelse($enrollment->learningPath->modules as $module)
                    <div style="margin-bottom: 18px;">
                        <strong>{{ $module->title }}</strong>
                        <div class="instructor-course-table" style="margin-top: 10px;">
                            @foreach($module->activities as $activity)
                                @php($activityProgress = $progress->get($activity->id))
                                <div class="instructor-course-row">
                                    <div>
                                        <strong>{{ $activity->title }}</strong>
                                        <span>{{ $activity->type }}@if($activityProgress?->score !== null) · Nilai {{ $activityProgress->score }}@endif</span>
                                    </div>
                                    @if($activityProgress && $activityProgress->status === 'completed')
                                        <span class="done-label"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Selesai</span>
                                    @else
                                        <span class="done-label pending"><iconify-icon icon="mdi:circle-outline"></iconify-icon> Belum selesai</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p>Kelas ini belum memiliki rangkaian aktivitas.</p>
                @endforelse
            </div>
        </div>

        <aside>
            <div class="content-card">
                <h2>Info Siswa</h2>
                <p><strong>Nama:</strong> {{ $enrollment->childProfile->name }}</p>
                <p><strong>Umur:</strong> {{ $enrollment->childProfile->age ?? '-' }} tahun</p>
                <p><strong>Orang Tua:</strong> {{ $enrollment->childProfile->user->name }}</p>
                <p><strong>Terdaftar:</strong> {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('d M Y') }}</p>
                <button class="btn btn-dark btn-full" type="button" onclick="window.print()"><iconify-icon icon="mdi:printer-outline"></iconify-icon> Cetak Laporan</button>
            </div>
        </aside>
    </div>
@endsection
