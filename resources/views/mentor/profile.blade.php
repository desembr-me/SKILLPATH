@extends('layouts.app')
@section('title', 'Profil Pengajar')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Pengajar</span>
            <h1>Kelola Profil & Bio Pengajar</h1>
            <p>Foto dan bio yang lengkap membantu orang tua mengenal keahlian dan gaya mengajarmu.</p>
        </div>
    </div>

    <div class="profile-grid">
        <div class="panel profile-card">
            <div class="panel-heading">
                <div><span class="panel-kicker">Akun pengajar</span><h2>Data Saya</h2></div>
            </div>

            <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data" class="profile-form">
                @csrf
                @method('PUT')

                <div class="profile-photo-block" style="display:flex; align-items:center; gap:20px; padding:16px 0; border-bottom:1px solid #eff0f4;">
                    <div class="profile-photo large" id="avatarPreviewContainer" style="width:84px; height:84px; border-radius:50%; overflow:hidden; background:#f5f3ff; color:var(--purple); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:600; border:2px solid #ddd6fe; flex-shrink:0;">
                        @if($user->avatar_url)
                            <img id="avatarPreviewImg" src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <span id="avatarInitial">{{ $user->initial }}</span>
                            <img id="avatarPreviewImg" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>
                    <div style="flex:1;">
                        <b style="display:block; font-size:14px; margin-bottom:2px;">Foto Profil Pengajar</b>
                        <small style="color:var(--muted); display:block; margin-bottom:10px;">Format: JPG, PNG, atau WEBP. Maksimal 5 MB.</small>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <label class="btn btn-sm btn-soft" style="cursor:pointer; margin:0;">
                                <x-icon name="plus" /> Pilih Foto Baru
                                <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" data-crop-avatar data-preview-target="#avatarPreviewContainer">
                            </label>
                            @if($user->avatar)
                                <label style="font-size:11px; color:var(--danger); display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
                                    <input type="checkbox" name="remove_avatar" value="1"> Hapus foto saat ini
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
                    <label>No. HP / WhatsApp
                        <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08xxxxxxxxxx">
                    </label>
                    <label>Headline Profil
                        <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="Contoh: Mentor Robotika & Coding Anak">
                    </label>
                </div>

                <label style="margin-top:12px; display:block;">Bio Singkat
                    <textarea name="bio" rows="4" placeholder="Ceritakan pengalaman dan pendekatan mengajarmu kepada orang tua...">{{ old('bio', $user->bio) }}</textarea>
                </label>

                <div style="margin-top:20px; padding-top:16px; border-top:1px solid #eff0f4;">
                    <h3 style="font-size:14px; margin:0 0 6px 0;">Ganti Password (Opsional)</h3>
                    <small style="color:var(--muted); display:block; margin-bottom:12px;">Biarkan kosong jika tidak ingin mengubah password.</small>
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

        <div class="panel profile-summary-card">
            <span class="panel-kicker">Ringkasan Mengajar</span>
            <div class="family-summary">
                <div class="summary-avatar" id="summaryAvatarPreview">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:18px;">
                    @else
                        <span>{{ $user->initial }}</span>
                        <img src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:18px;">
                    @endif
                </div>
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->headline ?: $user->email }}</p>
                </div>
            </div>
            <div class="summary-stats">
                <div><b>{{ $courses->count() }}</b><span>Course diajar</span></div>
                <div><b>{{ $students }}</b><span>Siswa aktif</span></div>
                <div><b>{{ $rating ?: '0.0' }}</b><span>Rating mentor</span></div>
                <div><b>{{ $reviewCount }}</b><span>Ulasan diterima</span></div>
            </div>
            @if($user->category)
            <div class="mini-tags summary-tags" style="margin-top:12px;">
                <span>Spesialisasi: {{ $user->category->name }}</span>
            </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.SkillPathAvatarCropper) {
        SkillPathAvatarCropper.bind('#avatarInput', {
            previewTargets: ['#avatarPreviewContainer', '#summaryAvatarPreview']
        });
    }
});
</script>
@endpush
@endsection
