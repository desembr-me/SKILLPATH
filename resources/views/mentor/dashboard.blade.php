@extends('layouts.app')
@section('title', 'Dashboard Pengajar')

@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Dashboard Pengajar</span>
            <h1>Halo, {{ auth()->user()->name }}</h1>
            <p>Kelola kelas, jadwal, kehadiran siswa, kredit sesi, ujian, dan pantau pendapatan Anda.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('mentor.schedules.index') }}"><x-icon name="calendar" /> Kelola Jadwal</a>
            <a class="btn btn-soft" href="{{ route('mentor.earnings') }}"><x-icon name="earnings" /> Pendapatan</a>
            <a class="btn btn-soft" href="{{ route('mentor.reviews') }}"><x-icon name="review" /> Ulasan</a>
        </div>
    </div>

    {{-- Reschedule Request Notification Banner --}}
    @if($pendingReschedules->isNotEmpty())
        <div class="panel" style="border: 2px solid var(--purple); background: #fdfaff; margin-bottom: 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <span class="stat-icon tone-pink" style="width:42px; height:42px; font-size:16px;">
                        <x-icon name="bell" />
                    </span>
                    <div>
                        <h2 style="font-size:15px; margin:0; color:var(--purple);">Ada {{ $pendingReschedules->count() }} Permintaan Perubahan Jadwal Baru</h2>
                        <p style="margin:2px 0 0 0; color:var(--ink-2); font-size:10.5px;">
                            Orang tua <b>{{ optional($pendingReschedules->first()->parent)->name }}</b> mengajukan perubahan jadwal untuk <b>{{ optional(optional($pendingReschedules->first()->enrollment)->child)->name }}</b>.
                        </p>
                    </div>
                </div>
                <a class="btn btn-primary btn-sm" href="{{ route('mentor.reschedules.index') }}">
                    Tinjau & Tanggapi <x-icon name="arrow-right" />
                </a>
            </div>
        </div>
    @endif

    <div class="stat-grid">
        <article>
            <span class="stat-icon tone-blue"><x-icon name="sessions" /></span>
            <div>
                <span>Course aktif</span>
                <b>{{ $courses->count() }}</b>
                <small>Course yang sedang berjalan</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-orange"><x-icon name="child" /></span>
            <div>
                <span>Siswa aktif</span>
                <b>{{ $students }}</b>
                <small>Siswa dalam kelas aktif</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-green"><x-icon name="earnings" /></span>
            <div>
                <span>Total Pendapatan</span>
                <b>Rp {{ number_format($totalEarnings, 0, ',', '.') }}</b>
                <small>Akumulasi bagi hasil</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-pink"><x-icon name="star" /></span>
            <div>
                <span>Rating mentor</span>
                <b>{{ $rating ? number_format((float)$rating, 1) : '0.0' }}</b>
                <small>Rata-rata review orang tua</small>
            </div>
        </article>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Kelas & Slot</span>
                    <h2>Course & Jadwal Saya</h2>
                </div>
                <a class="btn btn-ghost btn-sm" href="{{ route('mentor.schedules.index') }}">Kelola &rarr;</a>
            </div>
            @forelse($courses as $course)
                <div class="course-admin-row">
                    <div class="row-icon-vector" style="--row-accent:{{ $course->accent }}">
                        <x-icon :name="$course->category->slug" />
                    </div>
                    <div>
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->category->name }} • {{ $course->schedules->count() }} slot jadwal aktif</p>
                    </div>
                    <span class="status-chip active">Aktif</span>
                </div>
            @empty
                <div class="empty-state">
                    <x-icon name="book" />
                    <div>
                        <b>Belum ada course</b>
                        <span>Course Anda akan muncul di sini.</span>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="panel mentor-principle">
            <span class="principle-icon"><x-icon name="credit" /></span>
            <h2>Prinsip Kehadiran & Kredit Sesi</h2>
            <p class="mentor-note">
                Tandai <b>Buat kredit sesi</b> saat siswa izin karena alasan sah (misalnya sakit atau perubahan jadwal). Sistem SkillPath akan otomatis menerbitkan kredit pengganti untuk digunakan orang tua.
            </p>
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="panel" style="margin-top: 18px;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Siswa</span>
                <h2>Daftar Siswa Terdaftar</h2>
            </div>
            <span class="helper-badge">{{ $enrollments->count() }} Total Siswa</span>
        </div>
        @forelse($enrollments as $enrollment)
            <div class="child-row">
                <div class="child-avatar">{{ strtoupper(substr(optional($enrollment->child)->name ?? 'A', 0, 1)) }}</div>
                <div style="flex:1;">
                    <h3>{{ optional($enrollment->child)->name }}</h3>
                    <p style="margin:2px 0;">
                        {{ optional($enrollment->course)->title }}
                        @if($enrollment->schedule)
                            • {{ $days[$enrollment->schedule->day_of_week] }}, {{ substr($enrollment->schedule->start_time, 0, 5) }} WIB ({{ $enrollment->schedule->room }})
                        @endif
                    </p>
                    <div class="mini-tags">
                        <span>{{ ucfirst($enrollment->status) }}</span>
                        <span>{{ $enrollment->package_info['title'] ?? 'Paket 3 Bulan' }} ({{ $enrollment->total_sessions ?? ($enrollment->package_info['sessions'] ?? 12) }} Sesi)</span>
                        <span>{{ $enrollment->progress }}% progres belajar</span>
                    </div>
                </div>
                <a class="btn btn-soft btn-sm" href="{{ route('mentor.students.show', $enrollment) }}">Detail & Riwayat &rarr;</a>
            </div>
        @empty
            <div class="empty-state">
                <x-icon name="child" />
                <div>
                    <b>Belum ada siswa terdaftar</b>
                    <span>Siswa akan muncul setelah pendaftaran kursus aktif.</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Catat Kehadiran & Kredit (Perbaikan & Penyempurnaan) --}}
    <div class="panel mentor-tools" style="margin-top: 18px;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Presensi & Kehadiran</span>
                <h2>Catat Kehadiran & Kredit Sesi</h2>
            </div>
            <span class="helper-badge">Pilih sesi & simpan kehadiran</span>
        </div>

        @forelse($enrollments->where('status', 'active') as $enrollment)
            @php($sessions = optional($enrollment->schedule)->sessions ?? collect())
            @if($sessions->isNotEmpty())
                <form class="tool-form attendance-form" method="POST" action="{{ route('mentor.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">

                    <div>
                        <b style="display:block; font-size:12px;">{{ optional($enrollment->child)->name }}</b>
                        <small style="color:var(--muted); font-size:9.5px;">{{ optional($enrollment->course)->title }}</small>
                    </div>

                    <div>
                        <select name="course_session_id" required style="font-size:10.5px; padding:7px 8px; width:100%;">
                            @foreach($sessions as $s)
                                @php($att = $enrollment->attendance->firstWhere('course_session_id', $s->id))
                                <option value="{{ $s->id }}">
                                    Sesi {{ $s->session_no }} ({{ $s->session_date ? $s->session_date->format('d/m') : '-' }}) {{ $att ? '['.ucfirst($att->status).']' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" style="font-size:10.5px; padding:7px 8px; width:100%;">
                            <option value="present">Hadir</option>
                            <option value="excused">Izin</option>
                            <option value="absent">Tidak Hadir</option>
                            <option value="rescheduled">Jadwal Ulang</option>
                        </select>
                    </div>

                    <div>
                        <input name="absence_reason" placeholder="Alasan jika izin/absen" style="font-size:10.5px; padding:7px 8px; width:100%;">
                    </div>

                    <div>
                        <label class="compact-check" style="font-size:10px; cursor:pointer;" title="Terbitkan kredit sesi otomatis untuk kompensasi kehadiran">
                            <input type="checkbox" name="credit_eligible" value="1"> Buat Kredit
                        </label>
                    </div>

                    <div>
                        <button class="btn btn-sm btn-primary" type="submit"><x-icon name="check" /> Simpan</button>
                    </div>
                </form>
            @endif
        @empty
            <div class="empty-state compact-empty">
                <x-icon name="child" />
                <div>
                    <b>Belum ada siswa aktif</b>
                    <span>Presensi dapat dicatat setelah ada siswa yang terdaftar di kelas.</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Nilai Ujian & Retake --}}
    <div class="panel mentor-tools" style="margin-top: 18px;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Evaluasi</span>
                <h2>Nilai Ujian & Retake</h2>
            </div>
            <span class="helper-badge">Sertifikat terbit otomatis jika lulus passing grade</span>
        </div>

        @forelse($enrollments as $enrollment)
            @php($exam = optional(optional($enrollment->course)->exams)->first())
            @if($exam)
                <form class="tool-form exam-form" method="POST" action="{{ route('mentor.exam-attempts.store') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">

                    <div>
                        <b style="display:block; font-size:12px;">{{ optional($enrollment->child)->name }}</b>
                        <small style="color:var(--muted); font-size:9.5px;">
                            {{ optional($enrollment->course)->title }} • Passing: {{ $exam->passing_score }} • Percobaan: {{ $enrollment->examAttempts->where('exam_id', $exam->id)->count() }}/{{ $exam->max_attempts }}
                        </small>
                    </div>

                    <div>
                        <input type="number" name="score" min="0" max="100" placeholder="Nilai (0-100)" required style="font-size:10.5px; padding:7px 8px; width:100%;">
                    </div>

                    <div>
                        <input name="mentor_feedback" placeholder="Catatan evaluasi mentor..." style="font-size:10.5px; padding:7px 8px; width:100%;">
                    </div>

                    <div>
                        <button class="btn btn-primary btn-sm"><x-icon name="check" /> Simpan Nilai</button>
                    </div>
                </form>
            @endif
        @empty
            <div class="empty-state compact-empty">
                <x-icon name="certificate" />
                <div>
                    <b>Belum ada enrollment ujian</b>
                    <span>Data ujian akan muncul ketika siswa terdaftar tersedia.</span>
                </div>
            </div>
        @endforelse
    </div>
</section>
@endsection
