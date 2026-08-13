@extends('admin.layouts.app')

@section('title', 'Progres '.$childProfile->name.' | Admin SKILLPATH')
@section('page-title', 'Detail Progres Siswa')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.progress.index') }}">← Monitoring Progres</a>
    <span class="admin-status {{ $summary['status'] }}">{{ $summary['status_label'] }}</span>
</div>

<section class="admin-profile-summary">
    <div class="admin-profile-avatar-lg">{{ strtoupper(substr($childProfile->name, 0, 1)) }}</div>

    <div class="admin-profile-copy">
        <span class="admin-eyebrow">Profil siswa</span>
        <h2>{{ $childProfile->name }}</h2>
        <p>
            Usia {{ $childProfile->age }} tahun ·
            Orang tua {{ $childProfile->user?->name ?? 'tidak tersedia' }}
            @if($childProfile->user?->email)
                · {{ $childProfile->user->email }}
            @endif
        </p>

        <div class="admin-chip-list">
            @forelse($childProfile->interests as $interest)
                <span>{{ $interest->icon }} {{ $interest->name }}</span>
            @empty
                <span>Belum memilih minat</span>
            @endforelse
        </div>
    </div>

    <div class="admin-profile-side">
        <span>Aktivitas terakhir</span>
        <strong>{{ $summary['last_activity_at']?->translatedFormat('d M Y, H:i') ?? 'Belum ada' }}</strong>
        @if($summary['attention_reason'])
            <small>{{ $summary['attention_reason'] }}</small>
        @endif
    </div>
</section>

<div class="admin-metric-grid">
    <x-admin.metric-card
        label="Progres Keseluruhan"
        :value="$summary['progress_percent'].'%'"
        :hint="$summary['completed_activities'].'/'.$summary['total_activities'].' aktivitas selesai'"
        tone="blue"
    />
    <x-admin.metric-card
        label="Course Aktif"
        :value="$summary['enrollment_count']"
        :hint="$summary['remaining_activities'].' aktivitas tersisa'"
        tone="yellow"
    />
    <x-admin.metric-card
        label="Total Poin"
        :value="number_format($summary['points'])"
        hint="Akumulasi aktivitas selesai"
        tone="green"
    />
    <x-admin.metric-card
        label="Rata-rata Nilai"
        :value="$summary['average_score'] !== null ? number_format($summary['average_score'], 1) : '—'"
        hint="Dari aktivitas yang memiliki nilai"
        tone="pink"
    />
</div>

<div class="admin-split-grid progress-detail-grid">
    <section class="admin-section-card">
        <x-admin.section-header
            eyebrow="Course"
            title="Progres per course"
            description="Rincian aktivitas, poin, nilai, dan status belajar pada setiap course."
        />

        <div class="admin-stack-list">
            @forelse($courseProgress as $item)
                <article class="admin-list-card">
                    <div class="admin-list-icon">{{ $item['path']->icon }}</div>

                    <div class="admin-list-content">
                        <div class="admin-list-heading">
                            <div>
                                <strong>{{ $item['path']->title }}</strong>
                                <small>{{ $item['path']->instructor?->name ?? 'Belum ada pengajar' }}</small>
                            </div>
                            <span class="admin-status {{ $item['status'] }}">{{ $item['status_label'] }}</span>
                        </div>

                        <div class="admin-progress-summary">
                            <strong>{{ $item['progress_percent'] }}%</strong>
                            <div class="admin-progress-track large">
                                <span style="width: {{ $item['progress_percent'] }}%"></span>
                            </div>
                        </div>

                        <div class="admin-meta-row">
                            <span>{{ $item['completed_activities'] }}/{{ $item['total_activities'] }} aktivitas</span>
                            <span>{{ number_format($item['points']) }} poin</span>
                            <span>Nilai {{ $item['average_score'] !== null ? number_format($item['average_score'], 1) : '—' }}</span>
                            <span>Terakhir {{ $item['last_activity_at']?->translatedFormat('d M Y') ?? 'belum ada' }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="admin-empty-state">
                    <strong>Belum ada course.</strong>
                    <span>Siswa belum mempunyai enrollment yang dapat dipantau.</span>
                </div>
            @endforelse
        </div>
    </section>

    <aside class="admin-section-card">
        <x-admin.section-header
            eyebrow="Ringkasan"
            title="Aktivitas siswa"
            description="Informasi singkat untuk tindak lanjut admin."
        />

        <div class="admin-detail-list">
            <div>
                <span>Mulai enrollment</span>
                <strong>{{ $summary['first_enrollment_at']?->translatedFormat('d M Y') ?? 'Belum ada' }}</strong>
            </div>
            <div>
                <span>Hari tidak aktif</span>
                <strong>{{ $summary['days_inactive'] !== null ? $summary['days_inactive'].' hari' : '—' }}</strong>
            </div>
            <div>
                <span>Aktivitas selesai</span>
                <strong>{{ $summary['completed_activities'] }}</strong>
            </div>
            <div>
                <span>Aktivitas tersisa</span>
                <strong>{{ $summary['remaining_activities'] }}</strong>
            </div>
        </div>

        @if($summary['attention_reason'])
            <div class="admin-alert-box danger">
                <strong>Perlu tindak lanjut</strong>
                <span>{{ $summary['attention_reason'] }}</span>
            </div>
        @else
            <div class="admin-alert-box success">
                <strong>Monitoring normal</strong>
                <span>Tidak ada indikator keterlambatan yang perlu ditindaklanjuti saat ini.</span>
            </div>
        @endif
    </aside>
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Riwayat Belajar"
        title="Aktivitas terbaru"
        description="20 aktivitas terakhir yang ditandai selesai oleh siswa."
    />

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Course</th>
                <th>Modul</th>
                <th>Aktivitas</th>
                <th>Nilai</th>
                <th>Poin</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentActivities as $progress)
                <tr>
                    <td>{{ $progress->completed_at?->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                    <td>{{ $progress->activity->module->learningPath?->title ?? 'Course tidak tersedia' }}</td>
                    <td>{{ $progress->activity->module->title }}</td>
                    <td><strong>{{ $progress->activity->title }}</strong></td>
                    <td>{{ $progress->score ?? '—' }}</td>
                    <td><strong>+{{ number_format($progress->points_awarded) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="admin-empty-state">
                            <strong>Belum ada riwayat aktivitas.</strong>
                            <span>Aktivitas yang selesai akan muncul di sini.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
