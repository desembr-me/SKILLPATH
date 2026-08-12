@extends('layouts.app')

@section('title', 'SKILLPATH | Upskilling Nonakademik Anak')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Belajar sesuai minat, tumbuh sesuai tahap</span>
            <h1>Skill nonakademik yang <span>lebih personal</span> untuk setiap anak.</h1>
            <p class="hero-lead">
                SKILLPATH membantu anak usia 5–14 tahun menemukan jalur belajar yang sesuai dengan minat, usia, dan progresnya.
            </p>

            <div class="hero-actions">
                @auth
                    <a class="btn btn-dark" href="{{ route('dashboard') }}">Lanjut Belajar</a>
                @else
                    <a class="btn btn-dark" href="{{ route('register') }}">Mulai Jalur Pertama</a>
                    <a class="btn btn-ghost" href="#cara-kerja">Lihat Cara Kerja</a>
                @endauth
            </div>

            <div class="trust-row" aria-label="Keunggulan SKILLPATH">
                <span>✓ Usia 5–14</span>
                <span>✓ Berbasis minat</span>
                <span>✓ Progres tersimpan</span>
            </div>
        </div>

        <div class="hero-stage" aria-label="Ilustrasi ruang belajar SKILLPATH">
            <div class="wall-brick brick-one"></div>
            <div class="wall-brick brick-two"></div>
            <div class="wall-brick brick-three"></div>

            <div class="skillbot">
                <div class="bot-ear left"></div>
                <div class="bot-ear right"></div>
                <div class="bot-head">
                    <div class="bot-face">
                        <span class="bot-eye"></span>
                        <span class="bot-eye"></span>
                        <span class="bot-smile"></span>
                    </div>
                </div>
                <div class="bot-body"><span>S</span></div>
                <div class="bot-leg left"></div>
                <div class="bot-leg right"></div>
            </div>

            <div class="learning-board">
                <div class="board-top">
                    <span class="board-dot"></span>
                    <span class="board-dot"></span>
                    <span class="board-dot"></span>
                </div>
                <p class="board-kicker">Rekomendasi hari ini</p>
                <h2>Temukan skill yang kamu suka.</h2>
                <div class="board-tags">
                    <span>Teknologi</span>
                    <span>Seni</span>
                    <span>Komunikasi</span>
                </div>
                <div class="board-progress">
                    <div style="width: 64%"></div>
                </div>
                <small>Jalurmu akan berubah mengikuti progres.</small>
            </div>
        </div>
    </div>
</section>

<section class="section" id="cara-kerja">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Cara kerja</span>
            <h2>Tiga langkah untuk mulai belajar.</h2>
            <p>Tidak ada katalog yang membuat anak bingung. Sistem menyaring pilihan secara bertahap.</p>
        </div>

        <div class="step-grid">
            <article class="step-card">
                <span class="step-number">1</span>
                <h3>Pilih minat</h3>
                <p>Anak memilih topik yang paling menarik perhatian saat ini.</p>
            </article>
            <article class="step-card">
                <span class="step-number">2</span>
                <h3>Dapatkan jalur</h3>
                <p>Sistem mencocokkan usia, minat, dan aktivitas yang sudah diselesaikan.</p>
            </article>
            <article class="step-card">
                <span class="step-number">3</span>
                <h3>Belajar bertahap</h3>
                <p>Anak menyelesaikan aktivitas pendek dan melihat progres secara langsung.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section-soft" id="jalur">
    <div class="container">
        <div class="section-heading split-heading">
            <div>
                <span class="eyebrow">Jelajah jalur</span>
                <h2>Skill nyata. Aktivitas singkat.</h2>
            </div>
            @guest
                <a class="text-link" href="{{ route('register') }}">Buat rekomendasi personal →</a>
            @endguest
        </div>

        <div class="path-grid">
            @forelse ($featuredPaths as $path)
                <article class="path-card">
                    <div class="path-icon">{{ $path->icon }}</div>
                    <span class="path-skill">{{ $path->skill->name }}</span>
                    <h3>{{ $path->title }}</h3>
                    <p>{{ $path->description }}</p>
                    <div class="path-meta">
                        <span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span>
                        <span>{{ $path->duration_minutes }} menit</span>
                    </div>
                    @auth
                        <a class="card-link" href="{{ route('learning.path', $path) }}">Buka jalur →</a>
                    @else
                        <a class="card-link" href="{{ route('register') }}">Mulai →</a>
                    @endauth
                </article>
            @empty
                <article class="empty-card">
                    <h3>Jalur belajar akan muncul setelah seeder dijalankan.</h3>
                    <code>php artisan db:seed</code>
                </article>
            @endforelse
        </div>
    </div>
</section>

<section class="section parent-section" id="orang-tua">
    <div class="container parent-grid">
        <div>
            <span class="eyebrow">Untuk orang tua</span>
            <h2>Fokus pada perkembangan, bukan sekadar durasi layar.</h2>
            <p>
                Dashboard menunjukkan aktivitas yang selesai, poin, minat yang dipilih, dan rekomendasi jalur berikutnya.
            </p>
            @guest
                <a class="btn btn-blue" href="{{ route('register') }}">Buat Profil Anak</a>
            @endguest
        </div>

        <div class="parent-panel">
            <div class="mini-stat">
                <strong>12</strong>
                <span>aktivitas selesai</span>
            </div>
            <div class="mini-stat">
                <strong>220</strong>
                <span>poin belajar</span>
            </div>
            <div class="mini-stat">
                <strong>3</strong>
                <span>minat aktif</span>
            </div>
        </div>
    </div>
</section>
@endsection
