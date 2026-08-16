@extends('layouts.app')
@section('title', 'Detail Siswa ' . $child->name)

@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Detail Siswa</span>
            <h1>{{ $child->name }}</h1>
            <p>{{ $enrollment->course->title }} • Status: {{ ucfirst($enrollment->status) }} • Progres: {{ $enrollment->progress }}%</p>
        </div>
        <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Kehadiran</span>
                    <h2>Riwayat Kehadiran</h2>
                </div>
            </div>
            @forelse($enrollment->attendance as $att)
                <div class="child-row">
                    <div class="child-avatar"><x-icon name="calendar" /></div>
                    <div style="flex:1;">
                        <h3>Sesi {{ optional($att->courseSession)->session_no }} • {{ optional(optional($att->courseSession)->session_date)->format('d M Y') }}</h3>
                        <p>{{ optional($att->courseSession)->topic }}</p>
                        @if($att->absence_reason)
                            <div class="mini-tags"><span>Alasan: {{ $att->absence_reason }}</span></div>
                        @endif
                        @if($att->mentor_note)
                            <small style="display:block; color:var(--purple); margin-top:2px;">Catatan: {{ $att->mentor_note }}</small>
                        @endif
                    </div>
                    <div>
                        <span class="status-chip {{ $att->status === 'present' ? 'active' : ($att->status === 'excused' ? 'pending' : 'failed') }}">
                            {{ $att->status === 'present' ? 'Hadir' : ($att->status === 'excused' ? 'Izin' : 'Tidak Hadir') }}
                        </span>
                        @if($att->credit_eligible)
                            <span class="status-chip" style="background:#eaf8ef; color:#236b45; display:block; margin-top:4px; font-size:8px;">+ Kredit Sesi</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state compact-empty">
                    <x-icon name="calendar" />
                    <div>
                        <b>Belum ada catatan kehadiran</b>
                        <span>Gunakan formulir di bawah untuk mencatat kehadiran siswa pada sesi kelas.</span>
                    </div>
                </div>
            @endforelse

            {{-- Form catat kehadiran langsung --}}
            @if(optional($enrollment->schedule)->sessions->isNotEmpty())
                <div style="margin-top:16px; padding-top:14px; border-top:1px solid #eff0f4;">
                    <span class="panel-kicker" style="margin-bottom:8px; display:block;">Presensi Langsung</span>
                    <form method="POST" action="{{ route('mentor.attendance.store') }}" class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                        @csrf
                        <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                        <label>Pilih Sesi
                            <select name="course_session_id" required>
                                @foreach($enrollment->schedule->sessions as $ses)
                                    <option value="{{ $ses->id }}">Sesi {{ $ses->session_no }} ({{ optional($ses->session_date)->format('d/m') }})</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Status
                            <select name="status" required>
                                <option value="present">Hadir</option>
                                <option value="excused">Izin</option>
                                <option value="absent">Tidak Hadir</option>
                                <option value="rescheduled">Jadwal Ulang</option>
                            </select>
                        </label>
                        <label>Alasan (Opsional)
                            <input name="absence_reason" placeholder="Alasan izin / sakit">
                        </label>
                        <div style="grid-column: 1 / -1; display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                            <label class="compact-check" style="font-size:11px; cursor:pointer;">
                                <input type="checkbox" name="credit_eligible" value="1"> Buat kredit sesi pengganti
                            </label>
                            <button class="btn btn-primary btn-sm"><x-icon name="check" /> Simpan Presensi</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="panel student-profile-panel">
            <div class="panel-heading">
                <div><span class="panel-kicker">Profil anak</span><h2>Tentang {{ $child->name }}</h2></div>
            </div>
            <div class="family-summary">
                <div class="summary-avatar">
                    @if($child->avatar_url)
                        <img src="{{ $child->avatar_url }}" alt="Foto {{ $child->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:18px;">
                    @elseif($child->avatar && !str_starts_with($child->avatar,'avatars/'))
                        <span style="font-size:24px;">{{ $child->avatar }}</span>
                    @else
                        {{ $child->initial }}
                    @endif
                </div>
                <div>
                    <h2>{{ $child->name }}</h2>
                    <p>{{ $child->age }} tahun @if($child->nickname) • {{ $child->nickname }} @endif</p>
                </div>
            </div>
            <div class="mini-tags">
                @forelse($child->interests ?? [] as $interest)
                    <span>{{ $interest }}</span>
                @empty
                    <span>Minat belum diisi</span>
                @endforelse
            </div>
            @if($child->notes)
                <p class="mentor-note">"{{ $child->notes }}"</p>
            @endif
        </div>
    </div>

    <div class="panel mentor-tools" style="margin-top: 18px;">
        <div class="panel-heading">
            <div><span class="panel-kicker">Evaluasi</span><h2>Riwayat Nilai Ujian</h2></div>
        </div>
        @forelse($enrollment->examAttempts as $attempt)
            <div class="exam-row">
                <div class="exam-main">
                    <h3>{{ optional($attempt->exam)->title }} • Percobaan {{ $attempt->attempt_no }}</h3>
                    <p>Nilai {{ $attempt->score }} • Passing {{ optional($attempt->exam)->passing_score }}</p>
                    @if($attempt->mentor_feedback)<small>Feedback: {{ $attempt->mentor_feedback }}</small>@endif
                </div>
                <div class="exam-status">
                    <span class="status-chip {{ $attempt->status }}">{{ ucfirst($attempt->status) }}</span>
                </div>
            </div>
        @empty
            <div class="empty-state compact-empty">
                <x-icon name="certificate" />
                <div><b>Belum ada percobaan ujian</b><span>Nilai akan muncul setelah siswa mengikuti ujian.</span></div>
            </div>
        @endforelse
        @if($enrollment->certificate)
            <div class="learning-path-intro certificate-banner student-certificate-banner" style="margin-top:14px;">
                <span><x-icon name="certificate" /></span>
                <div>
                    <b>Sertifikat Lulus Terbit</b>
                    <p>Nomor {{ $enrollment->certificate->certificate_no }} • {{ $enrollment->certificate->issued_at->format('d M Y') }}</p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
