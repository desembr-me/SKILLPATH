@extends('layouts.admin')
@section('title', 'Profil Administrator')

@section('content')
<section class="admin-profile-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">PENGATURAN AKUN</span>
            <h1>Profil Administrator</h1>
            <p>Kelola data akun, foto profil, dan kredensial akses panel admin SkillPath.</p>
        </div>
    </div>

    <div class="panel profile-card profile-single" style="max-width: 680px; margin: 0 auto;">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Administrator</span>
                <h2>Data Administrator</h2>
            </div>
            <span class="helper-badge">Role: Admin</span>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')

            <div class="profile-photo-block" style="display:flex; align-items:center; gap:20px; padding:16px 0; border-bottom:1px solid #eff0f4;">
                <div class="profile-photo large" id="avatarPreviewContainer" style="width:84px; height:84px; border-radius:50%; overflow:hidden; background:#e0e7ff; color:var(--purple); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:600; border:2px solid #c7d2fe; flex-shrink:0;">
                    @if($user->avatar_url)
                        <img id="avatarPreviewImg" src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <span id="avatarInitial">{{ $user->initial }}</span>
                        <img id="avatarPreviewImg" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                    @endif
                </div>
                <div style="flex:1;">
                    <b style="display:block; font-size:14px; margin-bottom:2px;">Foto Profil Administrator</b>
                    <small style="color:var(--muted); display:block; margin-bottom:10px;">Format: JPG, PNG, atau WEBP. Maksimal 5 MB.</small>
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <label class="btn btn-sm btn-soft" style="cursor:pointer; margin:0;">
                            <x-icon name="plus" /> Pilih Foto Baru
                            <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" data-crop-avatar data-preview-target="#avatarPreviewContainer">
                        </label>
                        @if($user->avatar)
                            <label style="font-size:11px; color:var(--danger); display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="checkbox" name="remove_avatar" value="1"> Hapus foto profil saat ini
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-grid" style="margin-top:18px;">
                <label>Nama Lengkap
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label>Email
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
                <label>No. WhatsApp / HP
                    <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                </label>
            </div>

            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #eff0f4;">
                <h3 style="font-size:14px; margin:0 0 6px 0;">Ganti Password (Opsional)</h3>
                <small style="color:var(--muted); display:block; margin-bottom:12px;">Biarkan kosong jika Anda tidak ingin mengganti password.</small>
                <div class="form-grid">
                    <label>Password Baru
                        <input type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password">
                    </label>
                    <label>Konfirmasi Password Baru
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password">
                    </label>
                </div>
            </div>

            <div class="form-actions" style="margin-top:22px;">
                <button class="btn btn-primary"><x-icon name="check" /> Simpan Profil</button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.SkillPathAvatarCropper) {
        SkillPathAvatarCropper.bind('#avatarInput', {
            previewTargets: ['#avatarPreviewContainer', '.admin-avatar']
        });
    }
});
</script>
@endpush
@endsection
