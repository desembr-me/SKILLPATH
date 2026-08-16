<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Dashboard Admin') | SkillPath Administration</title>
    <meta name="description" content="SkillPath Admin Panel - Pusat kendali manajemen platform edukasi offline anak.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
    <link rel="icon" href="{{ asset('images/skillpath-logo.png') }}" type="image/png">
</head>
<body class="admin-body">
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <div class="admin-brand-icon">S</div>
            <div class="admin-brand-text">
                <h2>SkillPath</h2>
                <span>ADMINISTRATION</span>
            </div>
        </a>

        <!-- 1. UTAMA & ANALITIK -->
        <div class="admin-sidebar-section" data-section-id="utama">
            <button type="button" class="admin-sidebar-kicker-btn" aria-expanded="true" title="Sembunyikan / Tampilkan Menu Utama">
                <span>UTAMA & ANALITIK</span>
                <svg class="admin-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="admin-sidebar-collapse">
                <ul class="admin-nav">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <x-icon name="dashboard" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <x-icon name="analytics" />
                            <span>Statistik Platform</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <x-icon name="report" />
                            <span>Laporan Pendapatan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 2. AKADEMIK & KURSUS -->
        <div class="admin-sidebar-section" data-section-id="akademik">
            <button type="button" class="admin-sidebar-kicker-btn" aria-expanded="true" title="Sembunyikan / Tampilkan Akademik & Kursus">
                <span>AKADEMIK & KURSUS</span>
                <svg class="admin-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="admin-sidebar-collapse">
                <ul class="admin-nav">
                    <li>
                        <a href="{{ route('admin.courses.index') }}" class="{{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <x-icon name="package" />
                            <span>Manajemen Course</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.schedules.index') }}" class="{{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                            <x-icon name="calendar" />
                            <span>Jadwal Pengajar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <x-icon name="progress" />
                            <span>Progress Siswa</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.certificates.index') }}" class="{{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                            <x-icon name="certificate" />
                            <span>Sertifikat</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 3. OPERASIONAL & PENGGUNA -->
        <div class="admin-sidebar-section" data-section-id="operasional">
            <button type="button" class="admin-sidebar-kicker-btn" aria-expanded="true" title="Sembunyikan / Tampilkan Operasional & Pengguna">
                <span>OPERASIONAL & PENGGUNA</span>
                <svg class="admin-sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="admin-sidebar-collapse">
                <ul class="admin-nav">
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <x-icon name="users" />
                            <span>Manajemen Pengguna</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <x-icon name="receipt" />
                            <span>Manajemen Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <x-icon name="review" />
                            <span>Manajemen Ulasan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.recycle-bin.index') }}" class="{{ request()->routeIs('admin.recycle-bin.*') ? 'active' : '' }}">
                            <x-icon name="recycle-bin" />
                            <span>Recycle Bin</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="admin-sidebar-footer">
            <a class="admin-back-link" href="{{ route('home') }}">
                <x-icon name="arrow-left" />
                <span>Kembali ke Website</span>
            </a>

            <a class="admin-user-card" href="{{ route('admin.profile') }}" title="Edit profil admin">
                <div class="admin-avatar">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ auth()->user()->initial }}
                    @endif
                </div>
                <div class="admin-user-info">
                    <b>{{ auth()->user()->name }}</b>
                    <small>{{ auth()->user()->email }}</small>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="admin-logout-btn">
                    <x-icon name="logout" />
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main View Wrapper -->
    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <h3>SkillPath Admin</h3>
                <p>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="admin-topbar-right">
                <span class="admin-status-badge">
                    <span class="pulse-dot"></span>
                    Sistem aktif
                </span>
            </div>
        </header>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="flash success" style="top:80px;">
                <x-icon name="check" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="flash error" style="top:80px;">
                <x-icon name="conflict" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="flash error" style="top:80px;">
                <strong>Ada data yang perlu diperiksa:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content -->
        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('.admin-sidebar-section[data-section-id]');
    sections.forEach(function (sec) {
        const id = sec.getAttribute('data-section-id');
        const btn = sec.querySelector('.admin-sidebar-kicker-btn');
        const hasActive = sec.querySelector('.admin-nav a.active') !== null;
        
        // Restore saved collapsed state
        if (id) {
            const isSavedCollapsed = localStorage.getItem('sp_admin_sec_' + id) === 'collapsed';
            if (isSavedCollapsed && !hasActive) {
                sec.classList.add('collapsed');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

        if (btn) {
            btn.addEventListener('click', function () {
                const isNowCollapsed = sec.classList.toggle('collapsed');
                btn.setAttribute('aria-expanded', !isNowCollapsed);
                if (id) {
                    localStorage.setItem('sp_admin_sec_' + id, isNowCollapsed ? 'collapsed' : 'expanded');
                }
            });
        }
    });
});
</script>
</body>
</html>
