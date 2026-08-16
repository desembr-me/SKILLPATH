@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')

@section('content')
<section class="admin-users-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">OPERASIONAL AKUN</span>
            <h1>Manajemen Pengguna</h1>
            <p>Kelola semua akun pengguna SkillPath: Orang Tua, Pengajar (Mentor), dan Administrator.</p>
        </div>
        <div class="admin-action-group">
            <button class="btn-admin-primary" onclick="toggleAddUserModal()">
                <x-icon name="plus" />
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="admin-card" style="padding: 16px 20px; margin-bottom: 20px;">
        <div class="admin-filter-bar" style="margin-bottom: 0;">
            <div class="admin-filter-tabs">
                <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => $search]) }}" class="admin-filter-tab {{ $currentRole === 'all' ? 'active' : '' }}">
                    Semua ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'parent', 'search' => $search]) }}" class="admin-filter-tab {{ $currentRole === 'parent' ? 'active' : '' }}">
                    Orang Tua ({{ $parentCount }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'mentor', 'search' => $search]) }}" class="admin-filter-tab {{ $currentRole === 'mentor' ? 'active' : '' }}">
                    Pengajar ({{ $mentorCount }})
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}" class="admin-filter-tab {{ $currentRole === 'admin' ? 'active' : '' }}">
                    Admin ({{ $adminCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="admin-search-input">
                @if($currentRole !== 'all')
                    <input type="hidden" name="role" value="{{ $currentRole }}">
                @endif
                <x-icon name="search" />
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, no. HP...">
                @if($search)
                    <a href="{{ route('admin.users.index', ['role' => $currentRole]) }}" style="color:#8a84ab; font-size:11px; font-weight:600;">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Add User Modal / Box (Hidden by default) -->
    <div id="addUserModal" class="admin-card" style="display: none; border: 2px solid #5b36f5;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-family:'Fredoka'; font-size:20px; margin:0; color:#120e2e;">Tambah Pengguna Baru</h3>
            <button type="button" onclick="toggleAddUserModal()" style="background:none; border:none; color:#8a84ab; font-size:18px; cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <label>Nama Lengkap <input name="name" required placeholder="Contoh: Budi Santoso"></label>
                <label>Email <input type="email" name="email" required placeholder="Contoh: user@domain.com"></label>
                <label>No. HP / WhatsApp <input name="phone" placeholder="Contoh: 081234567890"></label>
                <label>Role
                    <select name="role" required onchange="checkRoleSelection(this)">
                        <option value="parent">Orang Tua</option>
                        <option value="mentor">Pengajar (Mentor)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </label>
                <label id="categorySelectWrap" style="display:none;">Kategori Spesialisasi Mentor
                    <select name="category_id">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Password <input type="password" name="password" required placeholder="Minimal 8 karakter"></label>
            </div>
            <div style="display:flex; gap:10px; margin-top:16px;">
                <button type="submit" class="btn-admin-primary">Simpan Pengguna</button>
                <button type="button" class="btn-admin-white" onclick="toggleAddUserModal()">Batal</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="admin-panel" style="padding: 0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Kontak</th>
                        <th>Spesialisasi</th>
                        <th>Bergabung</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="admin-avatar" style="width:34px; height:34px;">
                                        @if($u->avatar_url)
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                                        @else
                                            {{ $u->initial }}
                                        @endif
                                    </div>
                                    <div>
                                        <b style="color:#120e2e; display:block;">{{ $u->name }}</b>
                                        <small style="color:#8a84ab;">{{ $u->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-pill {{ $u->role === 'admin' ? 'paid' : ($u->role === 'mentor' ? 'pending' : '') }}" style="{{ $u->role === 'parent' ? 'background:#e0e7ff; color:#4338ca;' : '' }}">
                                    {{ $u->role === 'parent' ? 'Orang Tua' : ($u->role === 'mentor' ? 'Pengajar' : 'Admin') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#5c567e;">{{ $u->phone ?: '-' }}</span>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#5c567e;">{{ $u->category->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="font-size:11.5px; color:#8a84ab;">{{ $u->created_at->format('d M Y') }}</span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; align-items:center; gap:8px;">
                                    <button class="btn btn-sm btn-soft" onclick="openEditUserModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->phone }}', '{{ $u->role }}', '{{ $u->category_id }}')">
                                        <x-icon name="edit" />
                                        <span>Edit</span>
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--danger);" title="Hapus">
                                                <x-icon name="trash" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:#8a84ab; padding:32px;">
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #eaebf4;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Edit User Modal (Hidden) -->
    <div id="editUserModal" class="admin-card" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:min(550px, 92vw); z-index:1000; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: 2px solid #5b36f5;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-family:'Fredoka'; font-size:20px; margin:0; color:#120e2e;">Edit Data Pengguna</h3>
            <button type="button" onclick="closeEditUserModal()" style="background:none; border:none; color:#8a84ab; font-size:20px; cursor:pointer;">&times;</button>
        </div>
        <form id="editUserForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <label>Nama Lengkap <input id="editName" name="name" required></label>
                <label>Email <input type="email" id="editEmail" name="email" required></label>
                <label>No. HP / WhatsApp <input id="editPhone" name="phone"></label>
                <label>Role
                    <select id="editRole" name="role" required onchange="checkEditRoleSelection(this)">
                        <option value="parent">Orang Tua</option>
                        <option value="mentor">Pengajar (Mentor)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </label>
                <label id="editCategorySelectWrap" style="display:none;">Kategori Spesialisasi Mentor
                    <select id="editCategoryId" name="category_id">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Password Baru (Opsional) <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah"></label>
            </div>
            <div style="display:flex; gap:10px; margin-top:18px;">
                <button type="submit" class="btn-admin-primary">Simpan Perubahan</button>
                <button type="button" class="btn-admin-white" onclick="closeEditUserModal()">Batal</button>
            </div>
        </form>
    </div>
</section>

<script>
function toggleAddUserModal() {
    var modal = document.getElementById('addUserModal');
    modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
}

function checkRoleSelection(select) {
    var wrap = document.getElementById('categorySelectWrap');
    wrap.style.display = select.value === 'mentor' ? 'block' : 'none';
}

function checkEditRoleSelection(select) {
    var wrap = document.getElementById('editCategorySelectWrap');
    wrap.style.display = select.value === 'mentor' ? 'block' : 'none';
}

function openEditUserModal(id, name, email, phone, role, categoryId) {
    var form = document.getElementById('editUserForm');
    form.action = '/admin/users/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone || '';
    var roleSelect = document.getElementById('editRole');
    roleSelect.value = role;
    checkEditRoleSelection(roleSelect);
    if (categoryId) {
        document.getElementById('editCategoryId').value = categoryId;
    }
    document.getElementById('editUserModal').style.display = 'block';
}

function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}
</script>
@endsection
