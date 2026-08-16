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
            {{-- Desktop Nav: Parent --}}
            <nav class="main-nav-pill-group parent-main-nav desktop-nav" aria-label="Navigasi Utama Orang Tua">
                <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard</span></a>
                <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}"><x-icon name="package" /> <span>Semua Kursus</span></a>
                <a href="{{ route('parent.my-courses') }}" class="{{ request()->routeIs('parent.my-courses') || request()->routeIs('parent.learn') ? 'active' : '' }}"><x-icon name="book" /> <span>Kursus Saya</span></a>
                <a href="{{ route('parent.schedule') }}" class="{{ request()->routeIs('parent.schedule') ? 'active' : '' }}"><x-icon name="calendar" /> <span>Jadwal Belajar</span></a>
                <a href="{{ route('parent.exams') }}" class="{{ request()->routeIs('parent.exams') || request()->routeIs('parent.certificates.*') ? 'active' : '' }}"><x-icon name="certificate" /> <span>Ujian & Sertifikat</span></a>
                <a href="{{ route('parent.children') }}" class="{{ request()->routeIs('parent.children*') || request()->routeIs('parent.learning-path*') ? 'active' : '' }}"><x-icon name="child" /> <span>Profil Anak</span></a>
            </nav>
            {{-- Desktop Actions: Parent --}}
            <div class="header-actions parent-header-actions desktop-actions">
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
            {{-- Mobile Actions Bar: Parent --}}
            <div class="mobile-header-actions">
                <a href="{{ route('parent.cart') }}" class="mobile-action-btn {{ request()->routeIs('parent.cart') ? 'active' : '' }}" title="Keranjang Belanja" aria-label="Keranjang">
                    <x-icon name="cart" />
                    @if(auth()->user()->cartItems()->count())
                        <span class="mobile-badge">{{ auth()->user()->cartItems()->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('parent.profile') }}" class="mobile-avatar-pill" title="Profil {{ auth()->user()->name }}" aria-label="Profil">
                    <div class="avatar-ring sm">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                </a>
                <button class="nav-toggle" type="button" aria-label="Buka Menu Navigasi" aria-expanded="false" data-nav-toggle>
                    <span></span><span></span><span></span>
                </button>
            </div>
        @elseif(auth()->user()->isMentor())
            {{-- Desktop Nav: Mentor --}}
            <nav class="main-nav-pill-group mentor-main-nav desktop-nav" aria-label="Navigasi Mentor">
                <a href="{{ route('mentor.dashboard') }}" class="{{ request()->routeIs('mentor.dashboard') || request()->routeIs('mentor.students.show') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard</span></a>
                <a href="{{ route('mentor.schedules.index') }}" class="{{ request()->routeIs('mentor.schedules.*') ? 'active' : '' }}"><x-icon name="calendar" /> <span>Kelola Jadwal</span></a>
                <a href="{{ route('mentor.earnings') }}" class="{{ request()->routeIs('mentor.earnings') ? 'active' : '' }}"><x-icon name="wallet" /> <span>Pendapatan</span></a>
                <a href="{{ route('mentor.reviews') }}" class="{{ request()->routeIs('mentor.reviews') ? 'active' : '' }}"><x-icon name="review" /> <span>Ulasan Siswa</span></a>
                <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') ? 'active' : '' }}"><x-icon name="package" /> <span>Katalog Kursus</span></a>
            </nav>
            {{-- Desktop Actions: Mentor --}}
            <div class="header-actions parent-header-actions desktop-actions">
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
            {{-- Mobile Actions Bar: Mentor --}}
            <div class="mobile-header-actions">
                <a href="{{ route('mentor.reschedules.index') }}" class="mobile-action-btn {{ request()->routeIs('mentor.reschedules.*') ? 'active' : '' }}" title="Permintaan Jadwal" aria-label="Permintaan Jadwal">
                    <x-icon name="bell" />
                    @if(auth()->user()->unreadRescheduleRequestsCount() > 0)
                        <span class="mobile-badge pulse-badge">{{ auth()->user()->unreadRescheduleRequestsCount() }}</span>
                    @endif
                </a>
                <a href="{{ route('mentor.profile') }}" class="mobile-avatar-pill" title="Profil {{ auth()->user()->name }}" aria-label="Profil">
                    <div class="avatar-ring sm">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                </a>
                <button class="nav-toggle" type="button" aria-label="Buka Menu Navigasi" aria-expanded="false" data-nav-toggle>
                    <span></span><span></span><span></span>
                </button>
            </div>
        @else
            {{-- Desktop Nav: Admin --}}
            <nav class="main-nav-pill-group admin-main-nav desktop-nav" aria-label="Navigasi Administrator">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><x-icon name="dashboard" /> <span>Dashboard Admin</span></a>
                <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}"><x-icon name="package" /> <span>Kelola Kursus</span></a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><x-icon name="receipt" /> <span>Kelola Pesanan</span></a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><x-icon name="users" /> <span>Kelola Pengguna</span></a>
                <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}"><x-icon name="analytics" /> <span>Statistik</span></a>
            </nav>
            {{-- Desktop Actions: Admin --}}
            <div class="header-actions parent-header-actions desktop-actions">
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
            {{-- Mobile Actions Bar: Admin --}}
            <div class="mobile-header-actions">
                <a href="{{ route('admin.profile') }}" class="mobile-avatar-pill" title="Profil Administrator" aria-label="Profil">
                    <div class="avatar-ring sm">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            <span>{{ auth()->user()->initial }}</span>
                        @endif
                    </div>
                </a>
                <button class="nav-toggle" type="button" aria-label="Buka Menu Navigasi" aria-expanded="false" data-nav-toggle>
                    <span></span><span></span><span></span>
                </button>
            </div>
        @endif
    @else
        {{-- Desktop Nav: Guest --}}
        <nav class="main-nav-pill-group guest-main-nav desktop-nav" aria-label="Navigasi Utama">
            <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}"><x-icon name="package" /> <span>Semua Kursus</span></a>
            <a href="{{ route('mentors.index') }}" class="{{ request()->routeIs('mentors.index') ? 'active' : '' }}"><x-icon name="mentor" /> <span>Mentor Terpilih</span></a>
            <a href="{{ route('home') }}#categories"><x-icon name="spark" /> <span>Kategori</span></a>
            <a href="{{ route('how-it-works') }}" class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}"><x-icon name="path" /> <span>Cara Kerja</span></a>
            <a href="{{ route('home') }}#features"><x-icon name="shield-check" /> <span>Keunggulan</span></a>
        </nav>
        {{-- Desktop Actions: Guest --}}
        <div class="header-actions guest-header-actions desktop-actions">
            <a class="btn btn-ghost" href="{{ route('login') }}"><x-icon name="login" /> Masuk</a>
            <a class="btn btn-primary btn-parent-register" href="{{ route('register') }}"><x-icon name="spark" /> Daftar Orang Tua</a>
        </div>
        {{-- Mobile Actions Bar: Guest --}}
        <div class="mobile-header-actions">
            <a class="btn btn-sm btn-ghost mobile-login-btn" href="{{ route('login') }}"><x-icon name="login" /> Masuk</a>
            <button class="nav-toggle" type="button" aria-label="Buka Menu Navigasi" aria-expanded="false" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>
    @endauth
