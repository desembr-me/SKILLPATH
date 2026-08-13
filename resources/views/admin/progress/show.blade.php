@extends('admin.layouts.app')

@section('title', 'Kehadiran '.$childProfile->name.' | Admin SKILLPATH')
@section('page-title', 'Detail Kehadiran Peserta')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.progress.index') }}">← Monitoring Kehadiran</a>
    <span class="admin-status {{ $summary['status'] }}">{{ $summary['status_label'] }}</span>
</div>

<section class="admin-profile-summary">
    <div class="admin-profile-avatar-lg">{{ strtoupper(substr($childProfile->name, 0, 1)) }}</div>

    <div class="admin-profile-copy">
        <span class="admin-eyebrow">Profil peserta</span>
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
        <span>Sesi terakhir</span>
        <strong>{{ $summary['last_session_at']?->translatedFormat('d M Y, H:i') ?? 'Belum ada' }}</strong>
        @if($summary['attention_reason'])
            <small>{{ $summary['attention_reason'] }}</small>
        @endif
    </div>
</section>

<div class="admin-metric-grid">
    <x-admin.metric-card
        label="Tingkat Kehadiran"
        :value="($summary['attendance_rate'] ?? 0).'%'"
        :hint="$summary['attended_count'].'/'.$summary['session_count'].' sesi dihadiri'"
        tone="blue"
    />
    <x-admin.metric-card
        label="Kelas Aktif"
        :value="$summary['enrollment_count']"
        :hint="$summary['upcoming_booked'].' booking mendatang'"
        tone="yellow"
    />
    <x-admin.metric-card
        label="Sesi Dihadiri"
        :value="number_format($summary['attended_count'])"
        hint="Kehadiran yang sudah dikonfirmasi"
        tone="green"
    />
    <x-admin.metric-card
        label="Tidak Hadir"
        :value="number_format($summary['absent_count'])"
        hint="Sesi dengan status tidak hadir"
        tone="pink"
    />
</div>

<div class="admin-split-grid progress-detail-grid">
    <section class="admin-section-card">
        <x-admin.section-header
            eyebrow="Kelas"
            title="Kehadiran per kelas"
            description="Rincian jadwal, kehadiran, dan status peserta pada setiap kelas."
        />

        <div class="admin-stack-list">
            @forelse($courseAttendance as $item)
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
                            <strong>{{ $item['attendance_rate'] !== null ? $item['attendance_rate'].'%' : '—' }}</strong>
                            <div class="admin-progress-track large">
                                <span style="width: {{ $item['attendance_rate'] ?? 0 }}%"></span>
                            </div>
                        </div>

                        <div class="admin-meta-row">
                            <span>{{ $item['attended_count'] }}/{{ $item['session_count'] }} sesi hadir</span>
                            <span>{{ number_format($item['absent_count']) }} tidak hadir</span>
                            <span>{{ number_format($item['upcoming_count']) }} sesi mendatang</span>
                            <span>Terdaftar {{ $item['enrollment']->enrolled_at?->translatedFormat('d M Y') ?? '—' }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="admin-empty-state">
                    <strong>Belum ada kelas.</strong>
                    <span>Peserta belum mempunyai pendaftaran kelas yang dapat dipantau.</span>
                </div>
            @endforelse
        </div>
    </section>

    <aside class="admin-section-card">
        <x-admin.section-header
            eyebrow="Ringkasan"
            title="Kehadiran peserta"
            description="Informasi singkat untuk tindak lanjut kehadiran peserta."
        />

        <div class="admin-detail-list">
            <div>
                <span>Total booking</span>
                <strong>{{ number_format($summary['booking_count']) }}</strong>
            </div>
            <div>
                <span>Sesi dihadiri</span>
                <strong>{{ number_format($summary['attended_count']) }}</strong>
            </div>
            <div>
                <span>Tidak hadir</span>
                <strong>{{ number_format($summary['absent_count']) }}</strong>
            </div>
            <div>
                <span>Belum dipesan</span>
                <strong>{{ number_format($summary['unbooked_upcoming']) }}</strong>
            </div>
        </div>

        @if($summary['attention_reason'])
            <div class="admin-alert-box danger">
                <strong>Perlu tindak lanjut</strong>
                <span>{{ $summary['attention_reason'] }}</span>
            </div>
        @else
            <div class="admin-alert-box success">
                <strong>Kehadiran normal</strong>
                <span>Tidak ada indikator kehadiran yang perlu ditindaklanjuti saat ini.</span>
            </div>
        @endif
    </aside>
</div>

<section class="admin-section-card">
    <x-admin.section-header
        eyebrow="Riwayat Kehadiran"
        title="Sesi terbaru"
        description="Riwayat sesi kelas dan status kehadiran peserta."
    />

    <div class="admin-table-shell">
        <table class="admin-table admin-data-table">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kelas</th>
                <th>Jadwal</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->classSession?->starts_at?->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                    <td>{{ $booking->classSession?->learningPath?->title ?? 'Kelas tidak tersedia' }}</td>
                    <td>{{ $booking->classSession?->title ?? '—' }}</td>
                    <td><strong>{{ $booking->classSession?->venue_name ?? '—' }}</strong></td>
                    <td>{{ $booking->statusLabel() }}</td>
                    <td><strong>{{ $booking->notes ?: '—' }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="admin-empty-state">
                            <strong>Belum ada riwayat kehadiran.</strong>
                            <span>Booking dan kehadiran kelas akan muncul di sini.</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
