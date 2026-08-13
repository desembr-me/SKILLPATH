@extends('layouts.app')
@section('title','Kelas | SKILLPATH')
@section('content')
<section class="explore-hero"><div class="container"><span class="eyebrow">Marketplace Kelas</span><h1>Temukan kelas tatap muka yang cocok untuk anak.</h1><p>Filter berdasarkan kategori, usia, kata kunci, dan urutan kelas.</p></div></section>
<section class="section explore-section"><div class="container">
<form class="filter-panel filter-panel-market" method="GET" action="{{ route('explore.index') }}">
<div class="filter-search"><label for="q">Cari kelas</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Coding, gitar, bahasa, menggambar..."></div>
<div><label>Kategori</label><select name="category"><option value="">Semua</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>@endforeach</select></div>
<div><label>Usia</label><select name="age"><option value="">5–14 tahun</option>@for($a=5;$a<=14;$a++)<option value="{{ $a }}" @selected((string)request('age')===(string)$a)>{{ $a }} tahun</option>@endfor</select></div>
<div><label>Urutkan</label><select name="sort"><option value="newest">Terbaru</option><option value="rating" @selected(request('sort')==='rating')>Rating</option></select></div>
<div class="filter-actions"><button class="btn btn-dark" type="submit">Terapkan</button><a class="filter-reset" href="{{ route('explore.index') }}">Reset</a></div>
</form>
<div class="explore-summary"><strong>{{ $paths->count() }}</strong><span>kelas ditemukan</span></div>
<div class="course-market-grid">
@forelse($paths as $path)
<article class="market-course-card">
<div class="course-thumb"><span>{{ $path->icon }}</span><small>{{ strtoupper($path->classTypeLabel()) }}</small></div>
<div class="course-card-body">
<div class="course-category-line">@foreach($path->categories as $c)<span>{{ $c->name }}</span>@endforeach</div>
<h2><a href="{{ route('courses.show',$path) }}">{{ $path->title }}</a></h2>
<p class="teacher-line">oleh {{ $path->instructor?->name ?? 'Tim SKILLPATH' }}</p>
<div class="rating-line"><strong>{{ number_format((float)($path->reviews->avg('rating') ?? 0),1) }}</strong><span>★</span><small>({{ $path->reviews->count() }})</small><small>{{ $path->enrollments_count }} peserta</small></div>
<div class="path-meta"><span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span><span>{{ $path->venue_summary ?: $path->classTypeLabel() }}</span></div>
<div class="course-price-row">
    @if ($path->is_free)
        <strong>Gratis</strong>
    @else
        @if ($path->sale_price)
            <del>Rp{{ number_format($path->price, 0, ',', '.') }}</del>
        @endif

        <strong>Rp{{ number_format($path->effectivePrice(), 0, ',', '.') }}</strong>
    @endif
</div>
<a class="btn btn-dark btn-full" href="{{ route('courses.show',$path) }}">Lihat Kelas</a>
</div></article>
@empty<article class="empty-card"><h2>Kelas tidak ditemukan.</h2><p>Ubah filter pencarian Anda.</p></article>@endforelse
</div></div></section>
@endsection
