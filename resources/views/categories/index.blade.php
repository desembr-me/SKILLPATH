@extends('layouts.app')
@section('title', '6 Kategori | SKILLPATH')
@section('content')
<section class="category-hero kid-category-hero">
    <div class="container">
        <span class="eyebrow">6 Kategori Kelas</span>
        <h1>Enam cara seru untuk menemukan minat anak.</h1>
        <p>Setiap kategori tersedia dalam tiga level: Beginner, Intermediate, dan Expert.</p>
    </div>
</section>
<section class="section category-page-section">
    <div class="container">
        <div class="category-grid kid-category-grid">
            @foreach ($categories as $category)
                <a class="category-card kid-category-card category-{{ $category->slug }}" href="{{ route('categories.show', $category) }}">
                    <div class="category-icon kid-category-icon">{{ $category->icon }}</div>
                    <div class="category-card-copy">
                        <span class="category-count">{{ $category->learning_paths_count }} kelas</span>
                        <h2>{{ $category->name }}</h2>
                        <p>{{ $category->description }}</p>
                        <div class="kid-level-chips"><span>Beginner</span><span>Intermediate</span><span>Expert</span></div>
                        <span class="category-link">Lihat kelas →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