</header>

{{-- Mobile Navigation Drawer Backdrop --}}
<div class="mobile-nav-backdrop" id="mobileNavBackdrop" data-nav-backdrop></div>

{{-- Mobile Navigation Drawer --}}
<div class="mobile-nav-drawer" id="mobileNavDrawer" data-nav-drawer>
    <div class="mobile-drawer-header">
        <div class="drawer-brand">
            <img class="brand-logo sm" src="{{ asset('images/skillpath-logo.png') }}" alt="SkillPath">
            <span class="drawer-badge">Navigasi</span>
        </div>
        <button type="button" class="mobile-drawer-close" aria-label="Tutup Menu" data-nav-close>
            <x-icon name="close" />
        </button>
    </div>

    <div class="mobile-drawer-body">
        @auth
            <!-- User Profile Summary Card -->
            <div class="mobile-user-card">
                <div class="mobile-user-avatar">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <span>{{ auth()->user()->initial }}</span>
                    @endif
                </div>
                <div class="mobile-user-details">
                    <div class="mobile-user-name">{{ auth()->user()->name }}</div>
                    <div class="mobile-user-sub">
                        @if(auth()->user()->isParent())
                            <span class="role-chip parent">Orang Tua</span>
                        @elseif(auth()->user()->isMentor())
                            <span class="role-chip mentor">Mentor</span>
                        @else
                            <span class="role-chip admin">Admin</span>
                        @endif
                        <span class="mobile-user-email">{{ Str::limit(auth()->user()->email, 22) }}</span>
                    </div>
                </div>
                <a href="{{ auth()->user()->isParent() ? route('parent.profile') : (auth()->user()->isMentor() ? route('mentor.profile') : route('admin.profile')) }}" class="mobile-profile-link" title="Kelola Profil">
                    <x-icon name="edit" />
                </a>
            </div>

            @if(auth()->user()->isParent())
                <!-- Section: Navigasi Utama Orang Tua -->
                <div class="mobile-nav-section">
                    <span class="mobile-section-title">MENU UTAMA</span>
                    <nav class="mobile-nav-links">
                        <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="dashboard" /></div>
                            <span>Dashboard Utama</span>
                        </a>
                        <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="package" /></div>
                            <span>Semua Kursus Offline</span>
                        </a>
                        <a href="{{ route('parent.my-courses') }}" class="{{ request()->routeIs('parent.my-courses') || request()->routeIs('parent.learn') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="book" /></div>
                            <span>Kursus Aktif Saya</span>
                        </a>
                        <a href="{{ route('parent.schedule') }}" class="{{ request()->routeIs('parent.schedule') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="calendar" /></div>
                            <span>Jadwal Belajar</span>
                        </a>
                        <a href="{{ route('parent.exams') }}" class="{{ request()->routeIs('parent.exams') || request()->routeIs('parent.certificates.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="certificate" /></div>
                            <span>Ujian & Sertifikat</span>
                        </a>
                        <a href="{{ route('parent.children') }}" class="{{ request()->routeIs('parent.children*') || request()->routeIs('parent.learning-path*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="child" /></div>
                            <span>Profil & Minat Anak</span>
                        </a>
                    </nav>
                </div>

                <!-- Section: Pesanan & Transaksi -->
                <div class="mobile-nav-section">
                    <span class="mobile-section-title">PESANAN & TAGIHAN</span>
                    <nav class="mobile-nav-links">
                        <a href="{{ route('parent.cart') }}" class="{{ request()->routeIs('parent.cart') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="cart" /></div>
                            <span>Keranjang Belanja</span>
                            @if(auth()->user()->cartItems()->count())
                                <span class="mobile-link-badge">{{ auth()->user()->cartItems()->count() }} item</span>
                            @endif
                        </a>
                        <a href="{{ route('parent.orders') }}" class="{{ request()->routeIs('parent.orders') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="receipt" /></div>
                            <span>Riwayat Pesanan</span>
                        </a>
                        <a href="{{ route('parent.payment') }}" class="{{ request()->routeIs('parent.payment*') || request()->routeIs('parent.transactions*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="payment" /></div>
                            <span>Pembayaran & Tagihan</span>
                            @if(auth()->user()->pendingTransactionsCount() > 0)
                                <span class="mobile-link-badge warning pulse">{{ auth()->user()->pendingTransactionsCount() }} belum bayar</span>
                            @endif
                        </a>
                        <a href="{{ route('parent.wishlist') }}" class="{{ request()->routeIs('parent.wishlist') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="heart" /></div>
                            <span>Wishlist Kursus</span>
                        </a>
                    </nav>
                </div>
            @elseif(auth()->user()->isMentor())
                <!-- Section: Navigasi Mentor -->
                <div class="mobile-nav-section">
                    <span class="mobile-section-title">MENU MENTOR</span>
                    <nav class="mobile-nav-links">
                        <a href="{{ route('mentor.dashboard') }}" class="{{ request()->routeIs('mentor.dashboard') || request()->routeIs('mentor.students.show') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="dashboard" /></div>
                            <span>Dashboard Mentor</span>
                        </a>
                        <a href="{{ route('mentor.schedules.index') }}" class="{{ request()->routeIs('mentor.schedules.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="calendar" /></div>
                            <span>Kelola Jadwal Kursus</span>
                        </a>
                        <a href="{{ route('mentor.earnings') }}" class="{{ request()->routeIs('mentor.earnings') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="wallet" /></div>
                            <span>Pendapatan & Komisi</span>
                        </a>
                        <a href="{{ route('mentor.reviews') }}" class="{{ request()->routeIs('mentor.reviews') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="review" /></div>
                            <span>Ulasan & Masukan Siswa</span>
                        </a>
                        <a href="{{ route('mentor.reschedules.index') }}" class="{{ request()->routeIs('mentor.reschedules.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="bell" /></div>
                            <span>Permintaan Jadwal Ulang</span>
                            @if(auth()->user()->unreadRescheduleRequestsCount() > 0)
                                <span class="mobile-link-badge warning pulse">{{ auth()->user()->unreadRescheduleRequestsCount() }} baru</span>
                            @endif
                        </a>
                        <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="package" /></div>
                            <span>Katalog Kursus</span>
                        </a>
                    </nav>
                </div>
            @else
                <!-- Section: Navigasi Admin -->
                <div class="mobile-nav-section">
                    <span class="mobile-section-title">ADMINISTRASI PLATFORM</span>
                    <nav class="mobile-nav-links">
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="dashboard" /></div>
                            <span>Dashboard Admin</span>
                        </a>
                        <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="package" /></div>
                            <span>Kelola Kursus</span>
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="receipt" /></div>
                            <span>Kelola Pesanan</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="users" /></div>
                            <span>Kelola Pengguna</span>
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="analytics" /></div>
                            <span>Statistik Platform</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <div class="nav-icon-wrap"><x-icon name="report" /></div>
                            <span>Laporan Pendapatan</span>
                        </a>
                        <a href="{{ route('home') }}">
                            <div class="nav-icon-wrap"><x-icon name="external-link" /></div>
                            <span>Lihat Website Publik</span>
                        </a>
                    </nav>
                </div>
            @endif

            <!-- Logout in Mobile Drawer -->
            <div class="mobile-drawer-footer">
                <form method="POST" action="{{ route('logout') }}" class="mobile-logout-form">
                    @csrf
                    <button type="submit" class="btn btn-mobile-logout">
                        <x-icon name="logout" />
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        @else
            <!-- Guest Exploration Links -->
            <div class="mobile-nav-section">
                <span class="mobile-section-title">EKSPLORASI SKILLPATH</span>
                <nav class="mobile-nav-links">
                    <a href="{{ route('explore.index') }}" class="{{ request()->routeIs('explore.index') || request()->routeIs('courses.show') ? 'active' : '' }}">
                        <div class="nav-icon-wrap"><x-icon name="package" /></div>
                        <span>Semua Kursus Offline</span>
                    </a>
                    <a href="{{ route('mentors.index') }}" class="{{ request()->routeIs('mentors.index') ? 'active' : '' }}">
                        <div class="nav-icon-wrap"><x-icon name="mentor" /></div>
                        <span>Mentor Terpilih</span>
                    </a>
                    <a href="{{ route('home') }}#categories">
                        <div class="nav-icon-wrap"><x-icon name="spark" /></div>
                        <span>Kategori Bakat</span>
                    </a>
                    <a href="{{ route('how-it-works') }}" class="{{ request()->routeIs('how-it-works') ? 'active' : '' }}">
                        <div class="nav-icon-wrap"><x-icon name="path" /></div>
                        <span>Cara Kerja</span>
                    </a>
                    <a href="{{ route('home') }}#features">
                        <div class="nav-icon-wrap"><x-icon name="shield-check" /></div>
                        <span>Keunggulan SkillPath</span>
                    </a>
                </nav>
            </div>

            <!-- Guest Action CTA in Drawer -->
            <div class="mobile-drawer-footer guest-footer">
                <a class="btn btn-soft btn-block" href="{{ route('login') }}">
                    <x-icon name="login" /> Masuk ke Akun
                </a>
                <a class="btn btn-primary btn-block btn-parent-register" href="{{ route('register') }}">
                    <x-icon name="spark" /> Daftar Akun Orang Tua
                </a>
            </div>
        @endauth
        <div class="mobile-drawer-bottom-info">
            <span>SkillPath • Kursus Offline Non-Akademik Anak</span>
        </div>
    </div>
</div>

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
<script src="{{ asset('js/avatar-cropper.js') }}"></script>
@stack('scripts')
</body>
</html>
