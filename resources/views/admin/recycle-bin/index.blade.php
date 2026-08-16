@extends('layouts.admin')
@section('title', 'Recycle Bin')

@section('content')
<section class="admin-recycle-bin-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">ARSIP & PEMULIHAN</span>
            <h1>Recycle Bin & Data Terhapus</h1>
            <p>Kelola berkas, program belajar nonaktif, atau data yang diarsipkan dengan opsi pemulihan (*restore*) cepat.</p>
        </div>
        <div class="admin-action-group">
            <form method="POST" action="{{ route('admin.recycle-bin.empty') }}" onsubmit="return confirm('Kosongkan recycle bin? Tindakan ini permanen.');">
                @csrf
                <button type="submit" class="btn-admin-white" style="color:var(--danger);">
                    <x-icon name="trash" />
                    <span>Kosongkan Recycle Bin</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Panel Inactive/Draft Items -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div class="admin-panel-head" style="padding: 20px 24px; margin-bottom:0; border-bottom:1px solid #eaebf4;">
            <div>
                <span class="kicker">ITEM TERSIMPAN</span>
                <h2>Course Nonaktif & Draft ({{ $draftCourses->count() }})</h2>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th>Pengajar</th>
                        <th>Status Arsip</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($draftCourses as $crs)
                        <tr>
                            <td>
                                <b style="color:#120e2e;">{{ $crs->title }}</b>
                                <small style="color:#8a84ab; display:block;">{{ $crs->city }} • {{ $crs->location_name }}</small>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#5c567e;">{{ $crs->category->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#120e2e;">{{ $crs->instructor->name ?? 'SkillPath' }}</span>
                            </td>
                            <td>
                                <span class="status-pill pending">
                                    Diarsipkan (Draft)
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.recycle-bin.restore-course', $crs) }}" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-soft">
                                        <x-icon name="restore" />
                                        <span>Pulihkan (Aktifkan)</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:#8a84ab; padding:32px;">
                                Recycle bin kosong. Tidak ada data yang diarsipkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
