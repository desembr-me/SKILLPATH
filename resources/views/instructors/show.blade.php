@extends('layouts.app')
@section('title',$instructor->name.' | Pengajar SKILLPATH')
@section('content')
<section class="instructor-profile-hero kid-profile-hero">
    <div class="container instructor-profile-grid">
        <div class="teacher-avatar hero-avatar photo-avatar">
            @if($instructor->instructorProfile?->photoSrc())
                <img src="{{ $instructor->instructorProfile->photoSrc() }}" alt="Foto {{ $instructor->name }}">
            @else
                <span>{{ strtoupper(substr($instructor->name,0,1)) }}</span>
            @endif
        </div>
        <div>
            <span class="eyebrow">Pengajar Kelas Offline</span>
            <h1>{{ $instructor->name }}</h1>
            <p>{{ $instructor->instructorProfile?->headline ?: 'Pengajar kelas nonakademik SKILLPATH' }}</p>
            <div class="path-meta big">
                <span>★ {{ $instructor->instructorProfile?->rating ?? 0 }}</span>
                <span>{{ $instructor->instructorProfile?->years_experience ?? 0 }} tahun pengalaman</span>
                <span>{{ $instructor->instructorProfile?->students_count ?? 0 }} peserta</span>
                @if($instructor->instructorProfile?->is_verified)<span>✓ Terverifikasi</span>@endif
            </div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="content-card instructor-about-card">
            <h2>Tentang pengajar</h2>
            <p>{{ $instructor->instructorProfile?->bio ?: 'Profil lengkap pengajar sedang dilengkapi.' }}</p>
            <div class="instructor-info-grid">
                <div><small>KEAHLIAN</small><strong>{{ $instructor->instructorProfile?->expertise ?: '-' }}</strong></div>
                <div><small>PENDIDIKAN</small><strong>{{ $instructor->instructorProfile?->education ?: '-' }}</strong></div>
            </div>
        </div>

        <div class="section-heading"><span class="eyebrow">Kelas Tatap Muka</span><h2>Kelas dari {{ $instructor->name }}</h2></div>
        <div class="course-market-grid">
            @forelse($instructor->coursesTaught as $path)
                <article class="market-course-card kid-course-card">
                    <div class="course-thumb">@if($path->thumbnailSrc())<img src="{{ $path->thumbnailSrc() }}" alt="Gambar {{ $path->title }}" loading="lazy">@else<span>{{ $path->icon }}</span>@endif<small>{{ strtoupper($path->level) }}</small></div>
                    <div class="course-card-body">
                        <div class="course-category-line">@foreach($path->categories as $category)<span>{{ $category->name }}</span>@endforeach</div>
                        <h2>{{ $path->title }}</h2>
                        <p>{{ $path->description }}</p>
                        <div class="path-meta"><span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span><span>{{ $path->level }}</span></div>
                        <div class="course-price-row"><strong>{{ $path->is_free ? 'Gratis' : 'Rp'.number_format($path->effectivePrice(),0,',','.') }}</strong></div>
                        <a class="btn btn-dark btn-full" href="{{ route('courses.show',$path) }}">Lihat Kelas</a>
                    </div>
                </article>
            @empty
                <article class="empty-card"><h3>Belum ada kelas yang dipublikasikan.</h3></article>
            @endforelse
        </div>
    </div>
</section>
@endsection
