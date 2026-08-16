@extends('layouts.app')
@section('title', $mentor->name)
@section('content')
<section class="section compact">
    <div class="mentor-hero" style="--mentor-accent: {{ $mentor->category->accent ?? '#EAE5FF' }}">
        <div class="mentor-hero-photo">
            @if($mentor->avatar_url)
                <img src="{{ $mentor->avatar_url }}" alt="Foto {{ $mentor->name }}">
            @else
                <x-icon name="avatar" />
            @endif
        </div>
        <div class="mentor-hero-copy">
            @if($mentor->category)<span class="eyebrow">{{ $mentor->category->name }}</span>@endif
            <div class="mentor-name-row">
                <h1>{{ $mentor->name }}</h1>
                <span class="mentor-verified-pill" title="Mentor Resmi Terverifikasi SkillPath">
                    <x-icon name="verified" />
                    <span>Mentor Terverifikasi</span>
                </span>
            </div>
            <p class="mentor-hero-headline">{{ $mentor->headline ?: 'Mentor SkillPath' }}</p>
            @if($mentor->bio)<p class="mentor-bio">{{ $mentor->bio }}</p>@endif
            <div class="mini-tags">
                <span>{{ $mentor->courses->count() }} course diajar</span>
                <span>Rating {{ $mentor->rating ?: '0.0' }}</span>
            </div>
        </div>
    </div>
</section>

<section class="section compact">
    <div class="section-head">
        <div><span class="eyebrow">Course</span><h2>Course yang diajarkan {{ $mentor->name }}</h2></div>
        <a class="text-link" href="{{ route('mentors.index') }}">Lihat semua mentor <x-icon name="arrow-right" /></a>
    </div>
    <div class="course-grid">
        @forelse($mentor->courses as $course)
        <article class="course-card">
            <div class="course-cover"><x-course-art :course="$course" /><span class="course-category">{{ $course->category->name }}</span></div>
            <div class="course-body">
                <div class="mentor-row"><span>{{ $course->sessions_count }} sesi</span><span class="rating"><x-icon name="star" /> {{ $mentor->rating ?: '0.0' }}</span></div>
                <h3>{{ $course->title }}</h3>
                <p>{{ $course->subtitle }}</p>
                <div class="meta">
                    <span><x-icon name="child" /> {{ $course->age_min }}-{{ $course->age_max }} tahun</span>
                    <span><x-icon name="location" /> {{ $course->city }}</span>
                    <span><x-icon name="sessions" /> {{ $course->sessions_count }} sesi</span>
                </div>
                <div class="price-row"><div><b>Rp{{ number_format($course->price,0,',','.') }}</b><small>/ paket</small></div><a href="{{ route('courses.show',$course) }}">Lihat detail <x-icon name="arrow-right" /></a></div>
            </div>
        </article>
        @empty
            <div class="empty-state wide-empty"><x-icon name="book" /><div><b>Belum ada course</b><span>Mentor ini belum memiliki course aktif.</span></div></div>
        @endforelse
    </div>
</section>
@endsection
