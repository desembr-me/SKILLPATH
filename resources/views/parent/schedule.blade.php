@extends('layouts.app')
@section('title', 'Jadwal & Pengajuan Pindah Kelas')

@section('content')
@php($days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'])
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Jadwal Kelas Anak</span>
            <h1>Jadwal & Pengajuan Pindah Jadwal</h1>
            <p>Ajukan permohonan perubahan jadwal kursus jika anak berhalangan hadir pada sesi kelas yang sedang berjalan.</p>
        </div>
    </div>

    <div class="info-banner">
        <span><x-icon name="calendar" /></span>
        <div>
            <b>Bagaimana alur perubahan jadwal?</b>
            <p>Pilih slot jadwal alternatif dan berikan alasan perubahan. Permohonan Anda akan dikirim langsung ke pengajar untuk dikonfirmasi agar kuota dan sesi belajar anak tetap terpantau dengan baik.</p>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Kursus Berjalan</span>
                <h2>Jadwal Kursus Aktif</h2>
            </div>
        </div>

        @forelse($enrollments as $enrollment)
            @php($pendingReq = $enrollment->rescheduleRequests->where('status', 'pending')->first())
            <div class="credit-row" style="align-items: flex-start; padding: 18px 0; border-bottom: 1px solid #eff0f4;">
                <div class="credit-code-mark"><x-icon name="calendar" /></div>
                <div style="flex:1;">
                    <h3>{{ $enrollment->course->title }}</h3>
                    <p style="margin:4px 0;">
                        <b>{{ $enrollment->child->name }}</b> • Jadwal saat ini: 
                        <b>{{ $days[$enrollment->schedule->day_of_week] }}</b> ({{ substr($enrollment->schedule->start_time,0,5) }}-{{ substr($enrollment->schedule->end_time,0,5) }} WIB)
                        @if($enrollment->schedule->room) • Ruang: {{ $enrollment->schedule->room }} @endif
                    </p>
                    <small style="color:var(--muted);">Pengajar: {{ optional($enrollment->course->instructor)->name ?? '-' }}</small>

                    @if($pendingReq)
                        <div style="margin-top:10px; background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:10px; font-size:11px;">
                            <b style="color:#c2410c;">Permohonan Pindah Jadwal Sedang Diproses:</b>
                            <p style="margin:2px 0; color:var(--ink);">
                                Menunggu konfirmasi pengajar untuk pindah ke <b>{{ $days[optional($pendingReq->requestedSchedule)->day_of_week ?? 0] }} ({{ substr(optional($pendingReq->requestedSchedule)->start_time,0,5) }}-{{ substr(optional($pendingReq->requestedSchedule)->end_time,0,5) }} WIB)</b>.
                            </p>
                            <small style="color:var(--muted);">Alasan: "{{ $pendingReq->reason }}"</small>
                        </div>
                    @elseif($alternatives[$enrollment->id]->isNotEmpty())
                        <details style="margin-top:10px;">
                            <summary class="btn btn-sm btn-soft" style="display:inline-flex; width:auto; cursor:pointer;">
                                <x-icon name="calendar" /> Ajukan Pindah Jadwal
                            </summary>
                            <form method="POST" action="{{ route('parent.schedule.update', $enrollment) }}" style="margin-top:10px; background:#faf9ff; border:1px solid #ece8fb; border-radius:12px; padding:12px; display:grid; gap:8px;">
                                @csrf
                                @method('PUT')
                                <label style="font-size:11px; font-weight:700;">Pilih Jadwal Alternatif
                                    <select name="schedule_id" required style="font-size:11px; padding:7px 8px; width:100%; margin-top:4px;">
                                        <option value="">-- Pilih slot waktu alternatif --</option>
                                        @foreach($alternatives[$enrollment->id] as $schedule)
                                            <option value="{{ $schedule->id }}">
                                                {{ $days[$schedule->day_of_week] }} • {{ substr($schedule->start_time,0,5) }}-{{ substr($schedule->end_time,0,5) }} WIB @if($schedule->room) (Ruang {{ $schedule->room }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                                <label style="font-size:11px; font-weight:700;">Alasan Perubahan
                                    <input name="reason" placeholder="Contoh: Anak ada ujian sekolah / kegiatan keluarga" required style="font-size:11px; padding:7px 8px; width:100%; margin-top:4px;">
                                </label>
                                <div style="display:flex; justify-content:flex-end; margin-top:4px;">
                                    <button class="btn btn-primary btn-sm"><x-icon name="check" /> Kirim Pengajuan ke Pengajar</button>
                                </div>
                            </form>
                        </details>
                    @else
                        <small style="display:block; color:var(--muted); margin-top:6px;">Belum ada jadwal alternatif lain untuk kursus ini.</small>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <x-icon name="calendar" />
                <div>
                    <b>Belum ada course aktif</b>
                    <span>Jadwal kelas akan ditampilkan setelah pendaftaran kursus anak aktif.</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Status Riwayat Pengajuan --}}
    @if(isset($recentRequests) && $recentRequests->isNotEmpty())
        <div class="panel" style="margin-top:20px;">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Riwayat Pengajuan</span>
                    <h2>Status Permintaan Perubahan Jadwal</h2>
                </div>
            </div>

            @foreach($recentRequests as $req)
                <div class="child-row" style="padding:12px 0;">
                    <div class="child-avatar" style="background:#f0eefc; color:var(--purple);">
                        <x-icon name="calendar" />
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <h3>{{ optional(optional($req->enrollment)->course)->title }} ({{ optional(optional($req->enrollment)->child)->name }})</h3>
                            <span class="status-chip {{ $req->status === 'approved' ? 'paid' : ($req->status === 'rejected' ? 'failed' : 'pending') }}">
                                {{ $req->status === 'approved' ? 'Disetujui' : ($req->status === 'rejected' ? 'Ditolak' : 'Menunggu Konfirmasi') }}
                            </span>
                        </div>
                        <p style="margin:2px 0; font-size:11px;">
                            Pindah ke: <b>{{ $days[optional($req->requestedSchedule)->day_of_week ?? 0] }} ({{ substr(optional($req->requestedSchedule)->start_time,0,5) }}-{{ substr(optional($req->requestedSchedule)->end_time,0,5) }} WIB)</b>
                        </p>
                        @if($req->mentor_note)
                            <small style="color:var(--purple); display:block;">Catatan Pengajar: {{ $req->mentor_note }}</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
