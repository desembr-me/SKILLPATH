@extends('layouts.app')

@section('title', 'Untuk Orang Tua | SKILLPATH')

@section('content')
<section class="parent-page-hero">
    <div class="container parent-page-grid">
        <div>
            <span class="eyebrow">Untuk Orang Tua</span>
            <h1>Kelola pendaftaran kelas dan perkembangan belajar anak.</h1>
            <p>Orang tua menjadi pemegang akun untuk memilih kelas nonakademik tatap muka, mengecek jadwal dan lokasi, memesan kursi, serta memantau perkembangan anak.</p>

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
            <div class="monitor-row"><span>Kelas aktif</span><strong>{{ $stats['active_courses'] }}</strong></div>
            <div class="monitor-row"><span>Aktivitas selesai</span><strong>{{ $stats['completed_activities'] }}</strong></div>
            <div class="monitor-row"><span>Jadwal mendatang</span><strong>{{ $stats['upcoming_live'] }}</strong></div>
            <div class="monitor-row"><span>Total poin</span><strong>{{ $stats['total_points'] }}</strong></div>
            <div class="monitor-row"><span>Pesanan</span><strong>{{ $stats['orders'] }}</strong></div>
            @guest <small>Masuk untuk menampilkan data anak Anda.</small> @endguest
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Kontrol Orang Tua</span><h2>Satu akun untuk kelas, jadwal, dan perkembangan anak.</h2></div>
        <div class="parent-feature-grid">
            <article class="step-card"><span class="feature-symbol">01</span><h3>Pilih dan daftar kelas</h3><p>Bandingkan kategori, rentang usia, pengajar, jadwal, harga, dan ulasan.</p></article>
            <article class="step-card"><span class="feature-symbol">02</span><h3>Cek lokasi & jadwal</h3><p>Lihat waktu pelaksanaan, kapasitas peserta, dan informasi lokasi sebelum datang.</p></article>
            <article class="step-card"><span class="feature-symbol">03</span><h3>Pantau progres</h3><p>Lihat aktivitas yang selesai, poin, kelas aktif, dan sertifikat penyelesaian.</p></article>
            <article class="step-card"><span class="feature-symbol">04</span><h3>Pesan kursi kelas</h3><p>Pilih sesi tatap muka yang tersedia dan pastikan jadwal anak tidak bertabrakan.</p></article>
            <article class="step-card"><span class="feature-symbol">05</span><h3>Riwayat transaksi</h3><p>Setiap pendaftaran tersimpan sebagai pesanan agar orang tua dapat memeriksa status pembayaran.</p></article>
            <article class="step-card"><span class="feature-symbol">06</span><h3>Komunikasi dengan pengajar</h3><p>Peserta yang sudah terdaftar dapat mengirim pertanyaan terkait kelas dan perlengkapan yang perlu dibawa.</p></article>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container safety-grid">
        <div><span class="eyebrow">Pendampingan Berdasarkan Usia</span><h2>Pengalaman kelas disesuaikan dengan usia anak.</h2></div>
        <div class="safety-list">
            <div><strong>5–7 tahun</strong><span>Orang tua membantu persiapan, memastikan perlengkapan, dan mendampingi bila diperlukan.</span></div>
            <div><strong>8–10 tahun</strong><span>Anak mulai memilih kelas sesuai minat dengan persetujuan dan pemantauan orang tua.</span></div>
            <div><strong>11–14 tahun</strong><span>Anak mendapat kemandirian lebih besar, sedangkan orang tua tetap mengelola jadwal, transaksi, dan laporan.</span></div>
        </div>
    </div>
</section>
@endsection
