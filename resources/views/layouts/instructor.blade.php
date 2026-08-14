<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffd21a">
    <title>@yield('title', 'Dashboard Pengajar | SKILLPATH')</title>
    <link rel="stylesheet" href="{{ asset('css/skillpath.css') }}">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body>
<div class="instructor-shell">
    <aside class="instructor-sidebar" data-instructor-sidebar>
        <a class="brand instructor-brand" href="{{ route('instructor.dashboard') }}">
            <span class="brand-mark">S</span>
            <span>SKILLPATH<small>Dashboard Pengajar</small></span>
        </a>

        <nav class="instructor-nav">
            <a class="{{ request()->routeIs('instructor.dashboard', 'instructor.courses.*', 'instructor.live.*') ? 'is-active' : '' }}" href="{{ route('instructor.dashboard') }}">
                <iconify-icon class="instructor-nav-icon" icon="mdi:view-dashboard-outline"></iconify-icon> Dashboard
            </a>
            <a class="{{ request()->routeIs('instructor.profile.*') ? 'is-active' : '' }}" href="{{ route('instructor.profile.edit') }}">
                <iconify-icon class="instructor-nav-icon" icon="mdi:account-edit-outline"></iconify-icon> Profil Saya
            </a>
            <a class="{{ request()->routeIs('instructor.progress.*') ? 'is-active' : '' }}" href="{{ route('instructor.progress.index') }}">
                <iconify-icon class="instructor-nav-icon" icon="mdi:chart-line"></iconify-icon> Progres Siswa
            </a>
            <a class="{{ request()->routeIs('instructor.revenue.*') ? 'is-active' : '' }}" href="{{ route('instructor.revenue.index') }}">
                <iconify-icon class="instructor-nav-icon" icon="mdi:cash-multiple"></iconify-icon> Pendapatan
            </a>
        </nav>

        <div class="instructor-sidebar-footer">
            <a href="{{ route('home') }}">← Lihat website</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="instructor-main">
        <header class="instructor-topbar">
            <button class="nav-toggle instructor-toggle" type="button" data-instructor-toggle aria-label="Buka menu"><iconify-icon icon="mdi:menu"></iconify-icon></button>
            <span></span>
            <div class="instructor-topbar-actions">
                @php($unreadNotifications = auth()->user()->unreadNotifications()->count())
                <details class="notif-bell">
                    <summary><iconify-icon icon="mdi:bell-outline"></iconify-icon>@if($unreadNotifications > 0)<span class="notif-badge">{{ $unreadNotifications }}</span>@endif</summary>
                    <div class="notif-panel">
                        @forelse(auth()->user()->notifications()->latest()->take(8)->get() as $notif)
                            <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                                <p>{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
                                <small>{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <div class="notif-empty">Belum ada notifikasi.</div>
                        @endforelse
                        @if($unreadNotifications > 0)
                            <div class="notif-footer">
                                <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="text-button" type="submit">Tandai semua dibaca</button></form>
                            </div>
                        @endif
                    </div>
                </details>
                <a class="instructor-profile" href="{{ route('instructor.profile.edit') }}">
                    <span class="instructor-avatar">
                        @if(auth()->user()->instructorProfile?->photoSrc())
                            <img src="{{ auth()->user()->instructorProfile->photoSrc() }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </span>
                    <strong>{{ auth()->user()->name }}</strong>
                </a>
            </div>
        </header>

        <main class="instructor-content">
            @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif

            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('js/skillpath.js') }}"></script>
</body>
</html>
