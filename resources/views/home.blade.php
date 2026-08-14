@extends('layouts.app')
@section('title','SKILLPATH | Kelas Nonakademik Offline Anak 5–14 Tahun')
@section('body-class','landing-page kid-landing-page')

@section('content')
<section class="hero landing-hero kid-hero">
    <div class="kid-doodle kid-doodle-one">✦</div>
    <div class="kid-doodle kid-doodle-two">●</div>
    <div class="kid-doodle kid-doodle-three">Aa</div>

    <div class="container landing-hero-grid">
        <div class="landing-hero-copy">
            <span class="eyebrow">Kelas offline nonakademik · usia 5–14 tahun</span>
            <h1>Temukan aktivitas yang bikin anak <span>semangat belajar.</span></h1>
            <p class="hero-lead">SKILLPATH membantu orang tua menemukan kelas tatap muka yang seru, sesuai minat anak, level kemampuan, pengajar, lokasi, dan jadwal.</p>

            <div class="hero-actions landing-actions">
                <a class="btn btn-dark" href="{{ route('explore.index') }}">Cari Kelas Seru</a>
                <a class="btn btn-ghost" href="#kategori">Lihat 6 Kategori</a>
            </div>

            <div class="landing-trust" aria-label="Keunggulan SKILLPATH">
                <span>✓ Tatap muka</span>
                <span>✓ Pengajar terverifikasi</span>
                <span>✓ Usia 5–14 tahun</span>
                <span>✓ 3 level belajar</span>
            </div>
        </div>

        <div class="landing-hero-visual kid-hero-stage" aria-label="Ilustrasi aktivitas kelas SKILLPATH">
            <div class="kid-stage-label">BELAJAR · BERMAIN · BERKARYA</div>

            <div class="skillbot landing-skillbot kid-skillbot">
                <div class="bot-ear left"></div>
                <div class="bot-ear right"></div>
                <div class="bot-head"><div class="bot-face"><span class="bot-eye"></span><span class="bot-eye"></span><span class="bot-smile"></span></div></div>
                <div class="bot-body"><span>S</span></div>
                <div class="bot-leg left"></div><div class="bot-leg right"></div>
            </div>

            <a class="kid-float-card card-arts" href="{{ route('explore.index',['category'=>'arts']) }}"><span>✎</span><strong>Arts</strong></a>
            <a class="kid-float-card card-music" href="{{ route('explore.index',['category'=>'music']) }}"><span>♫</span><strong>Music</strong></a>
            <a class="kid-float-card card-language" href="{{ route('explore.index',['category'=>'languages']) }}"><span>Aa</span><strong>Languages</strong></a>
            <a class="kid-float-card card-tech" href="{{ route('explore.index',['category'=>'technology']) }}"><span>&lt;/&gt;</span><strong>Technology</strong></a>

            <div class="kid-stage-board">
                <small>LEVEL BELAJAR</small>
                <div><span>Beginner</span><span>Intermediate</span><span>Expert</span></div>
            </div>
        </div>
    </div>
</section>

<section class="landing-proof kid-proof" aria-label="Ringkasan layanan">
    <div class="container landing-proof-grid">
        <div><strong>6</strong><span>Kategori pilihan</span></div>
        <div><strong>3</strong><span>Level tiap kategori</span></div>
        <div><strong>5–14</strong><span>Usia peserta</span></div>
        <div><strong>100%</strong><span>Kelas tatap muka</span></div>
    </div>
</section>

