@extends('layouts.instructor')
@section('title','Dashboard Pengajar | SKILLPATH')
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Dashboard Pengajar</span>
    <h1>Halo, {{ auth()->user()->name }}.</h1>
    <p>Kelola course, progres siswa, dan pendapatan Anda.</p>
</div>

<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-top: 0;">
    <article class="stat-card"><span>Course</span><strong>{{ $courses->count() }}</strong></article>
    <article class="stat-card"><span>Peserta unik</span><strong>{{ $studentCount }}</strong></article>
    <article class="stat-card"><span>Rata-rata Progres</span><strong>{{ $avgCompletion }}%</strong></article>
    <article class="stat-card"><span>Pendapatan</span><strong>Rp{{ number_format($revenue, 0, ',', '.') }}</strong></article>
    <article class="stat-card"><span>Pertanyaan terbaru</span><strong>{{ $questions->count() }}</strong></article>
</div>

<div class="instructor-quick-grid">
    <a href="{{ route('instructor.progress.index') }}" class="instructor-quick-card">
        <iconify-icon icon="mdi:chart-line"></iconify-icon>
        <strong>Progres Siswa</strong>
        <small>Pantau progres belajar siswa per course</small>
    </a>
    <a href="{{ route('instructor.revenue.index') }}" class="instructor-quick-card">
        <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
        <strong>Pendapatan</strong>
        <small>Ringkasan pendapatan dari penjualan course</small>
    </a>
    <a href="{{ route('instructor.progress.index') }}" class="instructor-quick-card">
        <iconify-icon icon="mdi:clipboard-text-outline"></iconify-icon>
        <strong>Laporan Progres</strong>
        <small>Laporan rinci per siswa untuk evaluasi</small>
    </a>
</div>

<div class="section-heading dashboard-heading">
    <span class="eyebrow">Course Saya</span>
    <h2>Kelola course</h2>
</div>
<div class="instructor-course-table">
    @foreach($courses as $c)
        <div class="instructor-course-row">
            <div>
                <strong>{{ $c->title }}</strong>
                <span>{{ $c->enrollments_count }} peserta · {{ $c->reviews_count }} ulasan</span>
            </div>
            <a class="btn btn-ghost" href="{{ route('instructor.courses.edit', $c) }}">Kelola</a>
        </div>
    @endforeach
</div>

<div class="section-heading"><h2>Pertanyaan peserta</h2></div>
<div class="instructor-question-grid">
    @foreach($questions as $q)
        <article class="question-card">
            <strong>{{ $q->learningPath->title }}</strong>
            <p>{{ $q->question }}</p>
            @if($q->answers->isEmpty())
                <form method="POST" action="{{ route('instructor.questions.answer', $q) }}" class="form-stack">
                    @csrf
                    <textarea name="answer" rows="2" required placeholder="Jawab peserta..."></textarea>
                    <button class="btn btn-dark" type="submit">Jawab</button>
                </form>
            @else
                <span class="done-label"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon> Sudah dijawab</span>
            @endif
        </article>
    @endforeach
</div>
@endsection
