@extends('layouts.instructor')

@section('title', 'Ulasan Kursus | SKILLPATH')
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Feedback kursus</span>
    <h1>Ulasan siswa untuk course Anda</h1>
    <p>Tinjau komentar dan atur apakah review bisa ditampilkan di halaman course.</p>
</div>

<div class="toolbar-row" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
    <form method="GET" class="inline-form" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <select name="status" class="input-select">
            <option value="">Semua status</option>
            <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
            <option value="pending" @selected(request('status') === 'pending')>Menunggu</option>
        </select>
        <button class="btn btn-dark" type="submit">Terapkan</button>
        <a class="btn btn-ghost" href="{{ route('instructor.reviews.index') }}">Reset</a>
    </form>
</div>

<div class="review-list">
    @forelse($reviews as $review)
        <article class="review-card">
            <div class="review-header">
                <div>
                    <strong>{{ $review->user->name }}</strong>
                    <small>untuk {{ $review->learningPath->title }}</small>
                </div>
                <span class="status-badge {{ $review->is_approved ? 'published' : 'pending' }}">{{ $review->is_approved ? 'TAMPIL' : 'MENUNGGU' }}</span>
            </div>
            <div class="stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
            <p>{{ $review->review ?: 'Tidak ada komentar tertulis.' }}</p>
            <div class="review-footer">
                <small>{{ $review->created_at->format('d M Y H:i') }}</small>
                <form method="POST" action="{{ route('instructor.reviews.toggle-approve', $review) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn {{ $review->is_approved ? 'btn-ghost' : 'btn-dark' }}" type="submit">
                        {{ $review->is_approved ? 'Sembunyikan' : 'Setujui' }}
                    </button>
                </form>
            </div>
        </article>
    @empty
        <div class="empty-state">Belum ada ulasan untuk course Anda.</div>
    @endforelse
</div>

<div class="admin-pagination">{{ $reviews->links() }}</div>
@endsection
