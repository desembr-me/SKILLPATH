@extends('layouts.app')
@section('title','Course Saya')
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Course Saya</span>
            <h1>Course yang Diikuti</h1>
            <p>Pantau progres belajar dan lanjutkan aktivitas anak.</p>
        </div>
    </div>
    <div class="panel">
        @forelse($enrollments as $enrollment)
            <div class="my-course-row">
                <div class="row-icon-vector" style="--row-accent:{{ $enrollment->course->accent }}"><x-icon :name="$enrollment->course->category->slug" /></div>
                <div class="my-course-main">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 2px;">
                        <h3 style="margin: 0;">{{ $enrollment->course->title }}</h3>
                        <span class="status-chip soft" style="font-size: 9.5px; padding: 2px 7px;">{{ $enrollment->package_info['title'] ?? 'Paket 3 Bulan' }} ({{ $enrollment->total_sessions ?? ($enrollment->package_info['sessions'] ?? 12) }} Sesi)</span>
                    </div>
                    <p>{{ $enrollment->child->name }} • {{ $enrollment->course->category->name }}</p>
                    <div class="progress-bar"><span style="width:{{ $enrollment->progress }}%"></span></div>
                    <small>{{ $enrollment->progress }}% modul selesai</small>
                </div>
                <div class="row-actions">
                    @if($enrollment->certificate)<a class="btn btn-soft" href="{{ route('parent.certificates.show',$enrollment->certificate) }}"><x-icon name="certificate" /> Sertifikat</a>@endif
                    <a class="btn btn-primary" href="{{ route('parent.learn',$enrollment) }}">Belajar <x-icon name="arrow-right" /></a>
                </div>
            </div>
        @empty<div class="empty-state"><x-icon name="book" /><div><b>Belum ada course aktif</b><span>Course akan muncul di sini setelah pembayaran berhasil.</span></div></div>@endforelse
    </div>
</section>
@endsection
