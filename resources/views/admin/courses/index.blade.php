@extends('admin.layouts.app')

@section('title', 'Kelola Course | Admin SKILLPATH')
@section('page-title', 'Course')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Manajemen kelas offline</span>
            <h2>Daftar course</h2>
            <p>Kelola course pada 6 kategori utama dan 3 level pembelajaran SKILLPATH.</p>
        </div>
        <div class="admin-head-actions">
            <span class="admin-total-pill">{{ $courses->total() }} course</span>
            <a class="admin-btn primary" href="{{ route('admin.courses.create') }}">+ Tambah Course</a>
        </div>
    </div>

    <form class="admin-filter-bar" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul course...">
        <select name="category">
            <option value="">Semua kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="level">
            <option value="">Semua level</option>
            @foreach($levels as $level)<option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>@endforeach
        </select>
        <select name="status">
            <option value="">Semua status</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
        <button class="admin-btn dark" type="submit">Terapkan</button>
        <a class="admin-text-link" href="{{ route('admin.courses.index') }}">Reset</a>
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table wide">
            <thead><tr><th>Course</th><th>Kategori / Level</th><th>Pengajar</th><th>Usia</th><th>Harga</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($courses as $course)
                <tr>
                    <td><div class="admin-course-cell">@if($course->thumbnailSrc())<img class="admin-course-mini-thumb" src="{{ $course->thumbnailSrc() }}" alt="Gambar {{ $course->title }}" loading="lazy">@else<span class="admin-course-mini-icon">{{ $course->icon }}</span>@endif<span><strong>{{ $course->title }}</strong><small>Kelas tatap muka</small></span></div></td>
                    <td><strong>{{ $course->categories->pluck('name')->first() ?: '-' }}</strong><small class="admin-level-mini">{{ $course->level }}</small></td>
                    <td>{{ $course->instructor?->name ?? '-' }}</td>
                    <td>{{ $course->min_age }}–{{ $course->max_age }} tahun</td>
                    <td>{{ $course->is_free ? 'Gratis' : 'Rp'.number_format($course->effectivePrice(), 0, ',', '.') }}</td>
                    <td>{{ $course->enrollments_count }}</td>
                    <td><span class="status-badge {{ $course->is_published ? 'published' : 'draft' }}">{{ $course->is_published ? 'PUBLISHED' : 'DRAFT' }}</span></td>
                    <td>
                        <div class="admin-action-row">
                            <a class="admin-icon-btn" href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                            <a class="admin-icon-btn image" href="{{ route('admin.courses.image.edit', $course) }}">Ganti Gambar</a>
                            <a class="admin-icon-btn" href="{{ route('courses.show', $course) }}" target="_blank">Lihat</a>
                            <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">@csrf @method('PATCH')<button class="admin-icon-btn {{ $course->is_published ? 'danger' : 'success' }}" type="submit">{{ $course->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Pindahkan course ini ke Recycle Bin?')">@csrf @method('DELETE')<button class="admin-icon-btn danger" type="submit">Hapus</button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="admin-empty-cell">Course tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $courses->links() }}</div>
</section>
@endsection
