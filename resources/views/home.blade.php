@extends('layouts.app')
@section('title','Temukan Minat Anak')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow-pill"><span class="pill-dot"></span> Belajar langsung, tumbuh lewat pengalaman</span>
        <h1>Temukan kegiatan yang membuat anak <span>ingin terus mencoba.</span></h1>
        <p>Anak ikut memilih minat dan aktivitas yang ingin dijelajahi. Orang tua tetap memegang kendali atas jadwal, transaksi, dan perkembangan belajar.</p>
        <div class="hero-actions">
            @auth
                @if(auth()->user()->role === 'parent')
                    <a class="btn btn-primary btn-lg" href="{{ route('parent.onboarding') }}">Temukan Minat Anak <x-icon name="arrow-right" /></a>
                @endif
            @else
                <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Mulai Bersama Anak <x-icon name="arrow-right" /></a>
            @endauth
            <a class="btn btn-white btn-lg" href="{{ route('explore.index') }}">Lihat Kursus</a>
        </div>
        <div class="hero-proof">
            <div><b>6</b><span>Kategori minat</span></div>
            <div><b>5-14</b><span>Rentang usia</span></div>
            <div><b>Offline</b><span>Belajar langsung</span></div>
        </div>
    </div>

    <div class="hero-stage" aria-label="Ilustrasi jalur belajar SkillPath">
        <span class="hero-orbit orbit-a"></span>
        <span class="hero-orbit orbit-b"></span>
        <div class="hero-board">
            <div class="board-top">
                <div class="profile-mark">A</div>
                <div><small>Minat Alya</small><b>Creative Explorer</b></div>
                <span class="match-badge">92% match</span>
            </div>
            <div class="board-question">Apa yang paling ingin dicoba minggu ini?</div>
            <div class="board-interests">
                <div class="interest-choice choice-art"><x-icon name="arts" /><span>Arts</span></div>
                <div class="interest-choice choice-tech active"><x-icon name="technology" /><span>Technology</span></div>
                <div class="interest-choice choice-growth"><x-icon name="self-improvement" /><span>Self Improvement</span></div>
            </div>
            <div class="board-path">
                <div class="path-head"><span>Jalur rekomendasi</span><b>2 dari 4 tahap</b></div>
                <div class="path-track"><i class="done"></i><i class="active"></i><i></i><i></i></div>
                <small>Berikutnya: Junior Robotics Lab</small>
            </div>
        </div>
        <div class="floating-note note-one"><x-icon name="calendar" /><span><b>Sabtu</b><small>10.00 - 11.30</small></span></div>
        <div class="floating-note note-two"><x-icon name="location" /><span><b>Offline</b><small>Kemang, Jakarta</small></span></div>
        <div class="floating-note note-three"><x-icon name="check" /><span><b>Jadwal aman</b><small>Tidak ada bentrok</small></span></div>
    </div>
</section>

<section class="trust-strip">
    <div>
        <span class="trust-icon"><x-icon name="payment" /></span>
        <p><b>Orang tua mengelola keputusan penting.</b><br>Booking, pembayaran, dan perubahan jadwal tetap berada pada akun orang tua.</p>
    </div>
    <div>
        <span class="trust-icon"><x-icon name="co-design" /></span>
        <p><b>Anak ikut menentukan arah belajar.</b><br>Co-design membuat rekomendasi berasal dari minat anak, bukan hanya asumsi orang tua.</p>
    </div>
</section>

<section class="section" id="categories">
    <div class="section-head">
        <div><span class="eyebrow">Eksplorasi minat</span><h2>Enam kategori untuk menemukan aktivitas yang terasa tepat.</h2></div>
        <p>Setiap kategori berisi kegiatan offline yang mendorong anak mencoba, berinteraksi, dan menghasilkan sesuatu secara nyata.</p>
    </div>
    <div class="category-grid">
        @foreach($categories as $category)
            <x-category-tile :category="$category" />
        @endforeach
    </div>
</section>

<section class="section section-soft popular-section">
    <div class="section-head">
        <div><span class="eyebrow">Pilihan populer</span><h2>Kursus yang siap diikuti secara offline.</h2></div>
        <a class="text-link" href="{{ route('explore.index') }}">Lihat semua kursus <x-icon name="arrow-right" /></a>
    </div>
    <div class="course-grid">
        @foreach($courses as $course)
        <article class="course-card">
            <div class="course-cover"><x-course-art :course="$course" /><span class="course-category">{{ $course->category->name }}</span></div>
            <div class="course-body">
                <div class="mentor-row"><span>Mentor {{ $course->instructor->name }}</span><span class="rating"><x-icon name="star" /> 4.9</span></div>
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
        @endforeach
    </div>
</section>

<section class="section section-soft mentor-section">
    <div class="section-head">
        <div><span class="eyebrow">Pengajar</span><h2>Mentor yang mendampingi anak Anda.</h2></div>
        <a class="text-link" href="{{ route('mentors.index') }}">Lihat semua mentor <x-icon name="arrow-right" /></a>
    </div>
    <div class="mentor-grid">
        @foreach($mentors as $mentor)
            <x-mentor-flip-card :mentor="$mentor" />
        @endforeach
    </div>
</section>

