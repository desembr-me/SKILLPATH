@extends('layouts.app')
@section('title','Dashboard Pengajar | SKILLPATH')
@section('content')
<section class="dashboard-hero">
    <div class="container dashboard-hero-grid">
        <div>
            <span class="eyebrow">Dashboard Pengajar</span>
            <h1>Halo, {{ auth()->user()->name }}.</h1>
            <p>Kelola course, live class, progres siswa, dan pendapatan Anda.</p>
        </div>
    </div>
</section>
<section class="section dashboard-section">
    <div class="container">
        <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
            <article class="stat-card"><span>Course</span><strong>{{ $courses->count() }}</strong></article>
            <article class="stat-card"><span>Peserta unik</span><strong>{{ $studentCount }}</strong></article>
            <article class="stat-card"><span>Rata-rata Progres</span><strong>{{ $avgCompletion }}%</strong></article>
            <article class="stat-card"><span>Pendapatan</span><strong>Rp{{ number_format($revenue, 0, ',', '.') }}</strong></article>
            <article class="stat-card"><span>Pertanyaan terbaru</span><strong>{{ $questions->count() }}</strong></article>
        </div>

        <div class="instructor-quick-grid">
            <a href="{{ route('instructor.progress.index') }}" class="instructor-quick-card">
                <span>📈</span>
                <strong>Progres Siswa</strong>
                <small>Pantau progres belajar siswa per course</small>
            </a>
            <a href="{{ route('instructor.revenue.index') }}" class="instructor-quick-card">
                <span>💰</span>
                <strong>Pendapatan</strong>
                <small>Ringkasan pendapatan dari penjualan course</small>
            </a>
            <a href="{{ route('instructor.progress.index') }}" class="instructor-quick-card">
                <span>📋</span>
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

        <div class="two-column-section">
            <div>
                <div class="section-heading"><h2>Live class mendatang</h2></div>
                @forelse($upcoming as $s)
                    <div class="schedule-row">
                        <div>
                            <strong>{{ $s->title }}</strong>
                            <span>{{ $s->learningPath->title }} · {{ $s->starts_at->format('d M H:i') }} · {{ $s->bookings_count }} booking</span>
                        </div>
                        <details class="reschedule-toggle">
                            <summary>Ubah Jadwal</summary>
                            <form method="POST" action="{{ route('instructor.live.update', $s) }}" class="form-stack reschedule-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $s->title }}">
                                <input type="hidden" name="description" value="{{ $s->description }}">
                                <input type="hidden" name="meeting_url" value="{{ $s->meeting_url }}">
                                <input type="hidden" name="capacity" value="{{ $s->capacity }}">
                                <label><span>Mulai</span><input type="datetime-local" name="starts_at" value="{{ $s->starts_at->format('Y-m-d\TH:i') }}" required></label>
                                <label><span>Selesai</span><input type="datetime-local" name="ends_at" value="{{ $s->ends_at->format('Y-m-d\TH:i') }}" required></label>
                                <button class="btn btn-blue btn-small" type="submit">Simpan &amp; Kirim Notifikasi</button>
                            </form>
                        </details>
                    </div>
                @empty
                    <p>Belum ada jadwal.</p>
                @endforelse
            </div>
            <div>
                <div class="section-heading"><h2>Pertanyaan peserta</h2></div>
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
                            <span class="done-label">✓ Sudah dijawab</span>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
