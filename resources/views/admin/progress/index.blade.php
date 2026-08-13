@extends('admin.layouts.app')

@section('title', 'Monitoring Kehadiran Peserta | Admin SKILLPATH')
@section('page-title', 'Monitoring Kehadiran Peserta')

@section('content')
<x-admin.feature-header
    eyebrow="Operasional Kelas"
    title="Monitoring kehadiran peserta"
    description="Pantau pendaftaran kelas, booking jadwal, kehadiran, dan peserta yang membutuhkan perhatian dalam satu tampilan."
>
    <x-slot:actions>
        <a class="admin-btn secondary" href="{{ route('admin.progress.export', request()->query()) }}">Ekspor CSV</a>
    </x-slot:actions>
</x-admin.feature-header>

<div class="admin-metric-grid">
    <x-admin.metric-card
        label="Peserta Termonitor"
        :value="number_format($stats['students'])"
        hint="Memiliki kelas aktif"
        tone="blue"
    />
    <x-admin.metric-card
        label="Rata-rata Kehadiran"
        :value="$stats['average_attendance'].'%'"
        :hint="$stats['active'].' peserta dengan jadwal aktif'"
        tone="yellow"
    />
    <x-admin.metric-card
        label="Perlu Perhatian"
        :value="number_format($stats['needs_attention'])"
        :hint="$stats['needs_attention'].' perlu ditinjau'"
        tone="red"
    />
    <x-admin.metric-card
        label="Program Selesai"
        :value="number_format($stats['completed'])"
        hint="Tidak ada jadwal mendatang"
        tone="green"
    />
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Daftar Peserta"
        title="Kehadiran kelas"
        description="Gunakan filter untuk menemukan peserta berdasarkan kelas, usia, kondisi kehadiran, atau sesi terakhir."
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($students->total()) }} peserta</span>
        </x-slot:actions>
    </x-admin.section-header>

    <form class="admin-filter-panel" method="GET" action="{{ route('admin.progress.index') }}">
        <div class="admin-filter-grid progress-filter-grid">
            <label class="admin-filter-field">
                <span>Cari peserta</span>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Nama peserta, orang tua, atau email"
                >
            </label>

            <label class="admin-filter-field">
                <span>Kelas</span>
                <select name="course">
                    <option value="">Semua kelas</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course') === (string) $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Kelompok usia</span>
                <select name="age_group">
                    <option value="">Usia 5–14</option>
                    <option value="5_7" @selected(request('age_group') === '5_7')>5–7 tahun</option>
                    <option value="8_10" @selected(request('age_group') === '8_10')>8–10 tahun</option>
                    <option value="11_14" @selected(request('age_group') === '11_14')>11–14 tahun</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Status</span>
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="completed" @selected(request('status') === 'completed')>Program Selesai</option>
                    <option value="needs_attention" @selected(request('status') === 'needs_attention')>Perlu perhatian</option>
                    <option value="not_scheduled" @selected(request('status') === 'not_scheduled')>Belum ada jadwal</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Urutkan</span>
                <select name="sort">
                    <option value="name" @selected(request('sort', 'name') === 'name')>Nama A–Z</option>
                    <option value="attendance_low" @selected(request('sort') === 'attendance_low')>Kehadiran terendah</option>
                    <option value="attendance_high" @selected(request('sort') === 'attendance_high')>Kehadiran tertinggi</option>
                    <option value="recent" @selected(request('sort') === 'recent')>Sesi terbaru</option>
                    <option value="booking_high" @selected(request('sort') === 'booking_high')>Booking terbanyak</option>
                </select>
            </label>
        </div>

        <div class="admin-filter-actions">
            <button class="admin-btn primary" type="submit">Terapkan Filter</button>
            <a class="admin-btn ghost" href="{{ route('admin.progress.index') }}">Reset</a>
        </div>
    </form>

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table progress-table">
            <thead>
            <tr>
                <th>Peserta</th>
                <th>Kelas</th>
                <th>Booking</th>
                <th>Kehadiran</th>
                <th>Hadir</th>
                <th>Tidak Hadir</th>
                <th>Sesi Terakhir</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($students as $row)
                <tr>
                    <td>
                        <div class="admin-identity-cell">
                            <span class="admin-avatar-sm">{{ strtoupper(substr($row['child']->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $row['child']->name }}</strong>
                                <small>Usia {{ $row['child']->age }} · {{ $row['child']->user?->name ?? 'Orang tua tidak tersedia' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong>{{ $row['enrollment_count'] }}</strong>
                        <small class="admin-cell-help">kelas aktif</small>
                    </td>
                    <td>
                        <strong>{{ $row['booking_count'] }}/{{ $row['session_count'] }}</strong>
                        <small class="admin-cell-help">{{ $row['unbooked_upcoming'] }} jadwal belum dipesan</small>
                    </td>
                    <td>
                        <div class="admin-progress-cell">
                            <div>
                                <strong>{{ $row['attendance_rate'] !== null ? $row['attendance_rate'].'%' : '—' }}</strong>
                            </div>
                            <div class="admin-progress-track">
                                <span style="width: {{ $row['attendance_rate'] ?? 0 }}%"></span>
                            </div>
                        </div>
                    </td>
                    <td>{{ number_format($row['attended_count']) }}</td>
                    <td>{{ number_format($row['absent_count']) }}</td>
                    <td>
                        @if($row['last_session_at'])
                            <strong>{{ $row['last_session_at']->translatedFormat('d M Y') }}</strong>
                            <small class="admin-cell-help">{{ $row['last_session_at']->diffForHumans() }}</small>
                        @else
                            <span class="admin-muted">Belum ada</span>
                            <small class="admin-cell-help">Belum ada sesi tercatat</small>
                        @endif
                    </td>
                    <td>
                        <span class="admin-status {{ $row['status'] }}">{{ $row['status_label'] }}</span>
                        @if($row['attention_reason'])
                            <small class="admin-status-note">{{ $row['attention_reason'] }}</small>
                        @endif
                    </td>
                    <td>
                        <a class="admin-btn small ghost" href="{{ route('admin.progress.show', $row['child']) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="admin-empty-state">
                            <strong>Tidak ada peserta ditemukan.</strong>
                            <span>Ubah filter untuk menampilkan data lain.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">
        {{ $students->links() }}
    </div>
</section>

<div class="admin-info-note">
    <strong>Aturan monitoring kehadiran</strong>
    <span>Aktif: masih ada jadwal mendatang. Perlu perhatian: ada ketidakhadiran atau jadwal mendatang yang belum dipesan.</span>
</div>
@endsection
