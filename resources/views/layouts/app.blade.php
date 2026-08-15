<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SkillPath, marketplace kursus offline non-akademik untuk anak usia 5-14 tahun.">
    <title>@yield('title', 'SkillPath') | Kursus Offline Anak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
    @stack('styles')
</head>
<body>
<div class="announcement-bar">
    <span class="announcement-dot"></span>
    <span>Kursus offline non-akademik untuk anak usia 5-14 tahun</span>
    <a href="{{ route('how-it-works') }}">Lihat cara kerja</a>
</div>
<header class="site-header" id="siteHeader">
    <a class="brand" href="{{ route('home') }}" aria-label="SkillPath home">
        <span class="brand-mark">S</span>
        <span>SkillPath</span>
    </a>
    <button class="nav-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" data-nav-toggle>
        <span></span><span></span><span></span>
    </button>
    <nav class="main-nav" data-main-nav>
        <a href="{{ route('explore.index') }}">Kursus</a>
        <a href="{{ route('home') }}#categories">Kategori</a>
        <a href="{{ route('how-it-works') }}">Cara Kerja</a>
        <a href="{{ route('home') }}#features">Fitur</a>
    </nav>
    <div class="header-actions">
        @auth
            @php($dashboard = auth()->user()->role === 'admin' ? 'admin.dashboard' : (auth()->user()->role === 'mentor' ? 'mentor.dashboard' : 'parent.dashboard'))
            <a class="btn btn-soft" href="{{ route($dashboard) }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost">Keluar</button></form>
        @else
            <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
            <a class="btn btn-primary" href="{{ route('register') }}">Daftar Orang Tua</a>
        @endauth
    </div>
</header>

@if(session('success'))
<div class="flash success"><x-icon name="check" /> <span>{{ session('success') }}</span></div>
@endif
@if($errors->any())
<div class="flash error"><strong>Ada data yang perlu diperiksa.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<main>@yield('content')</main>

<footer class="site-footer">
    <div class="footer-brand">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark">S</span><span>SkillPath</span></a>
        <p>Kursus offline non-akademik yang membantu anak menemukan minat, mencoba keterampilan baru, dan berkembang melalui pengalaman nyata.</p>
        <span class="footer-note">Untuk keluarga dengan anak usia 5-14 tahun</span>
    </div>
    <div><strong>Eksplorasi</strong><a href="{{ route('explore.index') }}">Semua Kursus</a><a href="{{ route('home') }}#categories">Kategori</a><a href="{{ route('how-it-works') }}">Cara Kerja</a></div>
    <div><strong>Untuk Keluarga</strong><a href="{{ route('register') }}">Daftar Orang Tua</a><a href="{{ route('login') }}">Masuk</a><a href="{{ route('home') }}#features">Fitur SkillPath</a></div>
    <div><strong>Pengalaman</strong><span>Kelas offline</span><span>Mentor terpilih</span><span>Jalur belajar adaptif</span></div>
</footer>
<script src="{{ asset('js/skillpath.js') }}"></script>
@stack('scripts')
</body>
</html>
