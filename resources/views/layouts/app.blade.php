<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffd21a">
    <title>@yield('title', 'SKILLPATH')</title>
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="SKILLPATH beranda">
            <span class="brand-mark">S</span>
            <span>SKILLPATH</span>
        </a>

        <button class="nav-toggle" type="button" data-nav-toggle aria-label="Buka navigasi">☰</button>

        <nav class="main-nav" data-nav>
            <a href="{{ route('home') }}#jalur">Jelajah Jalur</a>
            <a href="{{ route('home') }}#cara-kerja">Cara Kerja</a>
            <a href="{{ route('home') }}#orang-tua">Untuk Orang Tua</a>

            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link-button" type="submit">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a class="btn btn-blue btn-small" href="{{ route('register') }}">Mulai Gratis</a>
            @endauth
        </nav>
    </div>
</header>

@if (session('success'))
    <div class="container flash-wrap">
        <div class="flash success">{{ session('success') }}</div>
    </div>
@endif

<main>
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="brand footer-brand">
                <span class="brand-mark">S</span>
                <span>SKILLPATH</span>
            </div>
            <p>Jalur belajar nonakademik yang relevan dengan usia dan minat anak.</p>
        </div>
        <div>
            <strong>Platform</strong>
            <a href="{{ route('home') }}#jalur">Jalur Belajar</a>
            <a href="{{ route('home') }}#cara-kerja">Cara Kerja</a>
        </div>
        <div>
            <strong>Pengguna</strong>
            <a href="{{ route('home') }}#orang-tua">Orang Tua</a>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('register') }}">Daftar</a>
            @endauth
        </div>
    </div>
    <div class="container footer-bottom">
        <small>© {{ date('Y') }} SKILLPATH. Prototype edukasi.</small>
    </div>
</footer>

<script src="{{ asset('js/skillpath.js') }}"></script>
</body>
</html>
