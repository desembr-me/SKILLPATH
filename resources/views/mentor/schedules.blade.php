@extends('layouts.app')
@section('title', 'Kelola Jadwal')

@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Manajemen Kelas</span>
            <h1>Kelola Jadwal Kursus</h1>
            <p>Atur hari, jam, kapasitas kuota, ruangan, dan sesi pertemuan untuk setiap kelas yang Anda ampu.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a>
        </div>
    </div>

    <div class="stat-grid">
        <article>
            <span class="stat-icon tone-blue"><x-icon name="calendar" /></span>
            <div>
                <span>Total Jadwal</span>
                <b>{{ $schedules->count() }}</b>
                <small>Slot waktu aktif</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-green"><x-icon name="check" /></span>
            <div>
                <span>Status Buka</span>
                <b>{{ $schedules->where('status', 'open')->count() }}</b>
                <small>Menerima pendaftaran baru</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-orange"><x-icon name="child" /></span>
            <div>
                <span>Total Kapasitas</span>
                <b>{{ $schedules->sum('capacity') }}</b>
                <small>Total kuota seluruh slot</small>
            </div>
        </article>
        <article>
            <span class="stat-icon tone-pink"><x-icon name="sessions" /></span>
            <div>
                <span>Siswa Terisi</span>
                <b>{{ $schedules->sum(fn($s) => $s->enrollments->where('status', 'active')->count()) }}</b>
                <small>Siswa di kelas aktif</small>
            </div>
        </article>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Daftar Slot</span>
                    <h2>Jadwal & Sesi Pertemuan</h2>
                </div>
                <span class="helper-badge">{{ $schedules->count() }} Jadwal Tersedia</span>
            </div>

            @forelse($schedules as $schedule)
                <div class="course-admin-row" style="align-items: flex-start; padding: 18px 0;">
                    <div class="row-icon-vector" style="--row-accent:{{ $schedule->course->accent }}">
                        <x-icon :name="$schedule->course->category->slug" />
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                            <div>
                                <h3>{{ $schedule->course->title }}</h3>
                                <p style="margin:4px 0;">
                                    <b>{{ $days[$schedule->day_of_week] }}</b>, {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }} WIB
                                    @if($schedule->room) • Ruang: <b>{{ $schedule->room }}</b>@endif
                                </p>
                                <small style="color:var(--muted); display:block;">
                                    Periode: {{ $schedule->start_date ? $schedule->start_date->format('d M Y') : '-' }} s/d {{ $schedule->end_date ? $schedule->end_date->format('d M Y') : 'Selesai' }}
                                </small>
                            </div>
                            <span class="status-chip {{ $schedule->status === 'open' ? 'active' : ($schedule->status === 'full' ? 'pending' : 'locked') }}">
                                {{ $schedule->status === 'open' ? 'Buka' : ($schedule->status === 'full' ? 'Penuh' : 'Ditutup') }}
                            </span>
                        </div>

                        <div class="mini-tags" style="margin-top: 8px;">
                            <span>Kuota: {{ $schedule->enrollments->where('status', 'active')->count() }}/{{ $schedule->capacity }} Siswa</span>
                            <span>{{ $schedule->sessions->count() }} Sesi Dibuat</span>
                        </div>

                        {{-- Collapsible sessions preview --}}
                        @if($schedule->sessions->isNotEmpty())
                            <details style="margin-top:10px; background:#f8f8fb; border-radius:12px; padding:8px 12px; font-size:10px;">
                                <summary style="cursor:pointer; font-weight:600; color:var(--purple); list-style:none;">
                                    Lihat {{ $schedule->sessions->count() }} Sesi Pertemuan &#9662;
                                </summary>
                                <div style="display:grid; gap:6px; margin-top:8px; padding-top:6px; border-top:1px solid #eff0f4;">
                                    @foreach($schedule->sessions as $ses)
                                        <div style="display:flex; justify-content:space-between; align-items:center;">
                                            <span><b>Sesi {{ $ses->session_no }}:</b> {{ $ses->topic }}</span>
                                            <small style="color:var(--muted);">{{ $ses->session_date ? $ses->session_date->format('d M Y') : '' }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <div style="margin-top:8px;">
                                <form method="POST" action="{{ route('mentor.schedules.sessions.store', $schedule) }}" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-sm btn-soft"><x-icon name="sessions" /> Generate Sesi Otomatis</button>
                                </form>
                            </div>
                        @endif

                        {{-- Edit form toggle --}}
                        <details style="margin-top: 10px;">
                            <summary class="btn btn-sm btn-ghost" style="display:inline-flex; width:auto; cursor:pointer;">
                                <x-icon name="edit" /> Edit Detail Jadwal
                            </summary>
                            <form method="POST" action="{{ route('mentor.schedules.update', $schedule) }}" class="form-grid" style="margin-top:12px; background:#faf9ff; border:1px solid #ece8fb; border-radius:14px; padding:14px;">
                                @csrf
                                @method('PUT')
                                <label>Hari Kelas
                                    <select name="day_of_week" required>
                                        @foreach($days as $idx => $dayName)
                                            <option value="{{ $idx }}" {{ $schedule->day_of_week == $idx ? 'selected' : '' }}>{{ $dayName }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>Ruangan / Lokasi
                                    <input name="room" value="{{ old('room', $schedule->room) }}" placeholder="Contoh: Studio Musik 1">
                                </label>
                                <label>Jam Mulai (HH:MM)
                                    <input type="time" name="start_time" value="{{ substr($schedule->start_time, 0, 5) }}" required>
                                </label>
                                <label>Jam Selesai (HH:MM)
                                    <input type="time" name="end_time" value="{{ substr($schedule->end_time, 0, 5) }}" required>
                                </label>
                                <label>Tanggal Mulai
                                    <input type="date" name="start_date" value="{{ optional($schedule->start_date)->format('Y-m-d') }}" required>
                                </label>
                                <label>Tanggal Selesai
                                    <input type="date" name="end_date" value="{{ optional($schedule->end_date)->format('Y-m-d') }}">
                                </label>
                                <label>Kapasitas Kuota
                                    <input type="number" name="capacity" min="1" max="100" value="{{ $schedule->capacity }}" required>
                                </label>
                                <label>Status Jadwal
                                    <select name="status" required>
                                        <option value="open" {{ $schedule->status === 'open' ? 'selected' : '' }}>Buka (Pendaftaran Aktif)</option>
                                        <option value="full" {{ $schedule->status === 'full' ? 'selected' : '' }}>Penuh</option>
                                        <option value="closed" {{ $schedule->status === 'closed' ? 'selected' : '' }}>Ditutup (Nonaktif)</option>
                                    </select>
                                </label>
                                <div style="grid-column: 1 / -1; display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                    <button class="btn btn-primary btn-sm"><x-icon name="check" /> Simpan Perubahan</button>
                                </div>
                            </form>
                            @if($schedule->enrollments->where('status', 'active')->isEmpty())
                                <form method="POST" action="{{ route('mentor.schedules.destroy', $schedule) }}" style="margin-top:6px;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-ghost" style="color:var(--danger);"><x-icon name="trash" /> Hapus Jadwal Ini</button>
                                </form>
                            @endif
                        </details>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <x-icon name="calendar" />
                    <div>
                        <b>Belum ada jadwal yang dibuat</b>
                        <span>Gunakan formulir di sebelah kanan untuk menambahkan slot jadwal kelas baru.</span>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Tambah Slot</span>
                    <h2>Tambah Jadwal Baru</h2>
                </div>
            </div>

            <form method="POST" action="{{ route('mentor.schedules.store') }}" class="profile-form">
                @csrf
                <label>Pilih Course
                    <select name="course_id" required>
                        <option value="">-- Pilih Course --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }} ({{ $c->category->name }})</option>
                        @endforeach
                    </select>
                </label>

                <div class="form-grid" style="margin:0;">
                    <label>Hari Kelas
                        <select name="day_of_week" required>
                            @foreach($days as $idx => $dayName)
                                <option value="{{ $idx }}" {{ old('day_of_week') == $idx ? 'selected' : '' }}>{{ $dayName }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Ruangan
                        <input name="room" value="{{ old('room') }}" placeholder="Contoh: Studio 1 Kemang">
                    </label>
                    <label>Jam Mulai (HH:MM)
                        <input type="time" name="start_time" value="{{ old('start_time', '10:00') }}" required>
                    </label>
                    <label>Jam Selesai (HH:MM)
                        <input type="time" name="end_time" value="{{ old('end_time', '11:30') }}" required>
                    </label>
                    <label>Tanggal Mulai
                        <input type="date" name="start_date" value="{{ old('start_date', now()->addDays(7)->format('Y-m-d')) }}" required>
                    </label>
                    <label>Tanggal Selesai
                        <input type="date" name="end_date" value="{{ old('end_date', now()->addMonths(3)->format('Y-m-d')) }}">
                    </label>
                    <label>Kapasitas Siswa
                        <input type="number" name="capacity" min="1" max="100" value="{{ old('capacity', 10) }}" required>
                    </label>
                    <label>Status
                        <select name="status" required>
                            <option value="open" selected>Buka (Pendaftaran Aktif)</option>
                            <option value="full">Penuh</option>
                            <option value="closed">Ditutup</option>
                        </select>
                    </label>
                </div>

                <div class="form-actions" style="margin-top:14px;">
                    <button class="btn btn-primary full"><x-icon name="plus" /> Simpan & Buat Sesi Otomatis</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
