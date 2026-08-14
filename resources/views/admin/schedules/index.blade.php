@extends('admin.layouts.app')

@section('title', 'Jadwal Pengajaran | Admin SKILLPATH')
@section('page-title', 'Jadwal Pengajaran')

@section('content')
<x-admin.feature-header
    eyebrow="Operasional Kelas"
    title="Jadwal pengajaran"
    description="Kelola sesi tatap muka, pengajar, waktu, lokasi, kapasitas, keterisian peserta, dan status kelas."
>
    <x-slot:actions>
        <a class="admin-btn secondary" href="{{ route('admin.schedules.export', request()->query()) }}">Ekspor CSV</a>
        <a class="admin-btn primary" href="{{ route('admin.schedules.create') }}">Tambah Jadwal</a>
    </x-slot:actions>
</x-admin.feature-header>

<div class="admin-metric-grid">
    <x-admin.metric-card label="Kelas Hari Ini" :value="number_format($stats['today'])" hint="Tidak termasuk sesi dibatalkan" tone="blue" />
    <x-admin.metric-card label="Sedang Berlangsung" :value="number_format($stats['live_now'])" hint="Status sesi berlangsung" tone="red" />
    <x-admin.metric-card label="Akan Datang" :value="number_format($stats['upcoming'])" hint="Terjadwal atau berlangsung" tone="yellow" />
    <x-admin.metric-card
        label="Rata-rata Keterisian"
        :value="number_format($stats['avg_occupancy'], 1).'%'" 
        hint="Booking dibanding kapasitas sesi mendatang"
        tone="green"
    />
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Agenda"
        title="Daftar jadwal pengajaran"
        description="Sistem mencegah jadwal pengajar yang bertabrakan pada waktu yang sama."
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($schedules->total()) }} sesi</span>
        </x-slot:actions>
    </x-admin.section-header>

    <form class="admin-filter-panel" method="GET" action="{{ route('admin.schedules.index') }}">
        <div class="admin-filter-grid schedule-filter-grid">
            <label class="admin-filter-field">
                <span>Cari jadwal</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Judul sesi, kelas, atau pengajar">
            </label>

            <label class="admin-filter-field">
                <span>Kelas</span>
                <select name="course_id">
                    <option value="">Semua kelas</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Pengajar</span>
                <select name="instructor_id">
                    <option value="">Semua pengajar</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected((string) request('instructor_id') === (string) $instructor->id)>
                            {{ $instructor->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Status</span>
                <select name="status">
                    <option value="">Semua status</option>
                    <option value="scheduled" @selected(request('status') === 'scheduled')>Terjadwal</option>
                    <option value="live" @selected(request('status') === 'live')>Berlangsung</option>
                    <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Periode</span>
                <select name="period">
                    <option value="">Semua waktu</option>
                    <option value="today" @selected(request('period') === 'today')>Hari ini</option>
                    <option value="upcoming" @selected(request('period') === 'upcoming')>Mendatang</option>
                    <option value="past" @selected(request('period') === 'past')>Sudah lewat</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Dari tanggal</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </label>

            <label class="admin-filter-field">
                <span>Sampai tanggal</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </label>
        </div>

        <div class="admin-filter-actions">
            <button class="admin-btn primary" type="submit">Terapkan Filter</button>
            <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">Reset</a>
        </div>
    </form>

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table schedule-table">
            <thead>
            <tr>
                <th>Waktu</th>
                <th>Sesi & Kelas</th>
                <th>Pengajar</th>
                <th>Keterisian</th>
                <th>Status</th>
                <th>Lokasi</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($schedules as $session)
                @php($occupancy = $session->capacity > 0 ? min(100, ($session->booked_count / $session->capacity) * 100) : 0)
                <tr>
                    <td>
                        <strong>{{ $session->starts_at?->translatedFormat('d M Y') }}</strong>
                        <small class="admin-cell-help">
                            {{ $session->starts_at?->format('H:i') }}–{{ $session->ends_at?->format('H:i') }}
                        </small>
                    </td>
                    <td>
                        <strong>{{ $session->title }}</strong>
                        <small class="admin-cell-help">{{ $session->learningPath?->title ?? 'Kelas tidak tersedia' }}</small>
                    </td>
                    <td>
                        <div class="admin-identity-cell compact">
                            <span class="admin-avatar-sm">{{ strtoupper(substr($session->instructor?->name ?? 'P', 0, 1)) }}</span>
                            <div>
                                <strong>{{ $session->instructor?->name ?? 'Tidak tersedia' }}</strong>
                                <small>{{ $session->instructor?->instructorProfile?->headline ?? 'Pengajar SKILLPATH' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="admin-progress-cell">
                            <div>
                                <strong>{{ $session->booked_count }}/{{ $session->capacity }}</strong>
                                <small>{{ number_format($occupancy, 0) }}%</small>
                            </div>
                            <div class="admin-progress-track">
                                <span style="width: {{ $occupancy }}%"></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="admin-status {{ $session->status }}">{{ ['scheduled'=>'TERJADWAL','live'=>'BERLANGSUNG','completed'=>'SELESAI','cancelled'=>'DIBATALKAN'][$session->status] ?? strtoupper($session->status) }}</span>
                    </td>
                    <td>
                        @if($session->location)
                            <strong>{{ $session->location }}</strong>
                        @else
                            <span class="admin-muted">Lokasi belum diisi</span>
                        @endif
                        @if($session->meeting_url)
                            <small><a class="admin-inline-link" href="{{ $session->meeting_url }}" target="_blank" rel="noopener">Buka peta</a></small>
                        @endif
                    </td>
                    <td>
                        <div class="admin-row-actions">
                            <a class="admin-btn small ghost" href="{{ route('admin.schedules.edit', $session) }}">Edit</a>

                            @if(!in_array($session->status, ['completed', 'cancelled']))
                                <form method="POST" action="{{ route('admin.schedules.cancel', $session) }}" onsubmit="return confirm('Batalkan jadwal pengajaran ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="admin-btn small danger" type="submit">Batalkan</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="admin-empty-state">
                            <strong>Belum ada jadwal yang sesuai.</strong>
                            <span>Tambahkan jadwal baru atau ubah filter pencarian.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $schedules->links() }}</div>
</section>

<div class="admin-info-note">
    <strong>Validasi jadwal</strong>
    <span>Admin tidak dapat membuat dua sesi yang waktunya bertabrakan untuk pengajar yang sama.</span>
</div>
@endsection
