@extends('layouts.admin')
@section('title', 'Manajemen Ulasan')

@section('content')
<section class="admin-reviews-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">QUALITY & FEEDBACK</span>
            <h1>Manajemen Ulasan & Kepuasan</h1>
            <p>Moderasi ulasan dan evaluasi dari orang tua mengenai pengalaman belajar bersama mentor serta sistem platform.</p>
        </div>
        <div class="admin-action-group">
            <div class="admin-mini-kpi" style="padding:10px 18px; border-radius:12px; background:#fff;">
                <span style="font-size:10px; font-weight:900; color:#8a84ab;">RATING MENTOR / PLATFORM</span>
                <b style="font-size:18px; color:#5b36f5;">★ {{ $avgMentor }} / {{ $avgPlatform }}</b>
            </div>
        </div>
    </div>

    <!-- Filter Rating -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.reviews.index', ['rating' => 'all']) }}" class="admin-filter-tab {{ $currentRating === 'all' ? 'active' : '' }}">
                    Semua Ulasan ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '5']) }}" class="admin-filter-tab {{ $currentRating === '5' ? 'active' : '' }}">
                    <x-icon name="star" style="width:13px; height:13px; fill:#f59e0b; stroke:#f59e0b;" /> 5 Bintang
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '4']) }}" class="admin-filter-tab {{ $currentRating === '4' ? 'active' : '' }}">
                    <x-icon name="star" style="width:13px; height:13px; fill:#f59e0b; stroke:#f59e0b;" /> 4 Bintang
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '3']) }}" class="admin-filter-tab {{ $currentRating === '3' ? 'active' : '' }}">
                    <x-icon name="star" style="width:13px; height:13px; fill:#f59e0b; stroke:#f59e0b;" /> 3 Bintang
                </a>
            </div>
        </div>
    </div>

    <!-- Reviews Grid / Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Orang Tua & Siswa</th>
                        <th>Course & Mentor</th>
                        <th>Rating Mentor</th>
                        <th>Rating Platform</th>
                        <th>Ulasan / Feedback</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ $rev->parent->name ?? 'Orang Tua' }}</b>
                                <small style="color:#8a84ab;">Anak: {{ optional(optional($rev->enrollment)->child)->name ?? '-' }}</small>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ optional(optional($rev->enrollment)->course)->title ?? 'Course Belajar' }}</b>
                                <small style="color:#8a84ab;">Mentor: {{ optional(optional(optional($rev->enrollment)->course)->instructor)->name ?? 'SkillPath' }}</small>
                            </td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-weight:800; color:#d97706;">
                                    <x-icon name="star" style="width:14px; height:14px; fill:#f59e0b; stroke:#f59e0b;" />
                                    <span>{{ $rev->mentor_rating }}.0</span>
                                </span>
                            </td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-weight:800; color:#2563eb;">
                                    <x-icon name="star" style="width:14px; height:14px; fill:#3b82f6; stroke:#3b82f6;" />
                                    <span>{{ $rev->platform_rating }}.0</span>
                                </span>
                            </td>
                            <td>
                                <p style="margin:0; font-size:12px; color:#4a446a; max-width:320px; line-height:1.5;">
                                    "{{ $rev->comment ?: 'Tidak ada catatan tambahan.' }}"
                                </p>
                            </td>
                            <td>
                                <span style="font-size:11.5px; color:#8a84ab;">{{ $rev->created_at->format('d M Y') }}</span>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.reviews.destroy', $rev) }}" onsubmit="return confirm('Hapus ulasan ini?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--danger);" title="Hapus Ulasan">
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada data ulasan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