<section class="section category-home-section landing-section kid-category-section" id="kategori">
    <div class="container">
        <div class="section-heading split-heading landing-section-heading">
            <div>
                <span class="eyebrow">6 Kategori SKILLPATH</span>
                <h2>Anak suka yang mana?</h2>
                <p>Mulai dari rasa penasaran anak. Semua kategori tersedia dalam level Beginner, Intermediate, dan Expert.</p>
            </div>
            <a class="text-link" href="{{ route('categories.index') }}">Lihat semua kategori →</a>
        </div>

        <div class="category-grid category-grid-home landing-category-grid kid-category-grid">
            @foreach($categories as $category)
                <a class="category-card landing-category-card kid-category-card category-{{ $category->slug }}" href="{{ route('categories.show',$category) }}">
                    <div class="category-icon kid-category-icon">{{ $category->icon }}</div>
                    <div class="category-card-copy">
                        <span class="category-count">{{ $category->learning_paths_count }} kelas</span>
                        <h3>{{ $category->name }}</h3>
                        <p>{{ $category->description }}</p>
                        <div class="kid-level-chips"><span>Beginner</span><span>Intermediate</span><span>Expert</span></div>
                        <span class="category-link">Pilih kategori →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft landing-section kid-level-section">
    <div class="container">
        <div class="section-heading centered-heading">
            <span class="eyebrow">Naik Level Bertahap</span>
            <h2>Mulai dari yang pas, berkembang sampai makin percaya diri.</h2>
            <p>Tiga level tersedia pada setiap kategori supaya anak bisa belajar sesuai kemampuan, bukan sekadar berdasarkan umur.</p>
        </div>
        <div class="kid-level-roadmap">
            <article><span class="level-number">01</span><div><strong>Beginner</strong><p>Untuk anak yang baru mencoba. Fokus mengenal dasar melalui aktivitas ringan dan menyenangkan.</p></div></article>
            <div class="road-arrow">→</div>
            <article><span class="level-number">02</span><div><strong>Intermediate</strong><p>Untuk anak yang sudah menguasai dasar dan siap mencoba tantangan serta proyek yang lebih terarah.</p></div></article>
            <div class="road-arrow">→</div>
            <article><span class="level-number">03</span><div><strong>Expert</strong><p>Untuk anak yang siap mendalami skill, menyelesaikan proyek lebih kompleks, dan menunjukkan hasil karya.</p></div></article>
        </div>
    </div>
</section>

<section class="section landing-section" id="kelas-pilihan">
    <div class="container">
        <div class="section-heading split-heading landing-section-heading">
            <div>
                <span class="eyebrow">Kelas Pilihan</span>
                <h2>Kelas yang siap dicoba.</h2>
                <p>Semua berlangsung secara offline bersama pengajar, dengan aktivitas praktik yang sesuai usia dan level.</p>
            </div>
            <a class="text-link" href="{{ route('explore.index') }}">Jelajahi semua kelas →</a>
        </div>

        <div class="course-market-grid landing-course-grid kid-course-grid">
            @forelse($featuredPaths as $path)
                <article class="market-course-card landing-course-card kid-course-card">
                    <div class="course-thumb landing-course-thumb">@if($path->thumbnailSrc())<img src="{{ $path->thumbnailSrc() }}" alt="Gambar {{ $path->title }}" loading="lazy">@else<span>{{ $path->icon }}</span>@endif<small>{{ strtoupper($path->level) }}</small></div>
                    <div class="course-card-body">
                        <div class="course-category-line">@foreach($path->categories as $category)<span>{{ $category->name }}</span>@endforeach</div>
                        <h2>{{ $path->title }}</h2>
                        <p class="teacher-line">bersama {{ $path->instructor?->name ?? 'Tim SKILLPATH' }}</p>
                        <div class="rating-line"><strong>{{ number_format((float)($path->reviews->avg('rating')??0),1) }}</strong><span>★</span><small>{{ $path->reviews->count() }} ulasan</small></div>
                        <div class="path-meta"><span>Usia {{ $path->min_age }}–{{ $path->max_age }}</span><span>{{ $path->level }}</span></div>
                        <div class="course-price-row"><strong>{{ $path->is_free?'Gratis':'Rp'.number_format($path->effectivePrice(),0,',','.') }}</strong></div>
                        <a class="btn btn-dark btn-full" href="{{ route('courses.show',$path) }}">Lihat Kelas</a>
                    </div>
                </article>
            @empty
                <div class="empty-card landing-empty">Kelas pilihan sedang disiapkan.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="section landing-process-section kid-process-section" id="cara-belajar">
    <div class="container landing-process-grid">
        <div class="landing-process-copy">
            <span class="eyebrow">Cara Ikut Kelas</span>
            <h2>Mudah untuk orang tua, menyenangkan untuk anak.</h2>
            <p>Pilih kelas yang cocok, cek pengajar dan jadwal, daftar, lalu anak datang ke lokasi untuk belajar langsung.</p>
            <a class="text-link" href="{{ route('how-it-works') }}">Lihat cara kerja lengkap →</a>
        </div>
        <div class="landing-step-list">
            <article class="landing-step-card"><span>01</span><div><h3>Pilih kategori & level</h3><p>Arts, Music, Self Improvement, Languages, Sports, atau Technology.</p></div></article>
            <article class="landing-step-card"><span>02</span><div><h3>Kenali pengajar</h3><p>Lihat foto, keahlian, pengalaman, rating, dan profil pengajar.</p></div></article>
            <article class="landing-step-card"><span>03</span><div><h3>Cek jadwal & lokasi</h3><p>Pilih sesi tatap muka yang paling sesuai dengan rutinitas keluarga.</p></div></article>
            <article class="landing-step-card"><span>04</span><div><h3>Datang & berkembang</h3><p>Anak praktik langsung dan orang tua tetap dapat memantau progresnya.</p></div></article>
        </div>
    </div>
