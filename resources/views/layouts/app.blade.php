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
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/skillpath-icon.png') }}">
    @stack('styles')
</head>
<body>
<header class="site-header" id="siteHeader">
    <a class="brand" href="{{ route('home') }}" aria-label="SkillPath home">
        <img class="brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath">
    </a>
    @auth
        @if(auth()->user()->isParent())
            <nav class="main-nav-pill-group parent-main-nav" data-main-nav aria-label="Navigasi Utama Orang Tua">
                <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard</span></a>
                <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}"><x-icon name="package" /> <span>Semua Kursus</span></a>
                <a href="{{ route('parent.my-courses') }}" class="{{ request()->routeIs('parent.my-courses') || request()->routeIs('parent.learn') ? 'active' : '' }}"><x-icon name="book" /> <span>Kursus Saya</span></a>
                <a href="{{ route('parent.schedule') }}" class="{{ request()->routeIs('parent.schedule') ? 'active' : '' }}"><x-icon name="calendar" /> <span>Jadwal Belajar</span></a>
                <a href="{{ route('parent.exams') }}" class="{{ request()->routeIs('parent.exams') || request()->routeIs('parent.certificates.*') ? 'active' : '' }}"><x-icon name="certificate" /> <span>Ujian & Sertifikat</span></a>
                <a href="{{ route('parent.children') }}" class="{{ request()->routeIs('parent.children*') || request()->routeIs('parent.learning-path*') ? 'active' : '' }}"><x-icon name="child" /> <span>Profil Anak</span></a>
            </nav>
            <div class="header-actions parent-header-actions">
                <div class="nav-action-icons">
                    <a href="{{ route('parent.cart') }}" class="nav-action-icon-btn {{ request()->routeIs('parent.cart') ? 'active' : '' }}" title="Keranjang Belanja" aria-label="Keranjang">
                        <x-icon name="cart" />
                        @if(auth()->user()->cartItems()->count())
                            <span class="nav-action-badge">{{ auth()->user()->cartItems()->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('parent.orders') }}" class="nav-action-icon-btn {{ request()->routeIs('parent.orders') ? 'active' : '' }}" title="Riwayat Pesanan" aria-label="Pesanan">
                        <x-icon name="receipt" />
                    </a>
                    <a href="{{ route('parent.payment') }}" class="nav-action-icon-btn nav-payment-btn {{ request()->routeIs('parent.payment*') || request()->routeIs('parent.transactions*') ? 'active' : '' }} {{ auth()->user()->pendingTransactionsCount() > 0 ? 'has-pending' : '' }}" title="Pembayaran & Tagihan" aria-label="Pembayaran">
                        <x-icon name="payment" />
                        @if(auth()->user()->pendingTransactionsCount() > 0)
                            <span class="nav-action-badge pulse-badge">{{ auth()->user()->pendingTransactionsCount() }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-divider"></div>
                <a class="header-profile parent-profile-pill" href="{{ route('parent.profile') }}" aria-label="Profil Saya" title="Profil Orang Tua: {{ auth()->user()->name }}">
                    <div class="avatar-ring">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                    <div class="header-profile-text">
                        <span class="user-name">{{ Str::limit(auth()->user()->name, 12) }}</span>
                        <span class="role-chip parent">Orang Tua</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost-logout" title="Keluar dari akun" aria-label="Keluar">
                        <x-icon name="logout" />
                        <span class="btn-text">Keluar</span>
                    </button>
                </form>
            </div>
        @elseif(auth()->user()->isMentor())
            <nav class="main-nav-pill-group mentor-main-nav" data-main-nav aria-label="Navigasi Mentor">
                <a href="{{ route('mentor.dashboard') }}" class="{{ request()->routeIs('mentor.dashboard') || request()->routeIs('mentor.students.show') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard</span></a>
                <a href="{{ route('mentor.schedules.index') }}" class="{{ request()->routeIs('mentor.schedules.*') ? 'active' : '' }}"><x-icon name="calendar" /> <span>Kelola Jadwal</span></a>
                <a href="{{ route('mentor.earnings') }}" class="{{ request()->routeIs('mentor.earnings') ? 'active' : '' }}"><x-icon name="wallet" /> <span>Pendapatan</span></a>
                <a href="{{ route('mentor.reviews') }}" class="{{ request()->routeIs('mentor.reviews') ? 'active' : '' }}"><x-icon name="review" /> <span>Ulasan Siswa</span></a>
                <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') ? 'active' : '' }}"><x-icon name="package" /> <span>Katalog Kursus</span></a>
            </nav>
            <div class="header-actions parent-header-actions">
                <div class="nav-action-icons">
                    <a href="{{ route('mentor.reschedules.index') }}" class="nav-action-icon-btn {{ request()->routeIs('mentor.reschedules.*') ? 'active' : '' }} {{ auth()->user()->unreadRescheduleRequestsCount() > 0 ? 'has-pending' : '' }}" title="Permintaan Jadwal" aria-label="Permintaan Jadwal">
                        <x-icon name="bell" />
                        @if(auth()->user()->unreadRescheduleRequestsCount() > 0)
                            <span class="nav-action-badge pulse-badge">{{ auth()->user()->unreadRescheduleRequestsCount() }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-divider"></div>
                <a class="header-profile mentor-profile-pill" href="{{ route('mentor.profile') }}" aria-label="Profil Pengajar" title="Profil Pengajar: {{ auth()->user()->name }}">
                    <div class="avatar-ring">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                    <div class="header-profile-text">
                        <span class="user-name">{{ Str::limit(auth()->user()->name, 12) }}</span>
                        <span class="role-chip mentor">Mentor</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost-logout" title="Keluar dari akun" aria-label="Keluar">
                        <x-icon name="logout" />
                        <span class="btn-text">Keluar</span>
                    </button>
                </form>
            </div>
        @else
            <nav class="main-nav-pill-group admin-main-nav" data-main-nav aria-label="Navigasi Administrator">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard Admin</span></a>
                <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"><x-icon name="package" /> <span>Kelola Kursus</span></a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><x-icon name="receipt" /> <span>Kelola Pesanan</span></a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><x-icon name="users" /> <span>Kelola Pengguna</span></a>
                <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}"><x-icon name="analytics" /> <span>Statistik</span></a>
            </nav>
            <div class="header-actions parent-header-actions">
                <div class="nav-action-icons">
                    <a href="{{ route('home') }}" class="nav-action-icon-btn" title="Lihat Website Utama" aria-label="Website Utama">
                        <x-icon name="external-link" />
                    </a>
                </div>
                <div class="nav-divider"></div>
                <a class="header-profile admin-profile-pill" href="{{ route('admin.profile') }}" aria-label="Profil Administrator" title="Administrator: {{ auth()->user()->name }}">
                    <div class="avatar-ring">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                    <div class="header-profile-text">
                        <span class="user-name">{{ Str::limit(auth()->user()->name, 12) }}</span>
                        <span class="role-chip admin">Admin</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost-logout" title="Keluar dari akun" aria-label="Keluar">
                        <x-icon name="logout" />
                        <span class="btn-text">Keluar</span>
                    </button>
                </form>
            </div>
        @endif
    @else
        <nav class="main-nav-pill-group guest-main-nav" data-main-nav aria-label="Navigasi Utama">
            <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}"><x-icon name="package" /> <span>Semua Kursus</span></a>
            <a href="{{ route('mentors.index') }}" class="{{ request()->routeIs('mentors.index') ? 'active' : '' }}"><x-icon name="mentor" /> <span>Mentor Terpilih</span></a>
            <a href="{{ route('home') }}#categories"><x-icon name="spark" /> <span>Kategori</span></a>
            <a href="{{ route('how-it-works') }}" class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}"><x-icon name="path" /> <span>Cara Kerja</span></a>
            <a href="{{ route('home') }}#features"><x-icon name="shield-check" /> <span>Keunggulan</span></a>
        </nav>
        <div class="header-actions guest-header-actions">
            <a class="btn btn-ghost" href="{{ route('login') }}"><x-icon name="login" /> Masuk</a>
            <a class="btn btn-primary btn-parent-register" href="{{ route('register') }}"><x-icon name="spark" /> Daftar Orang Tua</a>
        </div>
    @endauth
    <button class="nav-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" data-nav-toggle>
        <span></span><span></span><span></span>
    </button>
</header>

@if(session('success'))
<div class="flash success"><x-icon name="check" /> <span>{{ session('success') }}</span></div>
@endif
@if($errors->any())
<div class="flash error"><strong>Ada data yang perlu diperiksa.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<main>@yield('content')</main>

@if(auth()->check() && auth()->user()->isMentor())
    {{-- Footer dihilangkan untuk dashboard pengajar --}}
@elseif(auth()->check() && auth()->user()->isParent())
<footer class="site-footer-compact">
    <a class="brand" href="{{ route('home') }}"><img class="brand-logo" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath"></a>
    <p>SkillPath membantu orang tua menemukan dan mendaftarkan course offline non-akademik untuk anak usia 5-14 tahun.</p>
    <span>&copy; {{ now()->year }} SkillPath. Seluruh hak cipta dilindungi.</span>
</footer>
@elseif(!auth()->check())
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
