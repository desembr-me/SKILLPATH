@extends('admin.layouts.app')

@section('title', 'Recycle Bin | Admin SKILLPATH')
@section('page-title', 'Recycle Bin')

@section('content')
@php($typeLabels = ['course' => 'COURSE', 'category' => 'KATEGORI', 'user' => 'PENGGUNA', 'review' => 'REVIEW'])
<section class="admin-panel recycle-hero-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Pemulihan data</span>
            <h2>Data yang telah dihapus</h2>
            <p>Data di halaman ini belum hilang permanen. Admin dapat memulihkan atau menghapus permanen data tertentu.</p>
        </div>

        @if($items->isNotEmpty())
            <form method="POST" action="{{ route('admin.recycle-bin.restore-all') }}" onsubmit="return confirm('Pulihkan semua data di Recycle Bin?')">
                @csrf
                @method('PATCH')
                <button class="admin-btn primary" type="submit">Pulihkan Semua</button>
            </form>
        @endif
    </div>

    <div class="recycle-stat-grid">
        <a class="recycle-stat-card {{ $type === 'all' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index') }}">
            <span>Semua</span>
            <strong>{{ array_sum($counts) }}</strong>
        </a>
        <a class="recycle-stat-card {{ $type === 'course' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['type' => 'course']) }}">
            <span>Course</span>
            <strong>{{ $counts['course'] }}</strong>
        </a>
        <a class="recycle-stat-card {{ $type === 'category' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['type' => 'category']) }}">
            <span>Kategori</span>
            <strong>{{ $counts['category'] }}</strong>
        </a>
        <a class="recycle-stat-card {{ $type === 'user' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['type' => 'user']) }}">
            <span>Pengguna</span>
            <strong>{{ $counts['user'] }}</strong>
        </a>
        <a class="recycle-stat-card {{ $type === 'review' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['type' => 'review']) }}">
            <span>Review</span>
            <strong>{{ $counts['review'] }}</strong>
        </a>
    </div>

    <form class="admin-filter-bar recycle-filter" method="GET">
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari data yang dihapus...">
        <select name="type">
            <option value="all" @selected($type === 'all')>Semua jenis</option>
            <option value="course" @selected($type === 'course')>Course</option>
            <option value="category" @selected($type === 'category')>Kategori</option>
            <option value="user" @selected($type === 'user')>Pengguna</option>
            <option value="review" @selected($type === 'review')>Review</option>
        </select>
        <button class="admin-btn dark" type="submit">Terapkan</button>
        <a class="admin-text-link" href="{{ route('admin.recycle-bin.index') }}">Reset</a>
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table recycle-table">
            <thead>
            <tr>
                <th>Jenis</th>
                <th>Data</th>
                <th>Keterangan</th>
                <th>Dihapus pada</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        <span class="recycle-type-badge {{ $item['type'] }}">
                            {{ $typeLabels[$item['type']] }}
                        </span>
                    </td>
                    <td><strong>{{ $item['name'] }}</strong></td>
                    <td class="admin-muted">{{ $item['detail'] }}</td>
                    <td>{{ $item['deleted_at']?->format('d M Y H:i') ?? '-' }}</td>
                    <td>
                        <div class="admin-action-row recycle-actions">
                            <form method="POST" action="{{ route('admin.recycle-bin.restore', ['type' => $item['type'], 'id' => $item['id']]) }}">
                                @csrf
                                @method('PATCH')
                                <button class="admin-icon-btn success" type="submit">Pulihkan</button>
                            </form>

                            <form method="POST" action="{{ route('admin.recycle-bin.force-delete', ['type' => $item['type'], 'id' => $item['id']]) }}" onsubmit="return confirm('Hapus data ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button class="admin-icon-btn danger" type="submit">Hapus Permanen</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin-empty-cell">
                        <div class="recycle-empty-icon">♲</div>
                        Recycle Bin kosong untuk filter ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="recycle-note">
        <strong>Catatan keamanan:</strong>
        Course atau pengguna yang memiliki riwayat transaksi dan enrollment tidak dapat dihapus permanen. Data transaksi harus tetap terjaga.
    </div>
</section>
@endsection
