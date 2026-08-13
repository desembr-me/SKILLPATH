@extends('layouts.app')

@section('title', 'Untuk Orang Tua | SKILLPATH')

@section('content')
<section class="parent-page-hero">
    <div class="container parent-page-grid">
        <div>
            <span class="eyebrow">Untuk Orang Tua</span>
            <h1>Kelola pembelian kelas dan kegiatan tatap muka anak.</h1>
            <p>Orang tua menjadi pemegang akun. Anak menggunakan profilnya untuk mengikuti kelas yang sudah dibeli, memilih jadwal, dan hadir pada kegiatan tatap muka.</p>

            <div class="hero-actions">
                @auth
                    @if ($child)
                        <a class="btn btn-dark" href="{{ route('my-courses.index') }}">Kelas {{ $child->name }}</a>
                        <a class="btn btn-ghost" href="{{ route('orders.index') }}">Riwayat Pesanan</a>
                        <a class="btn btn-ghost" href="{{ route('onboarding.edit') }}">Ubah Profil Anak</a>
                    @else
                        <a class="btn btn-dark" href="{{ route('onboarding.edit') }}">Buat Profil Anak</a>
                    @endif
                @else
                    <a class="btn btn-dark" href="{{ route('register') }}">Buat Akun Orang Tua</a>
                    <a class="btn btn-ghost" href="{{ route('explore.index') }}">Lihat Kelas</a>
                @endauth
            </div>
        </div>

        <div class="parent-monitor-card">
            <span class="monitor-label">Ringkasan Akun</span>
            <div class="monitor-row"><span>Kelas terdaftar</span><strong>{{ $stats['registered_classes'] }}</strong></div>
            <div class="monitor-row"><span>Jadwal mendatang</span><strong>{{ $stats['upcoming_sessions'] }}</strong></div>
            <div class="monitor-row"><span>Sesi dihadiri</span><strong>{{ $stats['attended_sessions'] }}</strong></div>
            <div class="monitor-row"><span>Minat aktif</span><strong>{{ $stats['interests'] }}</strong></div>
            <div class="monitor-row"><span>Pesanan</span><strong>{{ $stats['orders'] }}</strong></div>
            @guest <small>Masuk untuk menampilkan data anak Anda.</small> @endguest
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Kontrol Orang Tua</span><h2>Satu akun untuk pendaftaran kelas dan transaksi.</h2></div>
        <div class="parent-feature-grid">
            <article class="step-card"><span class="feature-symbol">01</span><h3>Pilih dan beli kelas</h3><p>Bandingkan kategori, rentang usia, pengajar, tipe kelas, lokasi, harga, dan ulasan.</p></article>
            <article class="step-card"><span class="feature-symbol">02</span><h3>Kelola pendaftaran anak</h3><p>Kelas yang sudah dibayar otomatis terdaftar pada profil anak yang terhubung dengan akun orang tua.</p></article>
            <article class="step-card"><span class="feature-symbol">03</span><h3>Pantau kehadiran</h3><p>Lihat kelas terdaftar, jadwal, sesi yang dihadiri, dan sertifikat penyelesaian.</p></article>
            <article class="step-card"><span class="feature-symbol">04</span><h3>Atur jadwal kelas</h3><p>Pesan kursi kelas tatap muka dan lihat lokasi serta jadwal yang tersedia.</p></article>
            <article class="step-card"><span class="feature-symbol">05</span><h3>Riwayat transaksi</h3><p>Setiap pembelian tersimpan sebagai pesanan agar orang tua dapat memeriksa status pembayaran.</p></article>
            <article class="step-card"><span class="feature-symbol">06</span><h3>Komunikasi dengan pengajar</h3><p>Peserta yang sudah terdaftar dapat mengirim pertanyaan terkait kegiatan, lokasi, dan perlengkapan kelas.</p></article>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container safety-grid">
        <div><span class="eyebrow">Pendampingan Berdasarkan Usia</span><h2>Kontrol anak dibuat bertahap.</h2></div>
        <div class="safety-list">
            <div><strong>5–7 tahun</strong><span>Orang tua membantu membaca instruksi, menyiapkan perlengkapan, dan mendampingi anak ke lokasi.</span></div>
            <div><strong>8–10 tahun</strong><span>Anak mulai memilih kegiatan dengan persetujuan dan pemantauan orang tua.</span></div>
            <div><strong>11–14 tahun</strong><span>Anak mendapat kemandirian lebih besar, sedangkan orang tua tetap mengelola transaksi, jadwal, dan kehadiran.</span></div>
        </div>
    </div>
</section>
@endsection
