<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffd21a">
    <title>@yield('title', 'SKILLPATH')</title>
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
    <link rel="stylesheet" href="{{ asset('css/certificate.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">S</span><span>SKILLPATH</span></a>
        <button class="nav-toggle" type="button" data-nav-toggle aria-label="Buka navigasi">☰</button>
        <nav class="main-nav" data-nav>
            <a class="{{ request()->routeIs('explore.*','courses.*') ? 'is-active' : '' }}" href="{{ route('explore.index') }}">Kelas</a>
            <a class="{{ request()->routeIs('categories.*') ? 'is-active' : '' }}" href="{{ route('categories.index') }}">Kategori</a>
            <a class="{{ request()->routeIs('instructors.*') ? 'is-active' : '' }}" href="{{ route('instructors.index') }}">Pengajar</a>
            <a class="{{ request()->routeIs('parents') ? 'is-active' : '' }}" href="{{ route('parents') }}">Orang Tua</a>
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @elseif(auth()->user()->role === 'instructor')
                    <a href="{{ route('instructor.dashboard') }}">Dashboard Pengajar</a>
                @else
                    <a href="{{ route('my-courses.index') }}">Kelas Saya</a>
                    <a href="{{ route('live.index') }}">Jadwal Kelas</a>
                    <a href="{{ route('cart.index') }}">Keranjang</a>
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link-button" type="submit">Keluar</button></form>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a class="btn btn-blue btn-small" href="{{ route('register') }}">Cari Kelas</a>
            @endauth
        </nav>
    </div>
</header>
@if(session('success'))<div class="container flash-wrap"><div class="flash success">{{ session('success') }}</div></div>@endif
@if($errors->any())<div class="container flash-wrap"><div class="flash error">{{ $errors->first() }}</div></div>@endif
<main>@yield('content')</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div><div class="brand footer-brand"><span class="brand-mark">S</span><span>SKILLPATH</span></div><p>Marketplace kelas nonakademik tatap muka untuk anak usia 5–14 tahun dengan pengajar, jadwal, lokasi, dan kegiatan praktik.</p></div>
        <div><strong>Kegiatan</strong><a href="{{ route('explore.index') }}">Semua Kelas</a><a href="{{ route('categories.index') }}">Kategori</a><a href="{{ route('instructors.index') }}">Pengajar</a></div>
        <div><strong>Akun</strong>@auth<a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'instructor' ? route('instructor.dashboard') : route('my-courses.index')) }}">Dashboard</a>@if(auth()->user()->role === 'parent')<a href="{{ route('orders.index') }}">Pesanan</a>@endif @else<a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}">Daftar</a>@endauth</div>
    </div>
    <div class="container footer-bottom"><small>© {{ date('Y') }} SKILLPATH. Marketplace kelas nonakademik tatap muka.</small></div>
</footer>
<script src="{{ asset('js/skillpath.js') }}"></script>
</body>
</html>
