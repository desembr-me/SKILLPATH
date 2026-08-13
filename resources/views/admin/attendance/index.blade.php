@extends('admin.layouts.app')
@section('title','Kehadiran Peserta | Admin SKILLPATH')
@section('page-title','Kehadiran Peserta')
@section('content')
<x-admin.feature-header eyebrow="Kelas Offline" title="Monitoring kehadiran peserta" description="Pantau pendaftaran, jadwal, kehadiran, ketidakhadiran, dan peserta yang belum memilih jadwal." :secondary-href="route('admin.attendance.export',request()->query())" secondary-label="Export CSV" />
<div class="admin-stat-grid">
<x-admin.metric-card label="Peserta Termonitor" :value="$stats['students']" />
<x-admin.metric-card label="Rata-rata Kehadiran" :value="$stats['average_attendance'].'%'" />
<x-admin.metric-card label="Jadwal Aktif" :value="$stats['active']" />
<x-admin.metric-card label="Perlu Perhatian" :value="$stats['needs_attention']" />
</div>
<section class="admin-section-card">
<form method="GET" class="admin-filter-panel"><div class="admin-filter-grid">
<label class="admin-filter-field"><span>Cari peserta</span><input name="q" value="{{ request('q') }}" placeholder="Nama anak / orang tua / email"></label>
<label class="admin-filter-field"><span>Kelas</span><select name="course"><option value="">Semua kelas</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)request('course')===(string)$course->id)>{{ $course->title }}</option>@endforeach</select></label>
<label class="admin-filter-field"><span>Usia</span><select name="age_group"><option value="">Semua usia</option><option value="5_7" @selected(request('age_group')==='5_7')>5–7 tahun</option><option value="8_10" @selected(request('age_group')==='8_10')>8–10 tahun</option><option value="11_14" @selected(request('age_group')==='11_14')>11–14 tahun</option></select></label>
<label class="admin-filter-field"><span>Status</span><select name="status"><option value="">Semua status</option><option value="active" @selected(request('status')==='active')>Jadwal aktif</option><option value="completed" @selected(request('status')==='completed')>Program selesai</option><option value="needs_attention" @selected(request('status')==='needs_attention')>Perlu perhatian</option><option value="not_scheduled" @selected(request('status')==='not_scheduled')>Belum ada jadwal</option></select></label>
<label class="admin-filter-field"><span>Urutkan</span><select name="sort"><option value="name">Nama</option><option value="attendance_low" @selected(request('sort')==='attendance_low')>Kehadiran terendah</option><option value="attendance_high" @selected(request('sort')==='attendance_high')>Kehadiran tertinggi</option><option value="recent" @selected(request('sort')==='recent')>Sesi terakhir</option></select></label>
</div><div class="admin-filter-actions"><button class="admin-btn primary" type="submit">Terapkan</button><a class="admin-btn ghost" href="{{ route('admin.attendance.index') }}">Reset</a></div></form>
<div class="admin-table-shell"><table class="admin-table admin-data-table"><thead><tr><th>Peserta</th><th>Kelas</th><th>Booking</th><th>Hadir</th><th>Tidak Hadir</th><th>Kehadiran</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($students as $row)<tr><td><div class="admin-identity-cell compact"><span class="admin-avatar-sm">{{ strtoupper(substr($row['child']->name,0,1)) }}</span><div><strong>{{ $row['child']->name }}</strong><small>{{ $row['child']->age }} th · {{ $row['child']->user?->name }}</small></div></div></td><td>{{ $row['enrollment_count'] }}</td><td>{{ $row['booking_count'] }}<small class="admin-cell-help">{{ $row['upcoming_booked'] }} mendatang</small></td><td>{{ $row['attended_count'] }}</td><td>{{ $row['absent_count'] }}</td><td><strong>{{ $row['attendance_rate']!==null?$row['attendance_rate'].'%':'—' }}</strong></td><td><span class="admin-status {{ $row['status'] }}">{{ $row['status_label'] }}</span>@if($row['attention_reason'])<small class="admin-cell-help">{{ $row['attention_reason'] }}</small>@endif</td><td><a class="admin-btn small ghost" href="{{ route('admin.attendance.show',$row['child']) }}">Detail</a></td></tr>
@empty<tr><td colspan="8"><div class="admin-empty-state"><strong>Belum ada data peserta.</strong><span>Data muncul setelah anak terdaftar pada kelas.</span></div></td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $students->links() }}</div>
</section>
@endsection
