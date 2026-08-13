@extends('admin.layouts.app')

@section('title', 'Pengguna | Admin SKILLPATH')
@section('page-title', 'Pengguna')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Akun platform</span>
            <h2>Daftar pengguna</h2>
            <p>Lihat akun administrator, pengajar, dan orang tua.</p>
        </div>
        <span class="admin-total-pill">{{ $users->total() }} akun</span>
    </div>

    <form class="admin-filter-bar" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
        <select name="role">
            <option value="">Semua peran</option>
            <option value="parent" @selected(request('role') === 'parent')>Orang Tua</option>
            <option value="instructor" @selected(request('role') === 'instructor')>Pengajar</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
        </select>
        <button class="admin-btn dark" type="submit">Terapkan</button>
        <a class="admin-text-link" href="{{ route('admin.users.index') }}">Reset</a>
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Pengguna</th><th>Peran</th><th>Profil</th><th>Bergabung</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><div class="admin-user-cell"><span>{{ strtoupper(substr($user->name,0,1)) }}</span><div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></div></div></td>
                    <td><span class="role-badge {{ $user->role }}">{{ $user->role === 'parent' ? 'ORANG TUA' : strtoupper($user->role) }}</span></td>
                    <td>
                        @if($user->role === 'parent')
                            {{ $user->childProfile ? 'Anak: '.$user->childProfile->name.' ('.$user->childProfile->age.' tahun)' : 'Belum isi profil anak' }}
                        @elseif($user->role === 'instructor')
                            {{ $user->instructorProfile?->headline ?? 'Profil pengajar' }}
                        @else
                            Administrator sistem
                        @endif
                    </td>
                    <td>{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                    <td>
                        @if(auth()->id() === $user->id)
                            <span class="admin-muted">Akun aktif</span>
                        @else
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Pindahkan pengguna ini ke Recycle Bin?')">
                                @csrf
                                @method('DELETE')
                                <button class="admin-icon-btn danger" type="submit">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="admin-empty-cell">Pengguna tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-pagination">{{ $users->links() }}</div>
</section>
@endsection
