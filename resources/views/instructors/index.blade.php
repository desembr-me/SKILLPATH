@extends('layouts.app')
@section('title','Pengajar | SKILLPATH')
@section('content')
<section class="explore-hero instructor-explore-hero">
    <div class="container">
        <span class="eyebrow">Pengajar SKILLPATH</span>
        <h1>Kenali pengajar sebelum anak masuk kelas.</h1>
        <p>Lihat foto, bidang keahlian, pengalaman, rating, dan kelas tatap muka yang tersedia.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="instructor-grid kid-instructor-grid">
            @foreach($instructors as $i)
                <article class="instructor-list-card kid-instructor-card">
                    <div class="teacher-avatar xlarge photo-avatar">
                        @if($i->instructorProfile?->photoSrc())
                            <img src="{{ $i->instructorProfile->photoSrc() }}" alt="Foto {{ $i->name }}">
                        @else
                            <span>{{ strtoupper(substr($i->name,0,1)) }}</span>
                        @endif
                    </div>
                    <div class="kid-instructor-content">
                        <div class="verify-line">
                            <h2>{{ $i->name }}</h2>
                            @if($i->instructorProfile?->is_verified)<span>✓ Terverifikasi</span>@endif
                        </div>
                        <p>{{ $i->instructorProfile?->headline ?: 'Pengajar kelas nonakademik SKILLPATH' }}</p>
                        <div class="path-meta">
                            <span>★ {{ $i->instructorProfile?->rating ?? 0 }}</span>
                            <span>{{ $i->instructorProfile?->years_experience ?? 0 }} tahun</span>
                            <span>{{ $i->courses_taught_count }} kelas</span>
                        </div>
                        <a class="btn btn-ghost" href="{{ route('instructors.show',$i) }}">Lihat Profil</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