</section>

<section class="section landing-instructor-section kid-instructor-section" id="pengajar">
    <div class="container">
        <div class="section-heading split-heading landing-section-heading">
            <div>
                <span class="eyebrow">Pengajar</span>
                <h2>Wajah yang akan menemani anak belajar.</h2>
                <p>Profil pengajar menampilkan foto, keahlian, pengalaman, dan kelas yang dibawakan agar orang tua lebih yakin sebelum mendaftar.</p>
            </div>
            <a class="text-link" href="{{ route('instructors.index') }}">Semua pengajar →</a>
        </div>

        <div class="instructor-grid landing-instructor-grid">
            @forelse($featuredInstructors as $i)
                <article class="instructor-list-card landing-instructor-card kid-instructor-card">
                    <div class="teacher-avatar xlarge photo-avatar">
                        @if($i->instructorProfile?->photoSrc())
                            <img src="{{ $i->instructorProfile->photoSrc() }}" alt="Foto {{ $i->name }}">
                        @else
                            <span>{{ strtoupper(substr($i->name,0,1)) }}</span>
                        @endif
                    </div>
                    <div class="landing-instructor-copy">
                        <h2>{{ $i->name }}</h2>
                        <p>{{ $i->instructorProfile?->headline ?? 'Pengajar SKILLPATH' }}</p>
                        <div class="path-meta"><span>★ {{ $i->instructorProfile?->rating ?? '0.0' }}</span><span>{{ $i->courses_taught_count }} kelas</span></div>
                        <a class="card-link" href="{{ route('instructors.show',$i) }}">Lihat profil →</a>
                    </div>
                </article>
            @empty
                <div class="empty-card landing-empty">Profil pengajar sedang disiapkan.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="landing-final-cta kid-final-cta">
    <div class="container landing-final-cta-inner">
        <div><span class="eyebrow">Siap Mulai?</span><h2>Cari kelas yang bikin anak bilang, “Aku mau coba!”</h2><p>Pilih dari 6 kategori dan sesuaikan level, usia, pengajar, jadwal, serta lokasi.</p></div>
        <div class="landing-final-actions"><a class="btn btn-dark" href="{{ route('explore.index') }}">Cari Kelas</a><a class="btn btn-ghost" href="{{ route('parents') }}">Untuk Orang Tua</a></div>
    </div>
</section>
@endsection
