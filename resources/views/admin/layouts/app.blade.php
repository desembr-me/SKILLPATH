<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1f2430">
    <title>@yield('title', 'Admin SKILLPATH')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" data-admin-sidebar>
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <span class="admin-brand-mark">S</span>
            <span>
                <strong>SKILLPATH</strong>
                <small>Admin Panel</small>
            </span>
        </a>

        <nav class="admin-nav">
            <span class="admin-nav-label">UTAMA</span>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="admin-nav-icon">▦</span> Dashboard
            </a>
            <a class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                <span class="admin-nav-icon">▤</span> Course
            </a>
            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <span class="admin-nav-icon">◫</span> Kategori
            </a>
            <a class="{{ request()->routeIs('admin.instructors.*') ? 'active' : '' }}" href="{{ route('admin.instructors.index') }}">
                <span class="admin-nav-icon">♙</span> Pengajar
            </a>
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <span class="admin-nav-icon">◉</span> Pengguna
            </a>

            <span class="admin-nav-label">TRANSAKSI & KONTEN</span>
            <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <span class="admin-nav-icon">▣</span> Pesanan
            </a>
            <a class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                <span class="admin-nav-icon">★</span> Review
            </a>
            <a class="{{ request()->routeIs('admin.recycle-bin.*') ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index') }}">
                <span class="admin-nav-icon">♲</span> Recycle Bin
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <a href="{{ route('home') }}">← Lihat website</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-menu-button" type="button" data-admin-toggle aria-label="Buka menu">☰</button>
            <div>
                <span class="admin-page-kicker">SKILLPATH MANAGEMENT</span>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="admin-profile">
                <span class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>Administrator</small>
                </span>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="admin-alert success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="admin-alert error">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('[data-admin-toggle]');
    const sidebar = document.querySelector('[data-admin-sidebar]');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
});
</script>
</body>
</html>