<section class="section parent-value-section">
    <div class="value-visual">
        <div class="value-card value-main">
            <span class="eyebrow">Untuk Orang Tua</span>
            <h3>Satu dashboard untuk semua kebutuhan belajar anak.</h3>
            <div class="value-list">
                <span><x-icon name="calendar" /> Jadwal kursus</span>
                <span><x-icon name="calendar" /> Ubah jadwal</span>
                <span><x-icon name="certificate" /> Ujian & sertifikat</span>
                <span><x-icon name="path" /> Jalur belajar</span>
            </div>
        </div>
        <span class="value-shape shape-a"></span><span class="value-shape shape-b"></span>
    </div>
    <div class="value-copy">
        <span class="eyebrow">Praktis untuk keluarga</span>
        <h2>Anak bebas bereksplorasi. Orang tua tetap punya kontrol.</h2>
        <p>SkillPath memisahkan pengalaman eksplorasi anak dari keputusan finansial. Anak cukup berpartisipasi pada pemilihan minat dan proses belajar.</p>
        <ul class="check-list">
            <li><x-icon name="check" /> Jadwal otomatis dicek sebelum booking.</li>
            <li><x-icon name="check" /> Jadwal dapat diubah jika anak berhalangan.</li>
            <li><x-icon name="check" /> Sertifikat hanya terbit setelah anak lulus ujian.</li>
        </ul>
    </div>
</section>

<section class="section" id="features">
    <div class="section-head"><div><span class="eyebrow">Fitur utama SkillPath</span><h2>Dirancang untuk situasi nyata keluarga.</h2></div><p>Fitur inti membantu keluarga mengelola kursus tanpa mengurangi keterlibatan anak dalam memilih apa yang ingin dipelajari.</p></div>
    <div class="feature-grid">
        <article><span class="feature-icon"><x-icon name="calendar" /></span><h3>Ubah Jadwal Fleksibel</h3><p>Anak berhalangan hadir? Orang tua dapat memindahkan course ke jadwal lain yang tersedia kapan saja.</p></article>
        <article><span class="feature-icon"><x-icon name="conflict" /></span><h3>Deteksi Jadwal Bentrok</h3><p>Sistem memeriksa course aktif anak sebelum booking dikonfirmasi dan menampilkan alternatif.</p></article>
        <article><span class="feature-icon"><x-icon name="certificate" /></span><h3>Ujian, Retake, Sertifikat</h3><p>Sertifikat baru terbit setelah lulus final exam. Retake mengikuti batas percobaan course.</p></article>
        <article><span class="feature-icon"><x-icon name="growth" /></span><h3>Self Improvement</h3><p>Kategori untuk percaya diri, komunikasi, kemampuan sosial, dan pengelolaan emosi.</p></article>
        <article><span class="feature-icon"><x-icon name="review" /></span><h3>Dua Jenis Ulasan</h3><p>Rating mentor dan platform dipisahkan agar admin dapat membaca sumber masalah secara lebih akurat.</p></article>
        <article><span class="feature-icon"><x-icon name="co-design" /></span><h3>Co-Design Onboarding</h3><p>Anak berpartisipasi langsung dalam pemilihan minat dan gaya belajar bersama orang tua.</p></article>
        <article class="feature-wide"><span class="feature-icon"><x-icon name="path" /></span><div><h3>Jalur Belajar Adaptif</h3><p>Course berikutnya direkomendasikan dari usia, minat hasil co-design, course sebelumnya, dan perkembangan anak.</p></div><a class="feature-cta" href="{{ route('how-it-works') }}">Pelajari alurnya <x-icon name="arrow-right" /></a></article>
    </div>
</section>

<section class="steps-section">
    <div class="center-head"><span class="eyebrow">Cara kerja</span><h2>Empat langkah dari minat hingga perkembangan.</h2><p>Alur sederhana agar anak terlibat tanpa perlu menangani transaksi atau administrasi.</p></div>
    <div class="steps">
        <article><b>01</b><span class="step-icon"><x-icon name="co-design" /></span><h3>Kenali minat</h3><p>Orang tua dan anak melakukan onboarding bersama.</p></article>
        <article><b>02</b><span class="step-icon"><x-icon name="path" /></span><h3>Lihat jalur belajar</h3><p>Sistem menyusun rekomendasi berdasarkan profil anak.</p></article>
        <article><b>03</b><span class="step-icon"><x-icon name="calendar" /></span><h3>Pilih jadwal</h3><p>SkillPath memeriksa bentrok sebelum booking dibuat.</p></article>
        <article><b>04</b><span class="step-icon"><x-icon name="certificate" /></span><h3>Pantau perkembangan</h3><p>Orang tua melihat progress, ujian, jadwal, dan sertifikat.</p></article>
    </div>
</section>

<section class="cta">
    <div><span class="cta-kicker">Mulai dari minat, bukan asumsi.</span><h2>Temukan kegiatan offline yang cocok untuk anak.</h2><p>Buat profil anak, pilih minat bersama, lalu lihat rekomendasi yang sesuai dengan usia dan preferensinya.</p></div>
    <a class="btn btn-white btn-lg" href="{{ auth()->check() && auth()->user()->role === 'parent' ? route('parent.onboarding') : route('register') }}">Mulai Sekarang <x-icon name="arrow-right" /></a>
</section>
@endsection
