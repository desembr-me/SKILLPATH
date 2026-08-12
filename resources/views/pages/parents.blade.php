@extends('layouts.app')

@section('title', 'Untuk Orang Tua | SKILLPATH')

@section('content')
<section class="parent-page-hero">
    <div class="container parent-page-grid">
        <div>
            <span class="eyebrow">Untuk Orang Tua</span>
            <h1>Kelola pembelian course dan perkembangan belajar anak.</h1>
            <p>Orang tua menjadi pemegang akun. Anak menggunakan profil belajar untuk mengakses course yang sudah dibeli, mengikuti live class, dan menyelesaikan aktivitas.</p>

            <div class="hero-actions">
                @auth
                    @if ($child)
                        <a class="btn btn-dark" href="{{ route('my-courses.index') }}">Course {{ $child->name }}</a>
                        <a class="btn btn-ghost" href="{{ route('orders.index') }}">Riwayat Pesanan</a>
                        <a class="btn btn-ghost" href="{{ route('onboarding.edit') }}">Ubah Profil Anak</a>
                    @else
                        <a class="btn btn-dark" href="{{ route('onboarding.edit') }}">Buat Profil Anak</a>
                    @endif
                @else
                    <a class="btn btn-dark" href="{{ route('register') }}">Buat Akun Orang Tua</a>
                    <a class="btn btn-ghost" href="{{ route('explore.index') }}">Lihat Course</a>
                @endauth
            </div>
        </div>

        <div class="parent-monitor-card">
            <span class="monitor-label">Ringkasan Akun</span>
            <div class="monitor-row"><span>Course aktif</span><strong>{{ $stats['active_courses'] }}</strong></div>
            <div class="monitor-row"><span>Aktivitas selesai</span><strong>{{ $stats['completed_activities'] }}</strong></div>
            <div class="monitor-row"><span>Live class mendatang</span><strong>{{ $stats['upcoming_live'] }}</strong></div>
            <div class="monitor-row"><span>Total poin</span><strong>{{ $stats['total_points'] }}</strong></div>
            <div class="monitor-row"><span>Pesanan</span><strong>{{ $stats['orders'] }}</strong></div>
            @guest <small>Masuk untuk menampilkan data anak Anda.</small> @endguest
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading"><span class="eyebrow">Kontrol Orang Tua</span><h2>Satu akun untuk proses belajar dan transaksi.</h2></div>
        <div class="parent-feature-grid">
            <article class="step-card"><span class="feature-symbol">01</span><h3>Pilih dan beli course</h3><p>Bandingkan kategori, rentang usia, pengajar, tipe kelas, harga, dan ulasan.</p></article>
            <article class="step-card"><span class="feature-symbol">02</span><h3>Kelola akses anak</h3><p>Course yang sudah dibayar otomatis aktif pada profil anak yang terhubung dengan akun orang tua.</p></article>
            <article class="step-card"><span class="feature-symbol">03</span><h3>Pantau progres</h3><p>Lihat aktivitas yang selesai, poin, course aktif, dan sertifikat penyelesaian.</p></article>
            <article class="step-card"><span class="feature-symbol">04</span><h3>Atur live class</h3><p>Pesan kursi live class dan lihat jadwal sesi yang tersedia dari course anak.</p></article>
            <article class="step-card"><span class="feature-symbol">05</span><h3>Riwayat transaksi</h3><p>Setiap pembelian tersimpan sebagai pesanan agar orang tua dapat memeriksa status pembayaran.</p></article>
            <article class="step-card"><span class="feature-symbol">06</span><h3>Komunikasi dengan pengajar</h3><p>Peserta yang sudah terdaftar dapat mengirim pertanyaan terkait materi course.</p></article>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container safety-grid">
        <div><span class="eyebrow">Pendampingan Berdasarkan Usia</span><h2>Kontrol anak dibuat bertahap.</h2></div>
        <div class="safety-list">
            <div><strong>5–7 tahun</strong><span>Orang tua membantu membaca instruksi, mengatur perangkat, dan mendampingi aktivitas.</span></div>
            <div><strong>8–10 tahun</strong><span>Anak mulai memilih aktivitas dengan persetujuan dan pemantauan orang tua.</span></div>
            <div><strong>11–14 tahun</strong><span>Anak mendapat kemandirian lebih besar, sedangkan orang tua tetap mengelola transaksi dan laporan.</span></div>
        </div>
    </div>
</section>
@endsection
