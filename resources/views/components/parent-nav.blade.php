@php($cartCount = auth()->check() ? auth()->user()->cartItems()->count() : 0)
<nav class="parent-nav">
    <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"><x-icon name="sessions" /> Dashboard</a>
    <a href="{{ route('parent.my-courses') }}" class="{{ request()->routeIs('parent.my-courses') ? 'active' : '' }}"><x-icon name="book" /> Course Saya</a>
    <a href="{{ route('parent.cart') }}" class="{{ request()->routeIs('parent.cart') ? 'active' : '' }}"><x-icon name="cart" /> Keranjang @if($cartCount)<i class="nav-badge">{{ $cartCount }}</i>@endif</a>
    <a href="{{ route('parent.wishlist') }}" class="{{ request()->routeIs('parent.wishlist') ? 'active' : '' }}"><x-icon name="heart" /> Wishlist</a>
    <a href="{{ route('parent.orders') }}" class="{{ request()->routeIs('parent.orders') ? 'active' : '' }}"><x-icon name="receipt" /> Riwayat Pesanan</a>
    <a href="{{ route('parent.exams') }}" class="{{ request()->routeIs('parent.exams') ? 'active' : '' }}"><x-icon name="certificate" /> Ujian & Sertifikat</a>
    <a href="{{ route('parent.credits') }}" class="{{ request()->routeIs('parent.credits') ? 'active' : '' }}"><x-icon name="credit" /> Kredit Sesi</a>
</nav>
