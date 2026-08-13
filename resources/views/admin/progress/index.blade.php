@extends('admin.layouts.app')

@section('title', 'Monitoring Progres Siswa | Admin SKILLPATH')
@section('page-title', 'Monitoring Progres Siswa')

@section('content')
<x-admin.feature-header
    eyebrow="Learning Analytics"
    title="Monitoring progres siswa"
    description="Pantau progres course, aktivitas, nilai, poin, dan siswa yang membutuhkan perhatian dalam satu tampilan."
>
    <x-slot:actions>
        <a class="admin-btn secondary" href="{{ route('admin.progress.export', request()->query()) }}">Ekspor CSV</a>
    </x-slot:actions>
</x-admin.feature-header>

<div class="admin-metric-grid">
    <x-admin.metric-card
        label="Siswa Termonitor"
        :value="number_format($stats['students'])"
        hint="Memiliki enrollment aktif"
        tone="blue"
    />
    <x-admin.metric-card
        label="Rata-rata Progres"
        :value="$stats['average_progress'].'%'"
        :hint="$stats['active'].' siswa aktif belajar'"
        tone="yellow"
    />
    <x-admin.metric-card
        label="Perlu Perhatian"
        :value="number_format($stats['needs_attention'])"
        :hint="$stats['not_started'].' belum mulai'"
        tone="red"
    />
    <x-admin.metric-card
        label="Selesai"
        :value="number_format($stats['completed'])"
        hint="Mencapai 100% aktivitas"
        tone="green"
    />
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Daftar Siswa"
        title="Progres pembelajaran"
        description="Gunakan filter untuk menemukan siswa berdasarkan course, usia, kondisi progres, atau aktivitas terakhir."
    >
        <x-slot:actions>
            <span class="admin-count-pill">{{ number_format($students->total()) }} siswa</span>
        </x-slot:actions>
    </x-admin.section-header>

    <form class="admin-filter-panel" method="GET" action="{{ route('admin.progress.index') }}">
        <div class="admin-filter-grid progress-filter-grid">
            <label class="admin-filter-field">
                <span>Cari siswa</span>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Nama siswa, orang tua, atau email"
                >
            </label>

            <label class="admin-filter-field">
                <span>Course</span>
                <select name="course">
                    <option value="">Semua course</option>
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
                    <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                    <option value="needs_attention" @selected(request('status') === 'needs_attention')>Perlu perhatian</option>
                    <option value="not_started" @selected(request('status') === 'not_started')>Belum mulai</option>
                </select>
            </label>

            <label class="admin-filter-field">
                <span>Urutkan</span>
                <select name="sort">
                    <option value="name" @selected(request('sort', 'name') === 'name')>Nama A–Z</option>
                    <option value="progress_low" @selected(request('sort') === 'progress_low')>Progres terendah</option>
                    <option value="progress_high" @selected(request('sort') === 'progress_high')>Progres tertinggi</option>
                    <option value="recent" @selected(request('sort') === 'recent')>Aktivitas terbaru</option>
                    <option value="inactive" @selected(request('sort') === 'inactive')>Paling lama tidak aktif</option>
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
                <th>Siswa</th>
                <th>Course</th>
                <th>Aktivitas</th>
                <th>Progres</th>
                <th>Poin</th>
                <th>Nilai</th>
                <th>Terakhir Aktif</th>
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
                        <small class="admin-cell-help">enrollment aktif</small>
                    </td>
                    <td>
                        <strong>{{ $row['completed_activities'] }}/{{ $row['total_activities'] }}</strong>
                        <small class="admin-cell-help">{{ $row['remaining_activities'] }} tersisa</small>
                    </td>
                    <td>
                        <div class="admin-progress-cell">
                            <div>
                                <strong>{{ $row['progress_percent'] }}%</strong>
                            </div>
                            <div class="admin-progress-track">
                                <span style="width: {{ $row['progress_percent'] }}%"></span>
                            </div>
                        </div>
                    </td>
                    <td>{{ number_format($row['points']) }}</td>
                    <td>{{ $row['average_score'] !== null ? number_format($row['average_score'], 1) : '—' }}</td>
                    <td>
                        @if($row['last_activity_at'])
                            <strong>{{ $row['last_activity_at']->translatedFormat('d M Y') }}</strong>
                            <small class="admin-cell-help">{{ $row['days_inactive'] }} hari lalu</small>
                        @else
                            <span class="admin-muted">Belum ada</span>
                            @if($row['days_inactive'] !== null)
                                <small class="admin-cell-help">{{ $row['days_inactive'] }} hari sejak enrollment</small>
                            @endif
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
                            <strong>Tidak ada siswa ditemukan.</strong>
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
    <strong>Aturan monitoring</strong>
    <span>Aktif: ada aktivitas ≤14 hari. Perlu perhatian: tidak aktif &gt;14 hari, atau belum mulai setelah &gt;7 hari sejak enrollment.</span>
</div>
@endsection
