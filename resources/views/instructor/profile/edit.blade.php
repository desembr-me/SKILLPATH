@extends('layouts.instructor')
@section('title','Profil Pengajar | SKILLPATH')
@section('content')
<div class="instructor-page-header">
    <span class="eyebrow">Profil Pengajar</span>
    <h1>Perbarui profil Anda.</h1>
    <p>Foto dan informasi ini akan tampil kepada orang tua saat memilih pengajar untuk anak.</p>
</div>

<div class="profile-editor-grid">
    <section class="profile-photo-panel">
        <div class="profile-photo-preview">
            @if($profile->photoSrc())
                <img src="{{ $profile->photoSrc() }}" alt="Foto {{ auth()->user()->name }}">
            @else
                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <p>{{ $profile->headline ?: 'Tambahkan headline singkat agar orang tua lebih mudah mengenal Anda.' }}</p>
            @if($profile->is_verified)<span class="verified-pill">✓ Pengajar terverifikasi</span>@endif
        </div>
    </section>

    <section class="profile-form-card">
        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="form-stack">
            @csrf
            @method('PUT')

            <div class="two-fields">
                <label><span>Nama pengajar</span><input name="name" value="{{ old('name', auth()->user()->name) }}" required></label>
                <label><span>Pengalaman (tahun)</span><input type="number" min="0" max="60" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" required></label>
            </div>

            <label><span>Foto profil</span><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WebP. Maksimal 2 MB. Gunakan foto wajah yang jelas.</small></label>
            @if($profile->photo_url)
                <label class="check-line"><input type="checkbox" name="remove_photo" value="1"><span>Hapus foto profil saat ini</span></label>
            @endif

            <label><span>Headline</span><input name="headline" maxlength="140" value="{{ old('headline', $profile->headline) }}" placeholder="Contoh: Coach public speaking dan komunikasi anak"></label>
            <label><span>Keahlian</span><input name="expertise" maxlength="180" value="{{ old('expertise', $profile->expertise) }}" placeholder="Contoh: Public speaking, storytelling, communication"></label>
            <label><span>Pendidikan</span><input name="education" maxlength="180" value="{{ old('education', $profile->education) }}" placeholder="Contoh: S1 Pendidikan Bahasa"></label>
            <label><span>Tentang Anda</span><textarea name="bio" rows="7" maxlength="2000" placeholder="Ceritakan pendekatan Anda saat mendampingi anak belajar secara tatap muka...">{{ old('bio', $profile->bio) }}</textarea></label>

            <div class="profile-form-actions">
                <button class="btn btn-dark" type="submit">Simpan Profil</button>
                <a class="btn btn-ghost" href="{{ route('instructors.show', auth()->user()) }}" target="_blank">Lihat Profil Publik</a>
            </div>
        </form>
    </section>
</div>
@endsection
