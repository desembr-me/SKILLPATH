@extends('layouts.admin')
@section('title', 'Jadwal Pengajar')

@section('content')
<section class="admin-schedules-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">OPERASIONAL KELAS OFFLINE</span>
            <h1>Jadwal Pengajar & Studio</h1>
            <p>Kelola alokasi ruangan studio offline, jadwal mingguan pengajar, serta kapasitas kuota siswa per kelas.</p>
        </div>
        <div class="admin-action-group">
            <button class="btn-admin-primary" onclick="toggleAddScheduleModal()">
                <x-icon name="plus" />
                <span>Tambah Jadwal Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter Day -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.schedules.index') }}" class="admin-filter-tab {{ $currentDay === 'all' ? 'active' : '' }}">
                    Semua Hari ({{ $totalCount }})
                </a>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
                    <a href="{{ route('admin.schedules.index', ['day' => $d]) }}" class="admin-filter-tab {{ $currentDay === $d ? 'active' : '' }}">
                        {{ $d }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div id="addScheduleModal" class="admin-card" style="display: none; border: 2px solid #5b36f5;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-family:'Fredoka'; font-size:20px; margin:0; color:#120e2e;">Tambah Jadwal Kelas Baru</h3>
            <button type="button" onclick="toggleAddScheduleModal()" style="background:none; border:none; color:#8a84ab; font-size:18px; cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.schedules.store') }}">
            @csrf
            <div class="form-grid">
                <label style="grid-column: span 2;">Pilih Course Belajar
                    <select name="course_id" required>
                        <option value="">Pilih Course</option>
                        @foreach($courses as $crs)
                            <option value="{{ $crs->id }}">{{ $crs->title }} (Mentor: {{ $crs->instructor->name ?? 'SkillPath' }})</option>
                        @endforeach
                    </select>
                </label>
                <label>Hari Pelaksanaan
                    <select name="day_of_week" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </label>
                <label>Jam Mulai (WIB)
                    <input type="time" name="start_time" value="09:00" required>
                </label>
                <label>Jam Selesai (WIB)
                    <input type="time" name="end_time" value="10:30" required>
                </label>
                <label>Ruangan / Studio
                    <input name="room" value="Studio Utama A" required placeholder="Contoh: Studio Lab Komputer 1">
                </label>
                <label>Kapasitas Kuota Siswa
                    <input type="number" name="capacity" value="8" min="1" max="50" required>
                </label>
            </div>
            <div style="display:flex; gap:10px; margin-top:16px;">
                <button type="submit" class="btn-admin-primary">Simpan Jadwal</button>
                <button type="button" class="btn-admin-white" onclick="toggleAddScheduleModal()">Batal</button>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Hari & Jam</th>
                        <th>Course</th>
                        <th>Pengajar</th>
                        <th>Studio / Ruangan</th>
                        <th>Kapasitas</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sch)
                        <tr>
                            <td>
                                <span class="status-pill paid" style="font-size:11.5px; font-weight:600;">
                                    {{ $sch->day_of_week }}
                                </span>
                                <b style="color:#120e2e; display:block; margin-top:4px;">
                                    {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB
                                </b>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ $sch->course->title ?? '-' }}</b>
                                <small style="color:#8a84ab;">{{ $sch->course->sessions_count ?? '-' }} Pertemuan</small>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ $sch->instructor->name ?? 'SkillPath' }}</b>
                                <small style="color:#8a84ab;">{{ $sch->instructor->phone ?? '-' }}</small>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#5c567e;">{{ $sch->room ?: 'Studio 1' }}</span>
                                <small style="color:#8a84ab; display:block;">{{ $sch->course->location_name ?? 'SkillPath Hub' }}</small>
                            </td>
                            <td>
                                <span style="font-size:12px; font-weight:600; color:#166534;">
                                    {{ $sch->enrollments->count() }} / {{ $sch->capacity }} Terisi
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.schedules.destroy', $sch) }}" onsubmit="return confirm('Hapus jadwal ini?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--danger);" title="Hapus Jadwal">
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada data jadwal untuk hari yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</section>

<script>
function toggleAddScheduleModal() {
    var modal = document.getElementById('addScheduleModal');
    modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
