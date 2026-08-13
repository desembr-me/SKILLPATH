@extends('layouts.app')

@section('title', 'Cara Kerja | SKILLPATH')

@section('content')
<section class="info-hero">
    <div class="container info-hero-grid">
        <div>
            <span class="eyebrow">Cara Kerja</span>
            <h1>Dari minat anak menjadi kegiatan tatap muka yang nyata.</h1>
            <p>
                SKILLPATH membantu orang tua memilih kelas non-akademik berdasarkan usia dan minat, lalu mengelola pendaftaran, jadwal, lokasi, serta kehadiran dalam satu tempat.
            </p>
        </div>
        <div class="info-hero-card">
            <span class="info-card-label">Alur Kelas Offline</span>
            <strong>Minat + Usia + Jadwal</strong>
            <span>→ kelas yang sesuai</span>
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
            <span class="eyebrow">Pengalaman Orang Tua & Anak</span>
            <h2>Informasi penting tampil sebelum anak berangkat ke lokasi.</h2>
            <p>
                Setiap kelas menampilkan rentang usia, pengajar, lokasi, jadwal, kapasitas, fasilitas, dan persiapan yang diperlukan agar keputusan tidak dibuat berdasarkan foto promosi dan doa semata.
            </p>
        </div>

        <div class="principle-grid">
            <article><strong>5–7 tahun</strong><span>Program singkat dengan pendampingan dan proses antar-jemput yang jelas.</span></article>
            <article><strong>8–10 tahun</strong><span>Kegiatan eksploratif dengan arahan langsung dari pengajar.</span></article>
            <article><strong>11–14 tahun</strong><span>Program lebih mandiri, kolaboratif, dan berbasis keterampilan.</span></article>
            <article><strong>Orang tua</strong><span>Mengelola transaksi, jadwal, pemesanan kursi, dan riwayat kehadiran.</span></article>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container cta-panel">
        <div>
            <span class="eyebrow">Mulai</span>
            <h2>Siapkan profil anak dan temukan kelas non-akademik di sekitarnya.</h2>
        </div>
        @auth
            <a class="btn btn-dark" href="{{ route('dashboard') }}">Buka Dashboard</a>
        @else
            <a class="btn btn-dark" href="{{ route('register') }}">Mulai Gratis</a>
        @endauth
    </div>
</section>
@endsection
