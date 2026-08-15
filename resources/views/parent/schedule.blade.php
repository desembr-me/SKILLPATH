@extends('layouts.app')
@section('title','Jadwal')
@section('content')
@php($days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'])
<section class="dashboard-page">
    <div class="dash-title"><div><span class="eyebrow">Jadwal Kelas</span><h1>Jadwal</h1><p>Ubah jadwal course jika anak berhalangan mengikuti kelas pada jadwal saat ini.</p></div><a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Dashboard</a></div>
    <div class="info-banner"><span><x-icon name="calendar" /></span><div><b>Bagaimana cara kerjanya?</b><p>Pilih jadwal baru untuk course yang sedang aktif. Perubahan langsung berlaku untuk sesi berikutnya, selama tidak bentrok dengan course aktif anak yang lain.</p></div></div>
    <div class="panel">
        @forelse($enrollments as $enrollment)
            <div class="credit-row">
                <div class="credit-code-mark"><x-icon name="calendar" /></div>
                <div>
                    <h3>{{ $enrollment->course->title }}</h3>
                    <p>{{ $enrollment->child->name }} • Jadwal saat ini: {{ $days[$enrollment->schedule->day_of_week] }} • {{ substr($enrollment->schedule->start_time,0,5) }}-{{ substr($enrollment->schedule->end_time,0,5) }}@if($enrollment->schedule->room) • {{ $enrollment->schedule->room }}@endif</p>
                </div>
                @if($alternatives[$enrollment->id]->isNotEmpty())
                    <form method="POST" action="{{ route('parent.schedule.update',$enrollment) }}" class="inline-form">
                        @csrf
                        @method('PUT')
                        <select name="schedule_id" required>
                            <option value="">Anak berhalangan? Pilih jadwal baru</option>
                            @foreach($alternatives[$enrollment->id] as $schedule)
                                <option value="{{ $schedule->id }}">{{ $days[$schedule->day_of_week] }} • {{ substr($schedule->start_time,0,5) }}-{{ substr($schedule->end_time,0,5) }}@if($schedule->room) • {{ $schedule->room }}@endif</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary">Ubah Jadwal</button>
                    </form>
                @else
                    <small>Belum ada jadwal alternatif untuk course ini.</small>
                @endif
            </div>
        @empty<div class="empty-state"><x-icon name="calendar" /><div><b>Belum ada course aktif</b><span>Jadwal dapat diubah setelah course anak berjalan aktif.</span></div></div>@endforelse
    </div>
</section>
@endsection
