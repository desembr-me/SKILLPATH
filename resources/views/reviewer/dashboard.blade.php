@extends('layouts.app')

@section('title', 'Dashboard Reviewer | SKILLPATH')

@section('content')
<section class="container section-block">
    <div class="section-heading dashboard-heading">
        <span class="eyebrow">Dashboard Reviewer</span>
        <h1>Review website dan konten kursus</h1>
        <p>Pantau ulasan yang masuk dan moderasi agar tampil sesuai standar platform.</p>
    </div>

    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-top: 0;">
        <article class="stat-card">
            <span>Total Ulasan</span>
            <strong>{{ $stats['total_reviews'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Disetujui</span>
            <strong>{{ $stats['approved_reviews'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Menunggu</span>
            <strong>{{ $stats['pending_reviews'] }}</strong>
        </article>
        <article class="stat-card">
            <span>Course</span>
            <strong>{{ $stats['courses'] }}</strong>
        </article>
    </div>

    <div class="section-heading">
        <h2>Ulasan terbaru</h2>
    </div>

    <div class="review-list">
        @forelse($reviews as $review)
            <article class="review-card">
                <div class="review-header">
                    <div>
                        <strong>{{ $review->user->name }}</strong>
                        <small>untuk {{ $review->learningPath->title }}</small>
                    </div>
                    <span class="status-badge {{ $review->is_approved ? 'published' : 'pending' }}">
                        {{ $review->is_approved ? 'TAMPIL' : 'MENUNGGU' }}
                    </span>
                </div>
                <div class="stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                <p>{{ $review->review ?: 'Tidak ada komentar tertulis.' }}</p>
                <div class="review-footer">
                    <small>{{ $review->created_at->format('d M Y H:i') }}</small>
                    <form method="POST" action="{{ route('reviewer.reviews.toggle-approve', $review) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn {{ $review->is_approved ? 'btn-ghost' : 'btn-dark' }}" type="submit">
                            {{ $review->is_approved ? 'Tutup tampilan' : 'Setujui' }}
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty-state">Belum ada ulasan yang masuk.</div>
        @endforelse
    </div>

    <div class="mt-4">
        <a class="btn btn-dark" href="{{ route('reviewer.reviews.index') }}">Lihat semua review</a>
    </div>
</section>
@endsection
