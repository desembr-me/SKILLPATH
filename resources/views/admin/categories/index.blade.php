@extends('admin.layouts.app')
@section('title', 'Kategori | Admin SKILLPATH')
@section('page-title', 'Kategori')
@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Struktur katalog tetap</span>
            <h2>6 kategori utama SKILLPATH</h2>
            <p>Kategori dikunci agar katalog konsisten. Setiap kategori menggunakan level Beginner, Intermediate, dan Expert.</p>
        </div>
        <a class="admin-btn primary" href="{{ route('admin.courses.create') }}">+ Tambah Course</a>
    </div>

    <div class="admin-fixed-category-grid">
        @foreach($categories as $category)
            <article class="admin-fixed-category-card">
                <span class="admin-category-icon">{{ $category->icon }}</span>
                <div>
                    <strong>{{ $category->name }}</strong>
                    <p>{{ $category->description }}</p>
                    <div class="admin-level-chips"><span>Beginner</span><span>Intermediate</span><span>Expert</span></div>
                </div>
                <span class="admin-category-count">{{ $category->learning_paths_count }} course</span>
            </article>
        @endforeach
    </div>
</section>
@endsection
