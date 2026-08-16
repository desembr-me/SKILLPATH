@extends('layouts.admin')
@section('title', 'Progress Siswa')

@section('content')
<section class="admin-students-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">MONITORING BELAJAR</span>
            <h1>Progress & Capaian Siswa</h1>
            <p>Pantau ketercapaian modul, aktivitas belajar, nilai kuis/ujian, dan penerbitan sertifikat seluruh siswa.</p>
        </div>
        <div class="admin-action-group">
            <div class="admin-mini-kpi" style="padding:10px 18px; border-radius:12px; background:#fff;">
                <span style="font-size:10px; font-weight:900; color:#8a84ab;">SISWA AKTIF BELAJAR</span>
                <b style="font-size:18px; color:#5b36f5;">{{ $totalActiveStudents }} Siswa</b>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <form method="GET" action="{{ route('admin.students.index') }}" class="admin-search-input" style="width:100%; max-width:400px;">
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama anak atau judul kelas...">
                @if($search)
                    <a href="{{ route('admin.students.index') }}" style="color:#8a84ab; font-size:11px; font-weight:800;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Orang Tua</th>
                        <th>Course & Pengajar</th>
                        <th>Progress Aktivitas</th>
                        <th>Nilai Ujian</th>
                        <th>Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="admin-avatar" style="width:36px; height:36px; background:#f1efff; color:#5b36f5;">
                                        @if($st['child']->avatar_url)
                                            <img src="{{ $st['child']->avatar_url }}" alt="{{ $st['child']->name }}">
                                        @else
                                            {{ $st['child']->initial }}
                                        @endif
                                    </div>
                                    <div>
                                        <b style="color:#120e2e; display:block;">{{ $st['child']->name }}</b>
                                        <small style="color:#8a84ab;">Usia {{ $st['child']->age }} thn</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#5c567e;">{{ $st['child']->parent->name ?? '-' }}</span>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ $st['course']->title }}</b>
                                <small style="color:#8a84ab;">Mentor: {{ $st['course']->instructor->name ?? 'SkillPath' }}</small>
                            </td>
                            <td style="min-width: 160px;">
                                <div style="display:flex; justify-content:space-between; font-size:11.5px; font-weight:800; margin-bottom:4px;">
                                    <span>{{ $st['completedActivities'] }} / {{ $st['totalActivities'] }} Modul</span>
                                    <span style="color:{{ $st['progressPercent'] >= 100 ? '#166534' : ($st['progressPercent'] < 50 ? '#b45309' : '#5b36f5') }};">
                                        {{ $st['progressPercent'] }}%
                                    </span>
                                </div>
                                <div class="meter-track" style="height:6px;">
                                    <div class="meter-fill" style="width: {{ $st['progressPercent'] }}%; background: {{ $st['progressPercent'] >= 100 ? '#22c55e' : ($st['progressPercent'] < 50 ? '#f59e0b' : '#5b36f5') }};"></div>
                                </div>
                            </td>
                            <td>
                                @if($st['examScore'] !== null)
                                    <span class="status-pill {{ $st['examScore'] >= 75 ? 'paid' : 'pending' }}">
                                        Skor: {{ $st['examScore'] }} / 100
                                    </span>
                                @else
                                    <span style="font-size:11px; color:#8a84ab;">Belum Ujian</span>
                                @endif
                            </td>
                            <td>
                                @if($st['certificate'])
                                    <span class="status-pill paid" style="font-size:10px;">
                                        <x-icon name="certificate" style="width:12px; height:12px; margin-right:4px;" /> Terbit
                                    </span>
                                @else
                                    <span style="font-size:11px; color:#8a84ab;">Belum Terbit</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada data progres siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginator->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
