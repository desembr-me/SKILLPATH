@extends('layouts.app')
@section('title','Katalog Kursus')
@section('content')
<section class="page-hero catalog-hero">
    <div>
        <span class="eyebrow">Katalog 6 Kategori SkillPath</span>
        <h1>Temukan kursus offline yang tepat untuk minat & level anak.</h1>
        <p>Tersedia 6 kategori utama dengan 3 tingkatan jenjang belajar berjenjang (Beginner, Intermediate, Expert) dibimbing mentor ahli langsung di studio/lapangan.</p>
    </div>
    <div class="page-hero-art"><x-icon name="path" /><span></span><span></span></div>
</section>
<section class="section compact">
    <form class="filter-bar" method="GET">
        <label>
            <span>Kategori</span>
            <select name="category">
                <option value="">Semua Kategori (6)</option>
                @foreach($categories as $c)
                    <option value="{{ $c->slug }}" @selected(request('category')===$c->slug)>{{ $c->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Tingkat Level</span>
            <select name="level">
                <option value="">Semua Level (3)</option>
                <option value="Beginner" @selected(request('level')==='Beginner')>Beginner (Pemula)</option>
                <option value="Intermediate" @selected(request('level')==='Intermediate')>Intermediate (Menengah)</option>
                <option value="Expert" @selected(request('level')==='Expert')>Expert (Lanjutan)</option>
            </select>
        </label>
        <label>
            <span>Usia Anak</span>
            <select name="age">
                <option value="">Semua Usia</option>
                @foreach(range(5,14) as $age)
                    <option value="{{ $age }}" @selected((string)request('age')===(string)$age)>{{ $age }} tahun</option>
                @endforeach
            </select>
        </label>
        <div style="display:flex; gap:8px; align-items:flex-end;">
            <button class="btn btn-primary" type="submit"><x-icon name="search" /> Terapkan Filter</button>
            <a class="btn btn-ghost" href="{{ route('explore.index') }}">Reset</a>
        </div>
    </form>
    <div class="catalog-summary">
        <b>{{ $courses->total() }} Kursus Ditemukan</b>
        <span>Semua kegiatan dilakukan tatap muka (offline) dengan protokol pendampingan terbaik.</span>
    </div>
    <div class="course-grid">
        @forelse($courses as $course)
        <article class="course-card">
            <div class="course-cover">
                <x-course-art :course="$course" />
                <span class="course-category">{{ $course->category->name }}</span>
                @php
                    $levelClass = match(strtolower($course->level ?? 'beginner')) {
                        'expert' => 'badge-expert',
                        'intermediate' => 'badge-intermediate',
                        default => 'badge-beginner'
                    };
                @endphp
                <span class="course-level-badge {{ $levelClass }}">{{ $course->level ?? 'Beginner' }}</span>
            </div>
            <div class="course-body">
                <div class="mentor-row">
                    <span>Mentor {{ $course->instructor->name }}</span>
                    <span class="rating"><x-icon name="star" /> 4.9</span>
                </div>
                <h3>{{ $course->title }}</h3>
                <p>{{ $course->subtitle }}</p>
                <div class="meta">
                    <span><x-icon name="child" /> {{ $course->age_min }}-{{ $course->age_max }} tahun</span>
                    <span><x-icon name="location" /> {{ $course->city }}</span>
                    <span><x-icon name="sessions" /> {{ $course->sessions_count }} sesi</span>
                </div>
                <div class="price-row">
                    <div>
                        <b>Rp{{ number_format($course->price,0,',','.') }}</b>
                        <small>/ 3 bln</small>
                    </div>
                    <a href="{{ route('courses.show',$course) }}">Pilih Paket <x-icon name="arrow-right" /></a>
                </div>
            </div>
        </article>
        @empty
        <div class="empty-state wide-empty">
            Belum ada course yang cocok dengan filter yang dipilih. Coba reset filter atau ubah pilihan kategori & level.
        </div>
        @endforelse
    </div>
    <div class="pagination">{{ $courses->links() }}</div>
</section>
@endsection

