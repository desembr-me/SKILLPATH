@extends('layouts.app')
@section('title','Cara Kerja')
@section('content')
<section class="page-hero process-hero"><div><span class="eyebrow">Cara Kerja SkillPath</span><h1>Dari rasa ingin tahu menjadi pengalaman belajar nyata.</h1><p>SkillPath memisahkan keputusan finansial orang tua dari eksplorasi minat anak, lalu menghubungkannya dalam satu alur yang mudah dipantau.</p></div><div class="page-hero-art"><x-icon name="path" /><span></span><span></span></div></section>
<section class="section compact">
    <div class="timeline">
        <article><span>01</span><div class="timeline-icon"><x-icon name="users" /></div><div><h2>Orang tua membuat akun</h2><p>Akun utama digunakan untuk transaksi, booking, jadwal, review, dan monitoring perkembangan.</p></div></article>
        <article><span>02</span><div class="timeline-icon"><x-icon name="co-design" /></div><div><h2>Anak ikut onboarding co-design</h2><p>Anak memilih kategori dan gaya belajar yang disukai. Orang tua mendampingi, bukan menentukan seluruh jawaban.</p></div></article>
        <article><span>03</span><div class="timeline-icon"><x-icon name="path" /></div><div><h2>Sistem menyusun learning path</h2><p>Rekomendasi awal menggunakan usia, minat, preferensi, dan course yang tersedia.</p></div></article>
        <article><span>04</span><div class="timeline-icon"><x-icon name="calendar" /></div><div><h2>Orang tua memilih course dan jadwal</h2><p>SkillPath memeriksa bentrok dengan course aktif anak sebelum booking dibuat.</p></div></article>
        <article><span>05</span><div class="timeline-icon"><x-icon name="calendar" /></div><div><h2>Jadwal dapat diubah bila anak berhalangan</h2><p>Orang tua bisa langsung memindahkan course ke jadwal lain yang tersedia, tanpa proses persetujuan tambahan.</p></div></article>
        <article><span>06</span><div class="timeline-icon"><x-icon name="certificate" /></div><div><h2>Ujian menentukan sertifikat</h2><p>Anak perlu mencapai passing grade. Jika belum lulus, retake tersedia sampai batas percobaan.</p></div></article>
    </div>
</section>
@endsection
