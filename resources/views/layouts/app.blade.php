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
    <link rel="icon" type="image/png" href="{{ asset('images/skillpath-icon.png') }}">
    @stack('styles')
</head>
<body>
<header class="site-header" id="siteHeader">
    <a class="brand" href="{{ route('home') }}" aria-label="SkillPath home">
        <img class="brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath">
    </a>
    <button class="nav-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" data-nav-toggle>
        <span></span><span></span><span></span>
    </button>
    @auth
        @if(auth()->user()->isParent())
            <nav class="main-nav parent-main-nav" data-main-nav aria-label="Navigasi orang tua">
                <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"><x-icon name="sessions" /> Dashboard</a>
                <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}"><x-icon name="path" /> Semua Course</a>
                <a href="{{ route('parent.my-courses') }}" class="{{ request()->routeIs('parent.my-courses') || request()->routeIs('parent.learn') ? 'active' : '' }}"><x-icon name="book" /> Course Saya</a>
                <a href="{{ route('parent.cart') }}" class="{{ request()->routeIs('parent.cart') ? 'active' : '' }}"><x-icon name="cart" /> Keranjang @if(auth()->user()->cartItems()->count())<i class="nav-badge">{{ auth()->user()->cartItems()->count() }}</i>@endif</a>
                <a href="{{ route('parent.orders') }}" class="{{ request()->routeIs('parent.orders') ? 'active' : '' }}"><x-icon name="receipt" /> Pesanan</a>
                <a href="{{ route('parent.exams') }}" class="{{ request()->routeIs('parent.exams') || request()->routeIs('parent.certificates.*') ? 'active' : '' }}"><x-icon name="certificate" /> Ujian & Sertifikat</a>
                <a href="{{ route('parent.schedule') }}" class="{{ request()->routeIs('parent.schedule') ? 'active' : '' }}"><x-icon name="calendar" /> Jadwal</a>
                <a href="{{ route('parent.children') }}" class="{{ request()->routeIs('parent.children*') ? 'active' : '' }}"><x-icon name="child" /> Profil Anak</a>
            </nav>
            <div class="header-actions parent-header-actions">
                <a class="header-profile" href="{{ route('parent.profile') }}" aria-label="Profil saya">
                    @if(auth()->user()->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" alt="Foto {{ auth()->user()->name }}">
                    @else
                        <span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost">Keluar</button></form>
            </div>
        @elseif(auth()->user()->isMentor())
            <nav class="main-nav parent-main-nav" data-main-nav aria-label="Navigasi pengajar">
                <a href="{{ route('mentor.dashboard') }}" class="{{ request()->routeIs('mentor.dashboard') || request()->routeIs('mentor.students.show') ? 'active' : '' }}"><x-icon name="sessions" /> Dashboard</a>
                <a href="{{ route('mentor.reviews') }}" class="{{ request()->routeIs('mentor.reviews') ? 'active' : '' }}"><x-icon name="review" /> Ulasan</a>
                <a href="{{ route('mentor.profile') }}" class="{{ request()->routeIs('mentor.profile*') ? 'active' : '' }}"><x-icon name="child" /> Profil</a>
            </nav>
            <div class="header-actions parent-header-actions">
                <a class="header-profile" href="{{ route('mentor.profile') }}" aria-label="Profil pengajar">
                    @if(auth()->user()->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" alt="Foto {{ auth()->user()->name }}">
                    @else
                        <span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost">Keluar</button></form>
            </div>
        @else
            <nav class="main-nav" data-main-nav>
                <a href="{{ route('explore.index') }}">Kursus</a>
                <a href="{{ route('mentors.index') }}">Mentor</a>
                <a href="{{ route('home') }}#categories">Kategori</a>
                <a href="{{ route('how-it-works') }}">Cara Kerja</a>
                <a href="{{ route('home') }}#features">Fitur</a>
            </nav>
            <div class="header-actions">
                <a class="btn btn-soft" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost">Keluar</button></form>
            </div>
        @endif
    @else
        <nav class="main-nav" data-main-nav>
            <a href="{{ route('explore.index') }}">Kursus</a>
            <a href="{{ route('mentors.index') }}">Mentor</a>
            <a href="{{ route('home') }}#categories">Kategori</a>
            <a href="{{ route('how-it-works') }}">Cara Kerja</a>
            <a href="{{ route('home') }}#features">Fitur</a>
        </nav>
        <div class="header-actions">
            <a class="btn btn-ghost" href="{{ route('login') }}">Masuk</a>
            <a class="btn btn-primary" href="{{ route('register') }}">Daftar Orang Tua</a>
        </div>
    @endauth
</header>

@if(session('success'))
<div class="flash success"><x-icon name="check" /> <span>{{ session('success') }}</span></div>
@endif
@if($errors->any())
<div class="flash error"><strong>Ada data yang perlu diperiksa.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<main>@yield('content')</main>

@if(auth()->check() && auth()->user()->isParent())
<footer class="site-footer-compact">
    <a class="brand" href="{{ route('home') }}"><img class="brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath"></a>
    <p>SkillPath membantu orang tua menemukan dan mendaftarkan course offline non-akademik untuk anak usia 5-14 tahun.</p>
    <span>&copy; {{ now()->year }} SkillPath. Seluruh hak cipta dilindungi.</span>
</footer>
@else
<footer class="site-footer">
    <div class="footer-brand">
        <a class="brand" href="{{ route('home') }}"><img class="brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath"></a>
        <p>Kursus offline non-akademik yang membantu anak menemukan minat, mencoba keterampilan baru, dan berkembang melalui pengalaman nyata.</p>
        <span class="footer-note">Untuk keluarga dengan anak usia 5-14 tahun</span>
    </div>
    <div><strong>Eksplorasi</strong><a href="{{ route('explore.index') }}">Semua Kursus</a><a href="{{ route('home') }}#categories">Kategori</a><a href="{{ route('how-it-works') }}">Cara Kerja</a></div>
    <div><strong>Untuk Keluarga</strong><a href="{{ route('register') }}">Daftar Orang Tua</a><a href="{{ route('login') }}">Masuk</a><a href="{{ route('home') }}#features">Fitur SkillPath</a></div>
    <div><strong>Pengalaman</strong><span>Kelas offline</span><span>Mentor terpilih</span><span>Jalur belajar adaptif</span></div>
</footer>
@endif
<script src="{{ asset('js/skillpath.js') }}"></script>
@stack('scripts')
</body>
</html>
