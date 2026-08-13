@extends('layouts.app')
@section('title','Pengajar | SKILLPATH')
@section('content')
<section class="explore-hero">
    <div class="container">
        <span class="eyebrow">Pengajar SKILLPATH</span>
        <h1>Belajar bersama pengajar yang sesuai bidangnya.</h1>
        <p>Profil pengajar menampilkan keahlian, pengalaman, rating, dan kelas yang tersedia.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="instructor-grid">
            @foreach($instructors as $i)
                <article class="instructor-list-card">
                    <div class="teacher-avatar xlarge">{{ strtoupper(substr($i->name,0,1)) }}</div>
                    <div>
                        <div class="verify-line">
                            <h2>{{ $i->name }}</h2>
                            @if($i->instructorProfile?->is_verified)
                                <span>✓ Terverifikasi</span>
                            @endif
                        </div>
                        <p>{{ $i->instructorProfile?->headline }}</p>
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
