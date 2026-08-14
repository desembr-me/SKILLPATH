@extends('layouts.app')
@section('title', $category->name.' | SKILLPATH')
@section('content')
<section class="category-detail-hero kid-category-detail-hero">
    <div class="container">
        <a class="back-link" href="{{ route('categories.index') }}">← Semua kategori</a>
        <div class="category-detail-title"><div class="category-icon category-icon-large">{{ $category->icon }}</div><div><span class="eyebrow">Kategori Kelas Offline</span><h1>{{ $category->name }}</h1></div></div>
        <p>{{ $category->description }}</p>
        <div class="kid-level-chips large"><span>Beginner</span><span>Intermediate</span><span>Expert</span></div>
    </div>
</section>

@foreach($levels as $level => $paths)
<section class="section {{ $loop->even ? 'section-soft' : '' }} category-level-section" id="{{ strtolower($level) }}">
    <div class="container">
        <div class="section-heading split-heading">
            <div>
                <span class="eyebrow">Level {{ $loop->iteration }}</span>
                <h2>{{ $level }}</h2>
                <p>
                    @if($level === 'Beginner') Cocok untuk anak yang baru mengenal bidang {{ $category->name }}.
                    @elseif($level === 'Intermediate') Untuk anak yang sudah menguasai dasar dan ingin mencoba tantangan lebih terarah.
                    @else Untuk anak yang siap mendalami skill dan menyelesaikan proyek yang lebih menantang.
                    @endif
                </p>
            </div>
            <a class="text-link" href="{{ route('explore.index', ['category' => $category->slug, 'level' => $level]) }}">Filter level ini →</a>
        </div>

        <div class="course-market-grid kid-course-grid">
            @forelse ($paths as $path)
                <article class="market-course-card kid-course-card">
                    <div class="course-thumb">@if($path->thumbnailSrc())<img src="{{ $path->thumbnailSrc() }}" alt="Gambar {{ $path->title }}" loading="lazy">@else<span>{{ $path->icon }}</span>@endif<small>{{ strtoupper($level) }}</small></div>
                    <div class="course-card-body">
                        <span class="path-skill">{{ $path->skill?->name }}</span>
                        <h2>{{ $path->title }}</h2>
                        <p class="teacher-line">bersama {{ $path->instructor?->name ?? 'Tim SKILLPATH' }}</p>
                        <div class="rating-line"><strong>{{ number_format((float)($path->reviews->avg('rating') ?? 0), 1) }}</strong><span>★</span><small>{{ $path->enrollments_count }} peserta</small></div>
                        <div class="path-meta"><span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span><span>Offline</span></div>
                        <div class="course-price-row"><strong>{{ $path->is_free ? 'Gratis' : 'Rp'.number_format($path->effectivePrice(), 0, ',', '.') }}</strong></div>
                        <a class="btn btn-dark btn-full" href="{{ route('courses.show', $path) }}">Lihat Kelas</a>
                    </div>
                </article>
            @empty
                <article class="empty-card level-empty-card"><h3>Belum ada kelas {{ $level }}.</h3><p>Admin dapat menambahkan course baru pada level ini dari dashboard.</p></article>
            @endforelse
        </div>
    </div>
</section>
@endforeach
@endsection
