@extends('layouts.app')
@section('title','Dashboard Orang Tua')
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div><span class="eyebrow">Dashboard Orang Tua</span><h1>Halo, {{ auth()->user()->name }}</h1><p>Pantau anak, course, jadwal, kredit, ujian, dan transaksi dari satu tempat.</p></div>
        <div class="dash-actions"><a class="btn btn-soft" href="{{ route('parent.exams') }}"><x-icon name="certificate" /> Ujian & Sertifikat</a><a class="btn btn-soft" href="{{ route('parent.credits') }}"><x-icon name="credit" /> Kredit Sesi</a><a class="btn btn-primary" href="{{ route('parent.onboarding') }}">Tambah Anak</a></div>
    </div>

    <div class="stat-grid">
        <article><span class="stat-icon"><x-icon name="child" /></span><div><span>Anak terdaftar</span><b>{{ $children->count() }}</b><small>Profil anak dalam akun keluarga</small></div></article>
        <article><span class="stat-icon"><x-icon name="sessions" /></span><div><span>Course aktif</span><b>{{ $children->sum(fn($c)=>$c->enrollments->where('status','active')->count()) }}</b><small>Kursus yang sedang berjalan</small></div></article>
        <article><span class="stat-icon"><x-icon name="credit" /></span><div><span>Kredit tersedia</span><b>{{ $children->sum(fn($c)=>$c->credits->where('status','available')->count()) }}</b><small>Dapat digunakan untuk reschedule</small></div></article>
        <article><span class="stat-icon"><x-icon name="payment" /></span><div><span>Transaksi terakhir</span><b>{{ $transactions->count() }}</b><small>Riwayat pembayaran terbaru</small></div></article>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Profil keluarga</span><h2>Anak Saya</h2></div><a class="text-link" href="{{ route('parent.onboarding') }}">Tambah profil <x-icon name="arrow-right" /></a></div>
            @forelse($children as $child)
            <div class="child-row">
                <div class="child-avatar">{{ strtoupper(substr($child->name,0,1)) }}</div>
                <div><h3>{{ $child->name }}</h3><p>{{ $child->age }} tahun • {{ implode(', ',$child->interests ?: []) ?: 'Minat belum dipilih' }}</p><div class="mini-tags"><span>{{ $child->enrollments->where('status','active')->count() }} course aktif</span><span>{{ $child->credits->where('status','available')->count() }} kredit</span></div></div>
                <a class="btn btn-soft" href="{{ route('parent.learning-path',$child) }}">Jalur Belajar</a>
            </div>
            @empty<div class="empty-state"><x-icon name="child" /><div><b>Belum ada profil anak</b><span>Mulai onboarding bersama anak untuk membuat rekomendasi awal.</span></div></div>@endforelse
        </div>
        <div class="panel">
            <div class="panel-heading"><div><span class="panel-kicker">Keuangan</span><h2>Transaksi</h2></div></div>
            @forelse($transactions as $trx)
            <div class="transaction-row"><div><b>{{ $trx->invoice_code }}</b><small>{{ $trx->created_at->format('d M Y') }}</small></div><div><b>Rp{{ number_format($trx->total,0,',','.') }}</b><span class="status-chip {{ $trx->status }}">{{ ucfirst($trx->status) }}</span>@if($trx->status==='pending')<form method="POST" action="{{ route('parent.transactions.pay',$trx) }}">@csrf<button class="pay-link">Bayar Demo</button></form>@endif</div></div>
            @empty<div class="empty-state compact-empty"><x-icon name="payment" /><div><b>Belum ada transaksi</b><span>Transaksi akan muncul setelah course dibooking.</span></div></div>@endforelse
        </div>
    </div>

    <div class="panel review-panel">
        <div class="panel-heading"><div><span class="panel-kicker">Ulasan Terpisah</span><h2>Nilai Mentor & Platform</h2></div></div>
        @forelse($reviewable as $enrollment)
        <form method="POST" action="{{ route('parent.reviews.store',$enrollment) }}" class="review-form">@csrf
            <div class="review-form-head"><b>{{ $enrollment->course->title }}</b><small>{{ $enrollment->child->name ?? '' }}</small></div>
            <div class="review-form-grid">
                <label>Rating Mentor
                    <select name="mentor_rating" required>
                        <option value="">Pilih nilai</option>
                        @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} - {{ ['Kurang','Cukup','Baik','Sangat baik','Istimewa'][$i-1] }}</option>@endfor
                    </select>
                </label>
                <label>Rating Platform
                    <select name="platform_rating" required>
                        <option value="">Pilih nilai</option>
                        @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} - {{ ['Kurang','Cukup','Baik','Sangat baik','Istimewa'][$i-1] }}</option>@endfor
                    </select>
                </label>
            </div>
            <div class="review-form-grid">
                <label>Ulasan mentor (opsional)<textarea name="mentor_review" rows="2" placeholder="Pengalaman belajar bersama mentor..."></textarea></label>
                <label>Ulasan platform (opsional)<textarea name="platform_review" rows="2" placeholder="Pengalaman booking, jadwal, transaksi..."></textarea></label>
            </div>
            <button class="btn btn-soft btn-sm">Kirim Ulasan</button>
        </form>
        @empty<div class="empty-state compact-empty"><x-icon name="review" /><div><b>Belum ada course yang perlu diulas</b><span>Ulasan tersedia setelah course aktif atau selesai.</span></div></div>@endforelse
    </div>
</section>
@endsection
