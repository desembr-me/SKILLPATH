@extends('admin.layouts.app')

@section('title', 'Kategori | Admin SKILLPATH')
@section('page-title', 'Kategori')

@section('content')
<div class="admin-detail-grid category-admin-grid">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div><span class="admin-eyebrow">Struktur katalog</span><h2>Kategori course</h2></div>
        </div>

        <div class="admin-category-list">
            @foreach($categories as $category)
                <div class="admin-category-row">
                    <span class="admin-category-icon">{{ $category->icon }}</span>
                    <div><strong>{{ $category->name }}</strong><small>{{ $category->description ?: 'Tanpa deskripsi' }}</small></div>
                    <span class="admin-category-count">{{ $category->learning_paths_count }} course</span>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Pindahkan kategori ini ke Recycle Bin?')">
                        @csrf
                        @method('DELETE')
                        <button class="admin-icon-btn danger" type="submit">Hapus</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    <aside class="admin-panel admin-side-form">
        <span class="admin-eyebrow">Kategori baru</span>
        <h2>Tambah kategori</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <label><span>Nama kategori</span><input name="name" value="{{ old('name') }}" placeholder="Contoh: Dance" required></label>
            <label><span>Ikon singkat</span><input name="icon" value="{{ old('icon') }}" placeholder="Contoh: ♪" maxlength="20"></label>
            <label><span>Deskripsi</span><textarea name="description" rows="5" placeholder="Jelaskan fokus kategori...">{{ old('description') }}</textarea></label>
            <button class="admin-btn primary full" type="submit">Tambah Kategori</button>
        </form>
    </aside>
</div>
@endsection
