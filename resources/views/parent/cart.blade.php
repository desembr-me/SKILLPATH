@extends('layouts.app')
@section('title','Keranjang Booking')

@section('content')
<section class="dashboard-page cart-page-wrap">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Keranjang Belajar</span>
            <h1>Keranjang Booking Kursus</h1>
            <p>Tinjau jadwal dan pilihan kursus sebelum melanjutkan ke proses checkout.</p>
        </div>
        <div class="dash-actions">
            <a class="btn btn-soft" href="{{ route('parent.dashboard') }}">
                <x-icon name="arrow-left" /> Dashboard
            </a>
            <a class="btn btn-primary" href="{{ route('explore.index') }}">
                <x-icon name="plus" /> Tambah Kursus Lain
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash success">
            <x-icon name="check" />
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flash error">
            <x-icon name="conflict" />
            <div>
                <strong>Perhatian:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(!empty($hasConflict))
        <div class="conflict-alert-banner">
            <div class="conflict-alert-icon"><x-icon name="conflict" /></div>
            <div class="conflict-alert-copy">
                <h4>Pemberitahuan Bentrok Jadwal</h4>
                <p>Ada jadwal di keranjang yang bertabrakan dengan kursus aktif anak Anda. Anda tetap dapat melanjutkan checkout, namun item yang bentrok akan dipisahkan.</p>
            </div>
        </div>
    @endif

    <div class="cart-layout-grid">
        <div class="cart-items-panel panel">
            <div class="panel-heading">
                <div>
                    <span class="panel-kicker">Daftar Kursus</span>
                    <h2>Item dalam Keranjang ({{ $items->count() }})</h2>
                </div>
            </div>

            <div class="cart-items-list">
                @forelse($items as $item)
                    <article class="cart-item-card {{ !empty($item->conflicts) ? 'item-conflict' : '' }}">
                        <div class="cart-item-thumb-box">
                            <x-course-art :course="$item->schedule->course" />
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-head">
                                <div>
                                    <span class="category-pill">{{ $item->schedule->course->category->name }}</span>
                                    <h3>{{ $item->schedule->course->title }}</h3>
                                </div>
                                <div class="cart-item-price">
                                    Rp{{ number_format($item->schedule->course->price, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="cart-item-meta">
                                <span class="meta-pill child-pill"><x-icon name="child" /> Siswa: <b>{{ $item->child->name }}</b></span>
                                <span class="meta-pill"><x-icon name="calendar" /> Hari {{ $item->schedule->day_of_week }}, {{ substr($item->schedule->start_time, 0, 5) }} - {{ substr($item->schedule->end_time, 0, 5) }} WIB</span>
                                <span class="meta-pill"><x-icon name="location" /> {{ $item->schedule->course->location_name }}</span>
                                @if($item->schedule->course->instructor)
                                    <span class="meta-pill"><x-icon name="user" /> {{ $item->schedule->course->instructor->name }}</span>
                                @endif
                            </div>

                            @if(!empty($item->conflicts))
                                <div class="item-conflict-warning">
                                    <x-icon name="conflict" />
                                    <span>Bentrok dengan:
                                        @foreach($item->conflicts as $c)
                                            <b>{{ $c['course'] }} ({{ $c['schedule'] }})</b>{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </span>
                                </div>
                            @endif

                            <div class="cart-item-actions">
                                <a class="text-link" href="{{ route('courses.show', $item->schedule->course) }}">
                                    Lihat Detail Kursus <x-icon name="arrow-right" />
                                </a>
                                <form method="POST" action="{{ route('parent.cart.destroy', $item) }}" onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove-item">
                                        <x-icon name="trash" /> Hapus Kursus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <x-icon name="cart" />
                        <h3>Keranjang Booking Masih Kosong</h3>
                        <p>Pilih kelas yang diminati anak dari katalog kursus kami.</p>
                        <a class="btn btn-primary" href="{{ route('explore.index') }}" style="margin-top: 14px;">
                            <x-icon name="search" /> Jelajahi Katalog Kursus
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        @if($items->count())
            <aside class="cart-summary-aside">
                <div class="panel cart-summary-panel">
                    <span class="panel-kicker">Ringkasan Pesanan</span>
                    <h2>Estimasi Tagihan</h2>

                    <div class="summary-line">
                        <span>Subtotal ({{ $items->count() }} kursus)</span>
                        <b>Rp{{ number_format($subtotal, 0, ',', '.') }}</b>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Platform (Rp15.000/kursus)</span>
                        <b>Rp{{ number_format($platformFee, 0, ',', '.') }}</b>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-total">
                        <span>Total Pembayaran</span>
                        <b>Rp{{ number_format($total, 0, ',', '.') }}</b>
                    </div>

                    <a class="btn btn-primary btn-lg btn-block" href="{{ route('parent.checkout') }}">
                        <span>Lanjut ke Checkout</span> <x-icon name="arrow-right" />
                    </a>

                    <div class="cart-trust-note">
                        <x-icon name="shield-check" />
                        <small>Jadwal dan slot mentor akan diverifikasi langsung pada langkah checkout.</small>
                    </div>
                </div>
            </aside>
        @endif
    </div>
</section>
@endsection
