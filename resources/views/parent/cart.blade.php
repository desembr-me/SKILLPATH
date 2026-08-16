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
                @php
                    $totalSavings = $items->sum(fn ($i) => (float) ($i->package_info['savings'] ?? 0));
                @endphp
                @forelse($items as $item)
                    @php
                        $pkg = $item->package_info;
                    @endphp
                    <article class="cart-item-card {{ !empty($item->conflicts) ? 'item-conflict' : '' }}">
                        <div class="cart-item-thumb-box">
                            <x-course-art :course="$item->schedule->course" />
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-head">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                        <span class="category-pill">{{ $item->schedule->course->category->name }}</span>
                                        <span class="status-chip {{ $pkg['badge_type'] === 'popular' ? 'recommended' : ($pkg['badge_type'] === 'best_value' ? 'confirmed' : 'soft') }}" style="font-size: 10px;">
                                            {{ $pkg['title'] }} • {{ $pkg['sessions'] }} Sesi
                                        </span>
                                    </div>
                                    <h3>{{ $item->schedule->course->title }}</h3>
                                </div>
                                <div class="cart-item-price" style="text-align: right;">
                                    @if($pkg['discount_percent'] > 0)
                                        <small style="text-decoration: line-through; color: var(--muted); font-size: 11px; display: block;">
                                            Rp{{ number_format($pkg['original_price'], 0, ',', '.') }}
                                        </small>
                                    @endif
                                    <b>Rp{{ number_format($item->calculated_price, 0, ',', '.') }}</b>
                                    @if($pkg['savings'] > 0)
                                        <small style="color: #059669; font-weight: 600; font-size: 10.5px; display: block;">
                                            Hemat Rp{{ number_format($pkg['savings'], 0, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="cart-item-meta">
                                <span class="meta-pill child-pill"><x-icon name="child" /> Siswa: <b>{{ $item->child->name }}</b></span>
                                <span class="meta-pill"><x-icon name="calendar" /> Hari {{ $item->schedule->day_name }}, {{ substr($item->schedule->start_time, 0, 5) }} - {{ substr($item->schedule->end_time, 0, 5) }} WIB</span>
                                <span class="meta-pill"><x-icon name="location" /> {{ $item->schedule->course->location_name }}</span>
                                @if($item->schedule->course->instructor)
                                    <span class="meta-pill"><x-icon name="user" /> {{ $item->schedule->course->instructor->name }}</span>
                                @endif
                            </div>

                            {{-- Pilihan Paket Switcher di Keranjang --}}
                            <div style="background: #f8fafc; border: 1px solid #eef2f6; border-radius: 12px; padding: 10px 14px; margin-top: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #475569;">
                                    <x-icon name="spark" />
                                    <span>Pilihan Durasi Paket:</span>
                                </div>
                                <form method="POST" action="{{ route('parent.cart.update', $item) }}" style="display: flex; align-items: center; gap: 8px;">
                                    @csrf
                                    @method('PUT')
                                    <select name="package_duration" onchange="this.form.submit()" style="font-size: 11px; padding: 5px 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: var(--ink); font-weight: 600; cursor: pointer;">
                                        @foreach($item->schedule->course->packages as $m => $p)
                                            <option value="{{ $m }}" {{ ($item->package_duration ?: 3) == $m ? 'selected' : '' }}>
                                                {{ $p['title'] }} ({{ $p['sessions'] }} Sesi) - Rp{{ number_format($p['price'], 0, ',', '.') }} {{ $p['discount_percent'] > 0 ? '(Hemat ' . $p['discount_percent'] . '%)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
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
                        <span>Subtotal Paket ({{ $items->count() }} kursus)</span>
                        <b>Rp{{ number_format($subtotal, 0, ',', '.') }}</b>
                    </div>
                    @if($totalSavings > 0)
                        <div class="summary-line" style="color: #059669;">
                            <span>Total Hemat Paket</span>
                            <b>-Rp{{ number_format($totalSavings, 0, ',', '.') }}</b>
                        </div>
                    @endif
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
