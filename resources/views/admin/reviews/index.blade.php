@extends('admin.layouts.app')

@section('title', 'Review | Admin SKILLPATH')
@section('page-title', 'Review')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Moderasi</span>
            <h2>Review mentor & platform</h2>
            <p>Nilai mentor dan pengalaman platform dipisahkan agar sumber masalah dapat diidentifikasi.</p>
            <small>Seluruh masukan · Mentor: {{ number_format($stats['mentor_average'],2) }} · Platform: {{ number_format($stats['platform_average'],2) }} · Isu mentor: {{ $stats['mentor_issues'] }} · Isu platform: {{ $stats['platform_issues'] }} · Menunggu moderasi: {{ $stats['pending'] }}</small>
        </div>
        <span class="admin-total-pill">{{ $reviews->total() }} review</span>
    </div>

    <form class="admin-filter-bar compact" method="GET">
        <select name="status">
            <option value="">Semua status</option>
            <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
            <option value="pending" @selected(request('status') === 'pending')>Disembunyikan</option>
        </select>
        <select name="source">
            <option value="">Semua sumber masalah</option>
            <option value="mentor" @selected(request('source') === 'mentor')>Mentor</option>
            <option value="platform" @selected(request('source') === 'platform')>Platform</option>
            <option value="both" @selected(request('source') === 'both')>Mentor & Platform</option>
            <option value="none" @selected(request('source') === 'none')>Tidak ada indikasi</option>
        </select>
        <button class="admin-btn dark" type="submit">Terapkan</button>
        <a class="admin-text-link" href="{{ route('admin.reviews.index') }}">Reset</a>
    </form>

    <div class="admin-review-list">
        @forelse($reviews as $review)
            <article class="admin-review-card">
                <div class="admin-review-top">
                    <div>
                        <strong>{{ $review->user->name }}</strong>
                        <span>untuk {{ $review->learningPath->title }}</span>
                    </div>
                    <span class="status-badge {{ $review->is_approved ? 'published' : 'pending' }}">{{ $review->is_approved ? 'TAMPIL' : 'DISEMBUNYIKAN' }}</span>
                </div>

                <div class="admin-stars">Mentor: {{ str_repeat('★', $review->mentor_rating ?? $review->rating) }}{{ str_repeat('☆', 5 - ($review->mentor_rating ?? $review->rating)) }}</div>
                <p>{{ $review->mentor_review ?: $review->review ?: 'Tidak ada komentar untuk mentor.' }}</p>

                <div class="admin-stars">Platform: {{ str_repeat('★', $review->platform_rating ?? $review->rating) }}{{ str_repeat('☆', 5 - ($review->platform_rating ?? $review->rating)) }}</div>
                <p>{{ $review->platform_review ?: 'Tidak ada komentar untuk platform.' }}</p>
                <small>Indikasi sumber masalah: <strong>{{ $review->problemSource() }}</strong></small>

                <div class="admin-review-footer">
                    <small>{{ $review->created_at->format('d M Y H:i') }}</small>
                    <div class="admin-action-row">
                        <form method="POST" action="{{ route('admin.reviews.toggle-approve', $review) }}">
                            @csrf
                            @method('PATCH')
                            <button class="admin-btn {{ $review->is_approved ? 'outline' : 'primary' }}" type="submit">{{ $review->is_approved ? 'Sembunyikan' : 'Setujui Review' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Pindahkan review ini ke Recycle Bin?')">
                            @csrf
                            @method('DELETE')
                            <button class="admin-btn danger" type="submit">Hapus</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="admin-empty-state">Belum ada review.</div>
        @endforelse
    </div>
    <div class="admin-pagination">{{ $reviews->links() }}</div>
</section>
@endsection
