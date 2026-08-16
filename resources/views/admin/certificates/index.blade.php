@extends('layouts.admin')
@section('title', 'Sertifikat Kelulusan')

@section('content')
<section class="admin-certificates-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">PRESTASI & KELULUSAN</span>
            <h1>Buku Besar Sertifikat</h1>
            <p>Daftar seluruh sertifikat resmi kelulusan siswa yang diterbitkan SkillPath berserta kode verifikasi unik.</p>
        </div>
        <div class="admin-action-group">
            <div class="admin-mini-kpi" style="padding:10px 18px; border-radius:12px; background:#fff;">
                <span style="font-size:10px; font-weight:900; color:#8a84ab;">TOTAL SERTIFIKAT TERBIT</span>
                <b style="font-size:18px; color:#166534;">{{ $totalCount }} Sertifikat</b>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <form method="GET" action="{{ route('admin.certificates.index') }}" class="admin-search-input" style="width:100%; max-width:420px;">
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor sertifikat atau nama siswa...">
                @if($search)
                    <a href="{{ route('admin.certificates.index') }}" style="color:#8a84ab; font-size:11px; font-weight:800;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Certificates Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nomor Sertifikat</th>
                        <th>Siswa Lulus</th>
                        <th>Course & Mentor</th>
                        <th>Nilai Ujian</th>
                        <th>Tanggal Terbit</th>
                        <th style="text-align:right;">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                        <tr>
                            <td>
                                <b style="color:#5b36f5; font-family:monospace; font-size:13px;">{{ $cert->certificate_no ?? $cert->certificate_number }}</b>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="admin-avatar" style="width:32px; height:32px;">
                                        @if(optional($cert->enrollment->child)->avatar_url)
                                            <img src="{{ $cert->enrollment->child->avatar_url }}" alt="{{ $cert->enrollment->child->name }}">
                                        @else
                                            {{ optional($cert->enrollment->child)->initial ?: 'S' }}
                                        @endif
                                    </div>
                                    <b style="color:#120e2e;">{{ optional($cert->enrollment->child)->name ?? '-' }}</b>
                                </div>
                            </td>
                            <td>
                                <b style="color:#120e2e; display:block;">{{ optional($cert->enrollment->course)->title ?? '-' }}</b>
                                <small style="color:#8a84ab;">Mentor: {{ optional(optional($cert->enrollment->course)->instructor)->name ?? 'SkillPath' }}</small>
                            </td>
                            <td>
                                <span class="status-pill paid" style="font-size:11px;">
                                    Skor: {{ optional($cert->enrollment->examAttempt)->score ?? 100 }}/100
                                </span>
                            </td>
                            <td>
                                <span style="font-size:11.5px; color:#8a84ab;">{{ $cert->created_at->format('d M Y') }}</span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.certificates.show', $cert) }}" target="_blank" class="btn btn-sm btn-soft">
                                    <x-icon name="eye" />
                                    <span>Lihat Sertifikat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#8a84ab; padding:32px;">
                                Belum ada sertifikat yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certificates->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
