@extends('layouts.app')
@section('title','Katalog Kursus')
@section('content')
<section class="page-hero catalog-hero">
    <div><span class="eyebrow">Katalog SkillPath</span><h1>Temukan course offline yang pas untuk anak.</h1><p>Filter berdasarkan kategori dan usia. Setiap course menampilkan mentor, lokasi, jumlah sesi, dan jadwal yang tersedia.</p></div>
    <div class="page-hero-art"><x-icon name="path" /><span></span><span></span></div>
</section>
<section class="section compact">
    <form class="filter-bar" method="GET">
        <label><span>Kategori</span><select name="category"><option value="">Semua kategori</option>@foreach($categories as $c)<option value="{{ $c->slug }}" @selected(request('category')===$c->slug)>{{ $c->name }}</option>@endforeach</select></label>
        <label><span>Usia anak</span><select name="age"><option value="">Semua usia</option>@foreach(range(5,14) as $age)<option value="{{ $age }}" @selected((string)request('age')===(string)$age)>{{ $age }} tahun</option>@endforeach</select></label>
        <button class="btn btn-primary">Terapkan Filter</button>
        <a class="btn btn-ghost" href="{{ route('explore.index') }}">Reset</a>
    </form>
    <div class="catalog-summary"><b>{{ $courses->total() }} course ditemukan</b><span>Semua kegiatan dilakukan secara offline.</span></div>
    <div class="course-grid">
        @forelse($courses as $course)
        <article class="course-card">
            <div class="course-cover"><x-course-art :course="$course" /><span class="course-category">{{ $course->category->name }}</span></div>
            <div class="course-body">
                <div class="mentor-row"><span>Mentor {{ $course->instructor->name }}</span><span class="rating"><x-icon name="star" /> 4.9</span></div>
                <h3>{{ $course->title }}</h3><p>{{ $course->subtitle }}</p>
                <div class="meta"><span><x-icon name="child" /> {{ $course->age_min }}-{{ $course->age_max }} tahun</span><span><x-icon name="location" /> {{ $course->city }}</span><span><x-icon name="sessions" /> {{ $course->sessions_count }} sesi</span></div>
                <div class="price-row"><div><b>Rp{{ number_format($course->price,0,',','.') }}</b><small>/ paket</small></div><a href="{{ route('courses.show',$course) }}">Lihat detail <x-icon name="arrow-right" /></a></div>
            </div>
        </article>
        @empty<div class="empty-state wide-empty">Belum ada course yang cocok dengan filter. Coba ubah kategori atau usia anak.</div>@endforelse
    </div>
    <div class="pagination">{{ $courses->links() }}</div>
</section>
@endsection
