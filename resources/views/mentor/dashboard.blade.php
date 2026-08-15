@extends('layouts.app')
@section('title','Dashboard Pengajar')
@section('content')
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Dashboard Pengajar</span><h1>Halo, {{ auth()->user()->name }}</h1><p>Kelola course, kehadiran, kredit sesi, ujian, retake, dan evaluasi siswa.</p></div></div>
    <div class="stat-grid">
        <article><span class="stat-icon"><x-icon name="sessions" /></span><div><span>Course aktif</span><b>{{ $courses->count() }}</b><small>Course yang sedang berjalan</small></div></article>
        <article><span class="stat-icon"><x-icon name="child" /></span><div><span>Siswa aktif</span><b>{{ $students }}</b><small>Siswa dalam course aktif</small></div></article>
        <article><span class="stat-icon"><x-icon name="star" /></span><div><span>Rating mentor</span><b>{{ $rating ?: '0.0' }}</b><small>Rata-rata review orang tua</small></div></article>
        <article><span class="stat-icon"><x-icon name="review" /></span><div><span>Fokus operasional</span><b>Aktif</b><small>Kehadiran, evaluasi, dan ujian</small></div></article>
    </div>
    <div class="dashboard-grid">
        <div class="panel"><div class="panel-heading"><div><span class="panel-kicker">Kelas</span><h2>Course Saya</h2></div></div>
            @foreach($courses as $course)<div class="course-admin-row"><div class="row-icon-vector" style="--row-accent:{{ $course->accent }}"><x-icon :name="$course->category->slug" /></div><div><h3>{{ $course->title }}</h3><p>{{ $course->category->name }} • {{ $course->schedules->count() }} jadwal</p></div><span class="status-chip active">Aktif</span></div>@endforeach
        </div>
        <div class="panel mentor-principle"><span class="principle-icon"><x-icon name="credit" /></span><h2>Prinsip Kredit Sesi</h2><p class="mentor-note">Tandai <b>credit eligible</b> hanya saat sesi memenuhi kebijakan, misalnya sakit atau perubahan jadwal yang disetujui. Sistem akan membuat kredit otomatis.</p></div>
    </div>

    <div class="panel mentor-tools">
        <div class="panel-heading"><div><span class="panel-kicker">Kehadiran</span><h2>Catat Kehadiran & Kredit</h2></div><span class="helper-badge">Kredit dibuat otomatis jika eligible</span></div>
        @forelse($enrollments as $enrollment)
            @php($session = $enrollment->schedule->sessions->first())
            @if($session)
            <form class="tool-form" method="POST" action="{{ route('mentor.attendance.store') }}">@csrf
                <div><b>{{ $enrollment->child->name }}</b><small>{{ $enrollment->course->title }} • Sesi {{ $session->session_no }}</small></div>
                <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}"><input type="hidden" name="course_session_id" value="{{ $session->id }}">
                <select name="status"><option value="present">Hadir</option><option value="excused">Izin</option><option value="absent">Tidak Hadir</option></select>
                <input name="absence_reason" placeholder="Alasan jika tidak hadir">
                <label class="compact-check"><input type="checkbox" name="credit_eligible" value="1"> Buat kredit sesi</label>
                <button class="btn btn-soft">Simpan</button>
            </form>
            @endif
        @empty<div class="empty-state compact-empty"><x-icon name="child" /><div><b>Belum ada siswa aktif</b><span>Enrollment aktif akan muncul di sini.</span></div></div>@endforelse
    </div>

    <div class="panel mentor-tools">
        <div class="panel-heading"><div><span class="panel-kicker">Evaluasi</span><h2>Nilai Ujian & Retake</h2></div><span class="helper-badge">Sertifikat mengikuti passing grade</span></div>
        @forelse($enrollments as $enrollment)
            @php($exam = $enrollment->course->exams->first())
            @if($exam)
            <form class="tool-form" method="POST" action="{{ route('mentor.exam-attempts.store') }}">@csrf
                <div><b>{{ $enrollment->child->name }}</b><small>{{ $enrollment->course->title }} • Passing {{ $exam->passing_score }} • {{ $enrollment->examAttempts->where('exam_id',$exam->id)->count() }}/{{ $exam->max_attempts }} percobaan</small></div>
                <input type="hidden" name="exam_id" value="{{ $exam->id }}"><input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                <input type="number" name="score" min="0" max="100" placeholder="Nilai" required>
                <input name="mentor_feedback" placeholder="Feedback mentor">
                <button class="btn btn-primary">Simpan Nilai</button>
            </form>
            @endif
        @empty<div class="empty-state compact-empty"><x-icon name="certificate" /><div><b>Belum ada enrollment</b><span>Data ujian akan muncul ketika siswa aktif tersedia.</span></div></div>@endforelse
    </div>
</section>
@endsection
