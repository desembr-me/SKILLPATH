@extends('layouts.app')
@section('title','Profil Anak')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Anak</span>
            <h1>Kelola Data & Foto Anak</h1>
            <p>Perbarui foto, minat, dan gaya belajar anak agar rekomendasi kurikulum tetap sesuai.</p>
        </div>
        <a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="children-profile-grid">
        @forelse($children as $child)
            <article class="panel child-profile-card">
                <div class="child-profile-heading">
                    <div class="profile-photo" id="childAvatarContainer_{{ $child->id }}" style="overflow:hidden; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:50%; background:#f1efff; color:var(--purple); font-size:20px; font-weight:600; border:2px solid #ddd6fe; flex-shrink:0;">
                        @if($child->avatar_url)
                            <img id="childPreviewImg_{{ $child->id }}" src="{{ $child->avatar_url }}" alt="Foto {{ $child->name }}" style="width:100%; height:100%; object-fit:cover;">
                        @elseif($child->avatar && !str_starts_with($child->avatar, 'avatars/'))
                            <span id="childInitial_{{ $child->id }}" style="font-size:24px;">{{ $child->avatar }}</span>
                            <img id="childPreviewImg_{{ $child->id }}" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                        @else
                            <span id="childInitial_{{ $child->id }}">{{ $child->initial }}</span>
                            <img id="childPreviewImg_{{ $child->id }}" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>
                    <div>
                        <span class="panel-kicker">Profil anak</span>
                        <h2>{{ $child->name }}</h2>
                        <p>{{ $child->age }} tahun @if($child->nickname) • {{ $child->nickname }} @endif</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('parent.children.update', $child) }}" enctype="multipart/form-data" class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="photo-input-row" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                        <label class="btn btn-sm btn-soft" style="cursor:pointer; margin:0;">
                            <x-icon name="plus" /> Ganti Foto Anak
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" onchange="previewChildAvatar(this, '{{ $child->id }}')">
                        </label>
                        @if($child->avatar)
                            <label style="font-size:11px; color:var(--danger); display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="checkbox" name="remove_avatar" value="1"> Hapus foto
                            </label>
                        @endif
                        <small style="color:var(--muted);">Maksimal 5 MB</small>
                    </div>

                    <div class="form-grid">
                        <label>Nama Anak
                            <input name="name" value="{{ $child->name }}" required>
                        </label>
                        <label>Nama Panggilan
                            <input name="nickname" value="{{ $child->nickname }}" placeholder="Contoh: Alya">
                        </label>
                        <label>Tanggal Lahir
                            <input type="date" name="birth_date" value="{{ optional($child->birth_date)->format('Y-m-d') }}" required>
                        </label>
                    </div>

                    <div class="form-section-title compact" style="margin-top:14px;">
                        <div><span>01</span><h3>Minat Anak</h3></div>
                        <small>Pilih maksimal 3</small>
                    </div>
                    <div class="profile-interest-list">
                        @foreach(['Arts'=>'Seni','Music'=>'Musik','Languages'=>'Bahasa','Sports'=>'Olahraga','Self Improvement'=>'Pengembangan Diri','Technology'=>'Teknologi'] as $value => $label)
                            <label>
                                <input type="checkbox" name="interests[]" value="{{ $value }}" @checked(in_array($value, $child->interests ?? []))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-section-title compact" style="margin-top:14px;">
                        <div><span>02</span><h3>Gaya Belajar</h3></div>
                    </div>
                    <div class="profile-interest-list preferences">
                        @foreach(['hands_on'=>'Praktik Langsung','group'=>'Bersama Teman','step_by_step'=>'Bertahap & Terstruktur','storytelling'=>'Bercerita / Tampil'] as $value => $label)
                            <label>
                                <input type="checkbox" name="learning_preferences[]" value="{{ $value }}" @checked(in_array($value, $child->learning_preferences ?? []))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-section-title compact" style="margin-top:14px;">
                        <div><span>03</span><h3>Catatan Khusus</h3></div>
                        <small>Opsional, untuk pengajar</small>
                    </div>
                    <label class="notes-field">
                        <textarea name="notes" rows="3" placeholder="Contoh: alergi tertentu, mudah gugup di keramaian...">{{ $child->notes }}</textarea>
                    </label>

                    <div class="form-actions" style="margin-top:18px;">
                        <button class="btn btn-primary btn-sm"><x-icon name="check" /> Simpan Profil Anak</button>
                        <a class="btn btn-ghost btn-sm" href="{{ route('parent.learning-path', $child) }}">Jalur Belajar &rarr;</a>
                    </div>
                </form>
            </article>
        @empty
            <div class="panel empty-profile">
                <x-icon name="child" />
                <div><h3>Belum ada profil anak</h3><p>Tambahkan anak melalui alur onboarding agar rekomendasi kursus dapat dipersonalisasi.</p></div>
            </div>
        @endforelse
    </div>
</section>

<script>
function previewChildAvatar(input, childId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('childPreviewImg_' + childId);
            var initial = document.getElementById('childInitial_' + childId);
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (initial) initial.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
