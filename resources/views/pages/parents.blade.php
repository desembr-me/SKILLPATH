@extends('layouts.app')

@section('title', 'Untuk Orang Tua | SKILLPATH')

@section('content')
<section class="parent-page-hero">
    <div class="container parent-page-grid">
        <div>
            <span class="eyebrow">Untuk Orang Tua</span>
            <h1>Temukan kelas non-akademik, daftar, lalu pantau kehadiran anak.</h1>
            <p>Orang tua memegang akun dan transaksi. Anak mengikuti kegiatan tatap muka di lokasi yang tercantum, sementara jadwal, pemesanan kursi, kehadiran, dan sertifikat tersimpan rapi di SKILLPATH.</p>

            <div class="hero-actions">
                @auth
                    @if ($child)
                        <a class="btn btn-dark" href="{{ route('my-courses.index') }}">Kelas {{ $child->name }}</a>
                        <a class="btn btn-ghost" href="{{ route('class-schedules.index') }}">Jadwal Kelas</a>
                        <a class="btn btn-ghost" href="{{ route('orders.index') }}">Riwayat Pesanan</a>
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
            <div class="monitor-row"><span>Minat anak</span><strong>{{ $stats['interests'] }}</strong></div>
            <div class="monitor-row"><span>Pesanan</span><strong>{{ $stats['orders'] }}</strong></div>
            @guest <small>Masuk untuk menampilkan data anak Anda.</small> @endguest
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Kontrol Orang Tua</span><h2>Satu akun untuk pendaftaran kelas dan aktivitas offline anak.</h2></div>
        <div class="parent-feature-grid">
            <article class="step-card"><span class="feature-symbol">01</span><h3>Pilih kelas non-akademik</h3><p>Bandingkan kategori, rentang usia, pengajar, jadwal, lokasi, harga, fasilitas, dan ulasan.</p></article>
            <article class="step-card"><span class="feature-symbol">02</span><h3>Daftarkan anak</h3><p>Kelas yang sudah dibayar atau didaftarkan gratis otomatis terhubung dengan profil anak.</p></article>
            <article class="step-card"><span class="feature-symbol">03</span><h3>Pesan jadwal</h3><p>Pilih sesi tatap muka yang tersedia dan pastikan kursi anak tercatat sebelum datang ke lokasi.</p></article>
            <article class="step-card"><span class="feature-symbol">04</span><h3>Pantau kehadiran</h3><p>Lihat sesi yang akan datang, riwayat hadir, tidak hadir, atau pembatalan tanpa bergantung pada catatan manual.</p></article>
            <article class="step-card"><span class="feature-symbol">05</span><h3>Riwayat transaksi</h3><p>Setiap pembelian tersimpan sebagai pesanan agar status pembayaran mudah diperiksa.</p></article>
            <article class="step-card"><span class="feature-symbol">06</span><h3>Sertifikat penyelesaian</h3><p>Kelas yang mendukung sertifikat dapat menerbitkannya setelah peserta memenuhi kehadiran pada seluruh sesi wajib.</p></article>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container safety-grid">
        <div><span class="eyebrow">Pendampingan Sesuai Usia</span><h2>Pengalaman kelas tetap aman dan sesuai tahap perkembangan.</h2></div>
        <div class="safety-list">
            <div><strong>5–7 tahun</strong><span>Orang tua memastikan kesiapan anak, perlengkapan, transportasi, dan serah-terima di lokasi kelas.</span></div>
            <div><strong>8–10 tahun</strong><span>Anak mulai memilih kegiatan sesuai minat dengan persetujuan orang tua dan arahan pengajar.</span></div>
            <div><strong>11–14 tahun</strong><span>Anak mendapat kemandirian lebih besar, sedangkan orang tua tetap mengelola transaksi, jadwal, dan riwayat kehadiran.</span></div>
        </div>
    </div>
</section>
@endsection
