@extends('layouts.admin')
@section('title', 'Manajemen Course')

@section('content')
<section class="admin-courses-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">KATALOG BELAJAR</span>
            <h1>Manajemen Course</h1>
            <p>Kelola seluruh program belajar offline, mentor pengampu, jadwal sesi, dan status publikasi.</p>
        </div>
        <div class="admin-action-group">
            <a href="{{ route('admin.courses.create') }}" class="btn-admin-primary">
                <x-icon name="plus" />
                <span>Tambah Course Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.courses.index', ['status' => 'all', 'category_id' => $currentCategory, 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'all' ? 'active' : '' }}">
                    Semua ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.courses.index', ['status' => 'active', 'category_id' => $currentCategory, 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'active' ? 'active' : '' }}">
                    Aktif ({{ $activeCount }})
                </a>
                <a href="{{ route('admin.courses.index', ['status' => 'draft', 'category_id' => $currentCategory, 'search' => $search]) }}" class="admin-filter-tab {{ $currentStatus === 'draft' ? 'active' : '' }}">
                    Draft ({{ $draftCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.courses.index') }}" class="admin-search-input">
                @if($currentStatus !== 'all')
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul, kota, materi...">
                @if($search)
                    <a href="{{ route('admin.courses.index', ['status' => $currentStatus]) }}" style="color:#8a84ab; font-size:11px; font-weight:800;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Kategori</th>
                        <th>Pengajar</th>
                        <th>Biaya & Sesi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $c)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, {{ $c->accent_color ?: '#6857df' }}, #fff); display:grid; place-items:center; flex-shrink:0; overflow:hidden; border:1px solid #eef0f8;">
                                        @if($c->cover_image)
                                            <img src="{{ asset('storage/'.$c->cover_image) }}" alt="{{ $c->title }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <x-icon name="{{ $c->icon_name ?: 'book' }}" style="width:22px; height:22px; color:#4b4567;" />
                                        @endif
                                    </div>
                                    <div>
                                        <b style="color:#120e2e; display:block; font-size:13.5px;">{{ $c->title }}</b>
                                        <small style="color:#8a84ab;">Usia {{ $c->age_min }}-{{ $c->age_max }} tahun</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:700; font-size:12px; color:#5c567e;">{{ $c->category->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="font-weight:700; font-size:12.5px; color:#120e2e;">{{ $c->instructor->name ?? 'SkillPath' }}</span>
                            </td>
                            <td>
                                <b style="color:#120e2e;">Rp{{ number_format($c->price, 0, ',', '.') }}</b>
                                <small style="color:#8a84ab; display:block;">{{ $c->sessions_count }} sesi ({{ $c->duration_minutes }} mnt)</small>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#5c567e;">{{ $c->city }}</span>
                                <small style="color:#8a84ab; display:block;">{{ $c->location_name }}</small>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.courses.toggle-status', $c) }}" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="status-pill {{ $c->status === 'active' ? 'paid' : 'pending' }}" style="border:none; cursor:pointer;" title="Klik untuk ubah status">
                                        {{ $c->status === 'active' ? 'Aktif' : 'Draft' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; align-items:center; gap:8px;">
                                    <a href="{{ route('courses.show', $c->slug) }}" target="_blank" class="btn btn-sm btn-ghost" title="Lihat Halaman Publik">
                                        <x-icon name="external-link" />
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $c) }}" class="btn btn-sm btn-soft">
                                        <x-icon name="edit" />
                                        <span>Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $c) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus course ini?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--danger);" title="Hapus">
                                            <x-icon name="trash" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada data course yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
