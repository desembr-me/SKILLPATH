@extends('layouts.instructor')
@section('title','Progres Siswa | SKILLPATH')
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Dashboard Pengajar</span>
    <h1>Progres Siswa</h1>
    <p>Pantau progres belajar siswa pada course yang Anda ampu.</p>
</div>

<div class="progress-student-list">
    @forelse($enrollments as $enrollment)
        <div class="progress-student-row">
            <div>
                <strong>{{ $enrollment->childProfile->name }}</strong>
                <span>{{ $enrollment->learningPath->title }} · Terdaftar {{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('d M Y') }}</span>
            </div>
            <div class="progress-student-meter">
                <div class="progress-track"><span style="width: {{ $enrollment->completion_rate }}%"></span></div>
                <strong>{{ $enrollment->completion_rate }}%</strong>
            </div>
            <a class="btn btn-ghost btn-small" href="{{ route('instructor.progress.show', $enrollment) }}">Lihat Laporan</a>
        </div>
    @empty
        <div class="empty-card"><h2>Belum ada siswa yang terdaftar pada course Anda.</h2></div>
    @endforelse
</div>
@endsection
