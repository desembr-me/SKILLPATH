@extends('layouts.app')
@section('title','Ulasan Orang Tua')
@section('content')
<section class="dashboard-page">
    <div class="dash-title">
        <div>
            <span class="eyebrow">Ulasan</span>
            <h1>Riwayat Ulasan Orang Tua</h1>
            <p>Rangkuman penilaian dari orang tua terhadap kelas yang kamu ajar.</p>
        </div>
        <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="rating-split">
        <article>
            <span class="rating-label">Rata-rata rating mentor</span>
            <b>{{ $avgMentorRating ?: '0.0' }}</b>
            <div class="rating-stars">@for($i=1;$i<=5;$i++)<x-icon name="star" />@endfor</div>
            <p>Dari {{ $reviews->count() }} ulasan orang tua</p>
        </article>
        <article>
            <span class="rating-label">Rata-rata rating platform</span>
            <b>{{ $avgPlatformRating ?: '0.0' }}</b>
            <div class="rating-stars">@for($i=1;$i<=5;$i++)<x-icon name="star" />@endfor</div>
            <p>Penilaian pengalaman booking & jadwal</p>
        </article>
    </div>

    <div class="panel review-panel">
        <div class="panel-heading"><div><span class="panel-kicker">Detail</span><h2>Semua Ulasan</h2></div></div>
        @forelse($reviews as $review)
            <div class="review-form">
                <div class="review-form-head">
                    <b>{{ $review->course->title }}</b>
                    <small>{{ $review->parent->name }} • {{ $review->enrollment->child->name ?? '' }} • {{ $review->created_at->format('d M Y') }}</small>
                </div>
                <div class="review-form-grid">
                    <div><span class="rating-label">Rating mentor</span><div class="rating-stars">@for($i=1;$i<=$review->mentor_rating;$i++)<x-icon name="star" />@endfor</div>@if($review->mentor_review)<p class="mentor-note">{{ $review->mentor_review }}</p>@endif</div>
                    <div><span class="rating-label">Rating platform</span><div class="rating-stars">@for($i=1;$i<=$review->platform_rating;$i++)<x-icon name="star" />@endfor</div>@if($review->platform_review)<p class="mentor-note">{{ $review->platform_review }}</p>@endif</div>
                </div>
            </div>
        @empty
            <div class="empty-state compact-empty"><x-icon name="review" /><div><b>Belum ada ulasan</b><span>Ulasan akan muncul setelah orang tua menilai course kamu.</span></div></div>
        @endforelse
    </div>
</section>
@endsection
