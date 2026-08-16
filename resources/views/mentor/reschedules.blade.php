@extends('layouts.app')
@section('title', 'Permintaan Perubahan Jadwal')

@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Notifikasi & Permintaan</span>
            <h1>Permintaan Perubahan Jadwal</h1>
            <p>Kelola dan tanggapi permohonan pemindahan jadwal kelas yang diajukan oleh orang tua siswa.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a>
        </div>
    </div>

    {{-- Pending Requests --}}
    <div class="panel">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Menunggu Konfirmasi</span>
                <h2>Pengajuan Baru</h2>
            </div>
            <span class="helper-badge">{{ $pendingRequests->count() }} Permintaan Menunggu</span>
        </div>

        @forelse($pendingRequests as $req)
            <div class="child-row" style="align-items: flex-start; padding: 18px 0; border-bottom: 1px solid #eff0f4;">
                <div class="child-avatar" style="background:#f1efff; color:var(--purple);">
                    {{ strtoupper(substr(optional(optional($req->enrollment)->child)->name ?? 'A', 0, 1)) }}
                </div>
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                        <div>
                            <h3 style="font-size:16px;">{{ optional(optional($req->enrollment)->child)->name }}</h3>
                            <p style="color:var(--ink-2); font-weight:700; margin:2px 0;">
                                {{ optional(optional($req->enrollment)->course)->title }} • Orang Tua: {{ optional($req->parent)->name }}
                            </p>
                        </div>
                        <span class="status-chip pending">Menunggu Konfirmasi</span>
                    </div>

                    <div style="margin: 10px 0; background:#f7f8fc; border-radius:12px; padding:12px; display:grid; gap:8px; font-size:11px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="color:var(--muted); width:130px; font-weight:800;">Jadwal Saat Ini:</span>
                            <b style="color:var(--ink);">
                                {{ $days[optional($req->currentSchedule)->day_of_week ?? 0] }},
                                {{ substr(optional($req->currentSchedule)->start_time, 0, 5) }} - {{ substr(optional($req->currentSchedule)->end_time, 0, 5) }} WIB
                                @if(optional($req->currentSchedule)->room) ({{ optional($req->currentSchedule)->room }}) @endif
                            </b>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="color:var(--purple); width:130px; font-weight:800;">Jadwal Baru Diminta:</span>
                            <b style="color:var(--purple);">
                                {{ $days[optional($req->requestedSchedule)->day_of_week ?? 0] }},
                                {{ substr(optional($req->requestedSchedule)->start_time, 0, 5) }} - {{ substr(optional($req->requestedSchedule)->end_time, 0, 5) }} WIB
                                @if(optional($req->requestedSchedule)->room) ({{ optional($req->requestedSchedule)->room }}) @endif
                            </b>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:10px; border-top:1px dashed #e2e4ed; padding-top:6px;">
                            <span style="color:var(--muted); width:130px; font-weight:800;">Alasan Pengajuan:</span>
                            <span style="color:var(--ink-2); font-style:italic;">"{{ $req->reason ?: 'Tidak ada keterangan khusus.' }}"</span>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:8px;">
                        <form method="POST" action="{{ route('mentor.reschedules.approve', $req) }}" onsubmit="return confirm('Apakah Anda yakin menyetujui pemindahan jadwal ini?');">
                            @csrf
                            <input type="hidden" name="mentor_note" value="Disetujui oleh mentor.">
                            <button class="btn btn-primary btn-sm"><x-icon name="check" /> Setujui Pemindahan</button>
                        </form>

                        <details>
                            <summary class="btn btn-ghost btn-sm" style="cursor:pointer; color:var(--danger); border-color:#fadbd8;">
                                Tolak Permintaan
                            </summary>
                            <form method="POST" action="{{ route('mentor.reschedules.reject', $req) }}" style="margin-top:10px; display:flex; gap:8px;">
                                @csrf
                                <input name="mentor_note" placeholder="Tuliskan alasan penolakan..." required style="padding:7px 10px; font-size:11px; border:1px solid #ddd; border-radius:8px; min-width:260px;">
                                <button class="btn btn-sm btn-ghost" style="color:var(--danger); background:#ffebee;">Kirim Penolakan</button>
                            </form>
                        </details>

                        <small style="color:var(--muted); margin-left:auto;">
                            Diajukan: {{ $req->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <x-icon name="bell" />
                <div>
                    <b>Tidak ada permintaan perubahan jadwal baru</b>
                    <span>Semua permohonan dari orang tua siswa telah diproses.</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- History --}}
    <div class="panel admin-table-panel" style="margin-top: 20px;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Arsip</span>
                <h2>Riwayat Permintaan Jadwal</h2>
            </div>
            <span class="helper-badge">{{ $resolvedRequests->count() }} Riwayat Tercatat</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa & Orang Tua</th>
                        <th>Course</th>
                        <th>Jadwal Asal &rarr; Tujuan</th>
                        <th>Catatan / Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resolvedRequests as $req)
                        <tr>
                            <td>
                                <b>{{ $req->updated_at->format('d M Y') }}</b>
                                <small style="display:block; color:var(--muted);">{{ $req->updated_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <b>{{ optional(optional($req->enrollment)->child)->name ?? '-' }}</b>
                                <small style="display:block; color:var(--muted);">Orang tua: {{ optional($req->parent)->name ?? '-' }}</small>
                            </td>
                            <td>
                                <b>{{ optional(optional($req->enrollment)->course)->title ?? '-' }}</b>
                            </td>
                            <td>
                                <div style="font-size:10px;">
                                    <span style="color:var(--muted);">Dari: {{ $days[optional($req->currentSchedule)->day_of_week ?? 0] }} ({{ substr(optional($req->currentSchedule)->start_time, 0, 5) }}-{{ substr(optional($req->currentSchedule)->end_time, 0, 5) }})</span>
                                    <br>
                                    <span style="color:var(--purple); font-weight:700;">Ke: {{ $days[optional($req->requestedSchedule)->day_of_week ?? 0] }} ({{ substr(optional($req->requestedSchedule)->start_time, 0, 5) }}-{{ substr(optional($req->requestedSchedule)->end_time, 0, 5) }})</span>
                                </div>
                            </td>
                            <td>
                                <small style="display:block; color:var(--ink-2);"><b>Alasan:</b> {{ $req->reason }}</small>
                                @if($req->mentor_note)
                                    <small style="display:block; color:var(--purple); margin-top:2px;"><b>Catatan:</b> {{ $req->mentor_note }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="status-chip {{ $req->status === 'approved' ? 'paid' : 'failed' }}">
                                    {{ $req->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:24px;">
                                <div class="empty-state" style="justify-content:center;">
                                    <x-icon name="bell" />
                                    <div>
                                        <b>Belum ada riwayat permintaan</b>
                                        <span>Daftar permintaan yang disetujui atau ditolak akan muncul di sini.</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
