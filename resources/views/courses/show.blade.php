@extends('layouts.app')
@section('title',$course->title)
@section('content')
<section class="course-detail section compact">
    <div class="detail-grid">
        <div class="detail-visual"><x-course-art :course="$course" class="detail-art" /></div>
        <div class="detail-copy">
            <div class="detail-head-row">
                <span class="eyebrow">{{ $course->category->name }}</span>
                @auth
                    @if(auth()->user()->role==='parent')
                    <form method="POST" action="{{ route('parent.wishlist.toggle',$course) }}">@csrf<button class="wishlist-btn {{ $isWishlisted ? 'active' : '' }}" title="{{ $isWishlisted ? 'Hapus dari wishlist' : 'Tambah ke wishlist' }}"><x-icon name="heart" /></button></form>
                    @endif
                @endauth
            </div>
            <h1>{{ $course->title }}</h1>
            <p class="lead">{{ $course->subtitle }}</p>
            <div class="meta meta-lg">
                <span><x-icon name="child" /> {{ $course->age_min }}-{{ $course->age_max }} tahun</span>
                <span><x-icon name="location" /> {{ $course->location_name }}, {{ $course->city }}</span>
                <span><x-icon name="sessions" /> {{ $course->sessions_count }} sesi</span>
                <span><x-icon name="clock" /> {{ $course->duration_minutes }} menit</span>
            </div>
            <p>{{ $course->description }}</p>
            <div class="mentor-panel">
                <div class="mentor-avatar" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
                    @if($course->instructor->avatar_url)
                        <img src="{{ $course->instructor->avatar_url }}" alt="Foto {{ $course->instructor->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    @else
                        {{ $course->instructor->initial }}
                    @endif
                </div>
                <div><small>Mentor</small><b>{{ $course->instructor->name }}</b></div>
                <span class="rating"><x-icon name="star" /> 4.9</span>
            </div>
            <div class="course-price"><div><small>Harga paket</small><b>Rp{{ number_format($course->price,0,',','.') }}</b></div><span>{{ $course->sessions_count }} sesi pembelajaran offline</span></div>
        </div>
    </div>

    <div class="course-benefits">
        <article><x-icon name="check" /><div><b>Belajar langsung</b><small>Interaksi nyata dengan mentor dan kelompok kecil.</small></div></article>
        <article><x-icon name="path" /><div><b>Progress terpantau</b><small>Orang tua dapat mengikuti perkembangan dari dashboard.</small></div></article>
        <article><x-icon name="certificate" /><div><b>Sertifikat berbasis kelulusan</b><small>Sertifikat terbit setelah anak mencapai passing grade.</small></div></article>
    </div>

    <div class="booking-panel">
        <div class="booking-copy"><span class="eyebrow">Pilih jadwal offline</span><h2>Tambahkan ke keranjang</h2><p>Course akan masuk keranjang. Pengecekan jadwal bentrok dilakukan otomatis saat checkout.</p><div class="booking-note"><x-icon name="conflict" /><span>Pengecekan bentrok dilakukan otomatis sebelum checkout dikonfirmasi.</span></div></div>
        @auth
            @if(auth()->user()->role==='parent')
            <form method="POST" action="{{ route('parent.cart.store') }}" class="booking-form">@csrf
                <label>Anak<select name="child_id" required>@foreach(auth()->user()->children as $child)<option value="{{ $child->id }}">{{ $child->name }} • {{ $child->age }} tahun</option>@endforeach</select></label>
                <label>Jadwal<select name="schedule_id" required>@foreach($course->schedules as $schedule)<option value="{{ $schedule->id }}">Hari {{ $schedule->day_of_week }} • {{ substr($schedule->start_time,0,5) }}-{{ substr($schedule->end_time,0,5) }} • {{ $schedule->room }}</option>@endforeach</select></label>
                <button class="btn btn-primary btn-lg">Tambah ke Keranjang <x-icon name="cart" /></button>
            </form>
            @else<p class="muted-box">Booking hanya dapat dilakukan melalui akun orang tua.</p>@endif
        @else<a class="btn btn-primary btn-lg" href="{{ route('login') }}">Masuk sebagai Orang Tua <x-icon name="arrow-right" /></a>@endauth
    </div>
</section>
@endsection
