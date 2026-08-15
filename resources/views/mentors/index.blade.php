@extends('layouts.app')
@section('title','Daftar Mentor')
@section('content')
<section class="page-hero">
    <div><span class="eyebrow">Pengajar SkillPath</span><h1>Kenali mentor di balik setiap course.</h1><p>Setiap mentor fokus mengajar satu kategori, dipilih sesuai keahlian dan pengalamannya mendampingi anak.</p></div>
    <div class="page-hero-art"><x-icon name="users" /><span></span><span></span></div>
</section>
<section class="section compact">
    <div class="mentor-grid">
        @forelse($mentors as $mentor)
            <x-mentor-flip-card :mentor="$mentor" />
        @empty
            <div class="empty-state wide-empty"><x-icon name="users" /><div><b>Belum ada mentor terdaftar</b><span>Daftar mentor akan muncul di sini.</span></div></div>
        @endforelse
    </div>
</section>
@endsection
