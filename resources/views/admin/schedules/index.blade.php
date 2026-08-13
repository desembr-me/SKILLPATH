@extends('admin.layouts.app')
@section('title','Jadwal Kelas Offline | Admin SKILLPATH')
@section('page-title','Jadwal Kelas Offline')
@section('content')
<x-admin.feature-header eyebrow="Operasional" title="Jadwal kelas tatap muka" description="Kelola sesi, lokasi, kapasitas, dan keterisian kelas nonakademik." :primary-href="route('admin.schedules.create')" primary-label="Tambah Jadwal" :secondary-href="route('admin.schedules.export',request()->query())" secondary-label="Export CSV" />
<div class="admin-stat-grid">
    <x-admin.metric-card label="Sesi Hari Ini" :value="$stats['today']" />
    <x-admin.metric-card label="Akan Datang" :value="$stats['upcoming']" />
    <x-admin.metric-card label="Selesai Bulan Ini" :value="$stats['completed_month']" />
    <x-admin.metric-card label="Rata-rata Keterisian" :value="$stats['avg_occupancy'].'%'" />
</div>
<section class="admin-section-card">
<form method="GET" class="admin-filter-panel">
<div class="admin-filter-grid">
<label class="admin-filter-field"><span>Cari</span><input name="q" value="{{ request('q') }}" placeholder="Sesi, kelas, lokasi, pengajar"></label>
<label class="admin-filter-field"><span>Kelas</span><select name="course_id"><option value="">Semua kelas</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)request('course_id')===(string)$course->id)>{{ $course->title }}</option>@endforeach</select></label>
<label class="admin-filter-field"><span>Pengajar</span><select name="instructor_id"><option value="">Semua pengajar</option>@foreach($instructors as $instructor)<option value="{{ $instructor->id }}" @selected((string)request('instructor_id')===(string)$instructor->id)>{{ $instructor->name }}</option>@endforeach</select></label>
<label class="admin-filter-field"><span>Status</span><select name="status"><option value="">Semua</option><option value="scheduled" @selected(request('status')==='scheduled')>Terjadwal</option><option value="completed" @selected(request('status')==='completed')>Selesai</option><option value="cancelled" @selected(request('status')==='cancelled')>Dibatalkan</option></select></label>
<label class="admin-filter-field"><span>Periode</span><select name="period"><option value="">Semua waktu</option><option value="today" @selected(request('period')==='today')>Hari ini</option><option value="upcoming" @selected(request('period')==='upcoming')>Mendatang</option><option value="past" @selected(request('period')==='past')>Sudah lewat</option></select></label>
<label class="admin-filter-field"><span>Dari</span><input type="date" name="date_from" value="{{ request('date_from') }}"></label>
<label class="admin-filter-field"><span>Sampai</span><input type="date" name="date_to" value="{{ request('date_to') }}"></label>
</div><div class="admin-filter-actions"><button class="admin-btn primary" type="submit">Terapkan</button><a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">Reset</a></div>
</form>
<div class="admin-table-shell"><table class="admin-table admin-data-table"><thead><tr><th>Waktu</th><th>Sesi & Kelas</th><th>Lokasi</th><th>Pengajar</th><th>Keterisian</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($schedules as $session)
@php($occupancy=$session->capacity>0?min(100,($session->booked_count/$session->capacity)*100):0)
<tr><td><strong>{{ $session->starts_at?->translatedFormat('d M Y') }}</strong><small class="admin-cell-help">{{ $session->starts_at?->format('H:i') }}–{{ $session->ends_at?->format('H:i') }}</small></td>
<td><strong>{{ $session->title }}</strong><small class="admin-cell-help">{{ $session->learningPath?->title ?? 'Kelas tidak tersedia' }}</small></td>
<td><strong>{{ $session->venue_name }}</strong><small class="admin-cell-help">{{ $session->room ?: $session->address }}</small></td>
<td>{{ $session->instructor?->name ?? 'Tidak tersedia' }}</td>
<td><div class="admin-progress-cell"><div><strong>{{ $session->booked_count }}/{{ $session->capacity }}</strong><small>{{ number_format($occupancy,0) }}% · {{ $session->attended_count }} hadir</small></div><div class="admin-progress-track"><span style="width:{{ $occupancy }}%"></span></div></div></td>
<td><span class="admin-status {{ $session->status }}">{{ strtoupper($session->status) }}</span></td>
<td><div class="admin-row-actions"><a class="admin-btn small ghost" href="{{ route('admin.schedules.edit',$session) }}">Edit</a>@if(!in_array($session->status,['completed','cancelled']))<form method="POST" action="{{ route('admin.schedules.cancel',$session) }}" onsubmit="return confirm('Batalkan jadwal kelas ini?')">@csrf @method('PATCH')<button class="admin-btn small danger" type="submit">Batalkan</button></form>@endif</div></td></tr>
@empty<tr><td colspan="7"><div class="admin-empty-state"><strong>Belum ada jadwal yang sesuai.</strong><span>Tambahkan jadwal kelas tatap muka atau ubah filter.</span></div></td></tr>@endforelse
</tbody></table></div><div class="admin-pagination">{{ $schedules->links() }}</div>
</section>
@endsection
