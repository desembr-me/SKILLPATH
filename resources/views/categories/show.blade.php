@extends('layouts.app')

@section('title', $category->name.' | SKILLPATH')

@section('content')
<section class="category-detail-hero">
    <div class="container">
        <a class="back-link" href="{{ route('categories.index') }}">← Semua kategori</a>
        <div class="category-detail-title">
            <div class="category-icon category-icon-large">{{ $category->icon }}</div>
            <div><span class="eyebrow">Kategori Kelas</span><h1>{{ $category->name }}</h1></div>
        </div>
        <p>{{ $category->description }}</p>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-heading split-heading">
            <div><span class="eyebrow">Kelas {{ $category->name }}</span><h2>Pilih kelas sesuai usia dan kebutuhan anak.</h2></div>
            <a class="text-link" href="{{ route('explore.index', ['category' => $category->slug]) }}">Filter lebih lengkap →</a>
        </div>

        <div class="course-market-grid">
            @forelse ($category->learningPaths as $path)
                <article class="market-course-card">
                    <div class="course-thumb"><span>{{ $path->icon }}</span><small>{{ strtoupper($path->classTypeLabel()) }}</small></div>
                    <div class="course-card-body">
                        <span class="path-skill">{{ $path->skill->name }}</span>
                        <h2>{{ $path->title }}</h2>
                        <p class="teacher-line">oleh {{ $path->instructor?->name ?? 'Tim SKILLPATH' }}</p>
                        <div class="rating-line"><strong>{{ number_format((float)($path->reviews->avg('rating') ?? 0), 1) }}</strong><span>★</span><small>{{ $path->enrollments_count }} peserta</small></div>
                        <div class="path-meta"><span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span><span>{{ $path->level }}</span></div>
                        <div class="course-price-row"><strong>{{ $path->is_free ? 'Gratis' : 'Rp'.number_format($path->effectivePrice(), 0, ',', '.') }}</strong></div>
                        <a class="btn btn-dark btn-full" href="{{ route('courses.show', $path) }}">Lihat Kelas</a>
                    </div>
                </article>
            @empty
                <article class="empty-card"><h3>Belum ada kelas pada kategori {{ $category->name }}.</h3></article>
            @endforelse
        </div>
    </div>
</section>
@endsection
