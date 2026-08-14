@extends('admin.layouts.app')

@section('title', 'Pengajar | Admin SKILLPATH')
@section('page-title', 'Pengajar')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Tenaga pengajar</span>
            <h2>Kelola dan verifikasi pengajar</h2>
            <p>Pastikan profil pengajar memenuhi standar sebelum ditampilkan sebagai terverifikasi.</p>
        </div>
        <span class="admin-total-pill">{{ $instructors->total() }} pengajar</span>
    </div>

    <form class="admin-filter-bar compact" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email pengajar...">
        <button class="admin-btn dark" type="submit">Cari</button>
        <a class="admin-text-link" href="{{ route('admin.instructors.index') }}">Reset</a>
    </form>

    <div class="admin-card-list">
        @forelse($instructors as $instructor)
            @php($profile = $instructor->instructorProfile)
            <article class="admin-person-card">
                <div class="admin-person-avatar admin-person-photo">@if($profile?->photoSrc())<img src="{{ $profile->photoSrc() }}" alt="Foto {{ $instructor->name }}">@else{{ strtoupper(substr($instructor->name, 0, 1)) }}@endif</div>
                <div class="admin-person-main">
                    <div class="admin-person-title">
                        <div>
                            <h3>{{ $instructor->name }}</h3>
                            <p>{{ $profile?->headline ?: 'Pengajar SKILLPATH' }}</p>
                        </div>
                        <span class="status-badge {{ $profile?->is_verified ? 'published' : 'pending' }}">{{ $profile?->is_verified ? 'TERVERIFIKASI' : 'BELUM VERIFIKASI' }}</span>
                    </div>
                    <div class="admin-person-meta">
                        <span>{{ $instructor->email }}</span>
                        <span>{{ $instructor->courses_taught_count }} course</span>
                        <span>{{ $profile?->years_experience ?? 0 }} tahun pengalaman</span>
                        <span>★ {{ number_format((float) ($profile?->rating ?? 0), 1) }}</span>
                    </div>
                </div>
                <div class="admin-person-actions">
                    <form method="POST" action="{{ route('admin.instructors.toggle-verify', $instructor) }}">
                        @csrf
                        @method('PATCH')
                        <button class="admin-btn {{ $profile?->is_verified ? 'outline' : 'primary' }}" type="submit">{{ $profile?->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.instructors.destroy', $instructor) }}" onsubmit="return confirm('Pindahkan akun pengajar ini ke Recycle Bin? Course miliknya akan dibuat draft.')">
                        @csrf
                        @method('DELETE')
                        <button class="admin-btn danger" type="submit">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="admin-empty-state">Pengajar tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="admin-pagination">{{ $instructors->links() }}</div>
</section>
@endsection
