@extends('layouts.app')

@section('title', 'Kategori Skill | SKILLPATH')

@section('content')
<section class="category-hero">
    <div class="container">
        <span class="eyebrow">Kategori Skill</span>
        <h1>Pilih bidang yang paling menarik.</h1>
        <p>
            Jelajahi kelas tersedia berdasarkan kategori. Setiap kategori berisi kegiatan nonakademik
            yang dapat dipilih sesuai minat dan usia anak.
        </p>
    </div>
</section>

<section class="section category-page-section">
    <div class="container">
        <div class="category-grid">
            @foreach ($categories as $category)
                <a class="category-card" href="{{ route('categories.show', $category) }}">
                    <div class="category-icon">{{ $category->icon }}</div>

                    <div class="category-card-copy">
                        <span class="category-count">
                            {{ $category->learning_paths_count }} kelas tersedia
                        </span>

                        <h2>{{ $category->name }}</h2>
                        <p>{{ $category->description }}</p>

                        <span class="category-link">Lihat kategori →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
