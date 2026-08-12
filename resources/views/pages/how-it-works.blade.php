@extends('layouts.app')

@section('title', 'Cara Kerja | SKILLPATH')

@section('content')
<section class="info-hero">
    <div class="container info-hero-grid">
        <div>
            <span class="eyebrow">Cara Kerja</span>
            <h1>Dari minat anak menjadi jalur belajar yang terarah.</h1>
            <p>
                SKILLPATH menggunakan data usia, minat, dan progres untuk menyederhanakan pilihan dan menjaga kesinambungan belajar.
            </p>
        </div>
        <div class="info-hero-card">
            <span class="info-card-label">Alur Adaptif</span>
            <strong>Usia + Minat + Progres</strong>
            <span>→ rekomendasi jalur</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Alur Pengguna</span>
            <h2>Lima tahap utama.</h2>
        </div>

        <div class="process-grid">
            @foreach ($steps as $step)
                <article class="process-card">
                    <span class="step-number">{{ $step['number'] }}</span>
                    <h2>{{ $step['title'] }}</h2>
                    <p>{{ $step['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container ucd-grid">
        <div>
            <span class="eyebrow">User-Centered Design</span>
            <h2>Antarmuka menyesuaikan kebutuhan anak dan orang tua.</h2>
            <p>
                Desain menggunakan navigasi singkat, area klik besar, bahasa sederhana, progres yang terlihat, serta pilihan yang tidak berlebihan.
            </p>
        </div>

        <div class="principle-grid">
            <article><strong>5–7 tahun</strong><span>Pendampingan lebih tinggi dan instruksi singkat.</span></article>
            <article><strong>8–10 tahun</strong><span>Pilihan mandiri dengan arahan visual yang jelas.</span></article>
            <article><strong>11–14 tahun</strong><span>Kontrol lebih besar terhadap jalur dan target belajar.</span></article>
            <article><strong>Orang tua</strong><span>Memantau aktivitas, minat, poin, dan progres.</span></article>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container cta-panel">
        <div>
            <span class="eyebrow">Mulai</span>
            <h2>Siapkan profil anak dan dapatkan rekomendasi pertama.</h2>
        </div>
        @auth
            <a class="btn btn-dark" href="{{ route('dashboard') }}">Buka Dashboard</a>
        @else
            <a class="btn btn-dark" href="{{ route('register') }}">Mulai Gratis</a>
        @endauth
    </div>
</section>
@endsection
