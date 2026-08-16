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
            <div class="admin-mini-kpi" style="padding:10px 18px; border-radius:12px; background:#fff; display:flex; flex-direction:column; gap:4px; box-shadow:0 2px 10px rgba(0,0,0,0.03);">
                <span style="font-size:10px; font-weight:600; color:#8a84ab; letter-spacing:0.5px;">RATA-RATA PENILAIAN</span>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="display:inline-flex; align-items:center; gap:4px; font-size:15px; font-weight:600; color:#d97706;" title="Rating Mentor">
                        <x-icon name="star" style="width:16px; height:16px; color:#f59e0b;" />
                        <span>{{ $avgMentor }}</span>
                        <small style="font-size:10px; color:#9ca3af; font-weight:700;">Mentor</small>
                    </span>
                    <span style="color:#e5e7eb;">|</span>
                    <span style="display:inline-flex; align-items:center; gap:4px; font-size:15px; font-weight:600; color:#2563eb;" title="Rating Platform">
                        <x-icon name="star" style="width:16px; height:16px; color:#3b82f6;" />
                        <span>{{ $avgPlatform }}</span>
                        <small style="font-size:10px; color:#9ca3af; font-weight:700;">Platform</small>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Rating -->
    <div class="admin-card" style="padding: 14px 18px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.reviews.index', ['rating' => 'all']) }}" class="admin-filter-tab {{ $currentRating === 'all' ? 'active' : '' }}">
                    Semua Ulasan ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '5']) }}" class="admin-filter-tab {{ $currentRating === '5' ? 'active' : '' }}">
                    <x-icon name="star" style="width:14px; height:14px; color:#f59e0b;" /> 5 Bintang
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '4']) }}" class="admin-filter-tab {{ $currentRating === '4' ? 'active' : '' }}">
                    <x-icon name="star" style="width:14px; height:14px; color:#f59e0b;" /> 4 Bintang
                </a>
                <a href="{{ route('admin.reviews.index', ['rating' => '3']) }}" class="admin-filter-tab {{ $currentRating === '3' ? 'active' : '' }}">
                    <x-icon name="star" style="width:14px; height:14px; color:#f59e0b;" /> 3 Bintang
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
                        <th>Catatan Ulasan</th>
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
                                <div style="display:inline-flex; align-items:center; gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" style="width:13px; height:13px; color:{{ $i <= $rev->mentor_rating ? '#f59e0b' : '#e2e8f0' }};" />
                                    @endfor
                                    <span style="font-weight:600; font-size:12px; color:#b45309; margin-left:4px;">{{ $rev->mentor_rating }}.0</span>
                                </div>
                            </td>
                            <td>
                                <div style="display:inline-flex; align-items:center; gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-icon name="star" style="width:13px; height:13px; color:{{ $i <= $rev->platform_rating ? '#3b82f6' : '#e2e8f0' }};" />
                                    @endfor
                                    <span style="font-weight:600; font-size:12px; color:#1d4ed8; margin-left:4px;">{{ $rev->platform_rating }}.0</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:11.5px; color:#4a446a; max-width:320px; line-height:1.45;">
                                    @if($rev->mentor_review)
                                        <p style="margin:0 0 3px;"><b>Mentor:</b> "{{ $rev->mentor_review }}"</p>
                                    @endif
                                    @if($rev->platform_review)
                                        <p style="margin:0;"><b>Platform:</b> "{{ $rev->platform_review }}"</p>
                                    @endif
                                    @if(!$rev->mentor_review && !$rev->platform_review)
                                        <span style="color:#9ca3af; font-style:italic;">Tidak ada catatan tertulis.</span>
                                    @endif
                                </div>
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
