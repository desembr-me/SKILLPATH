@extends('layouts.app')
@section('title','Profil Anak')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Anak</span>
            <h1>Kelola data anak</h1>
            <p>Perbarui foto, minat, dan gaya belajar anak agar rekomendasi tetap sesuai.</p>
        </div>
        <a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="children-profile-grid">
        @forelse($children as $child)
            <article class="panel child-profile-card">
                <div class="child-profile-heading">
                    <div class="profile-photo">
                        @if($child->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($child->avatar) }}" alt="Foto {{ $child->name }}">
                        @else
                            {{ strtoupper(substr($child->name,0,1)) }}
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

                    <div class="photo-input-row">
                        <label class="file-button">Ganti foto anak
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                        </label>
                        <small>Opsional · maksimal 2 MB</small>
                    </div>

                    <div class="form-grid">
                        <label>Nama anak
                            <input name="name" value="{{ $child->name }}" required>
                        </label>
                        <label>Nama panggilan
                            <input name="nickname" value="{{ $child->nickname }}" placeholder="Contoh: Alya">
                        </label>
                        <label>Tanggal lahir
                            <input type="date" name="birth_date" value="{{ optional($child->birth_date)->format('Y-m-d') }}" required>
                        </label>
                    </div>

                    <div class="form-section-title compact">
                        <div><span>01</span><h3>Minat anak</h3></div>
                        <small>Pilih maksimal 3</small>
                    </div>
                    <div class="profile-interest-list">
                        @foreach(['Arts'=>'Seni','Music'=>'Musik','Languages'=>'Bahasa','Sports'=>'Olahraga','Self Improvement'=>'Pengembangan diri','Technology'=>'Teknologi'] as $value => $label)
                            <label>
                                <input type="checkbox" name="interests[]" value="{{ $value }}" @checked(in_array($value, $child->interests ?? []))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-section-title compact">
                        <div><span>02</span><h3>Gaya belajar</h3></div>
                    </div>
                    <div class="profile-interest-list preferences">
                        @foreach(['hands_on'=>'Praktik langsung','group'=>'Bersama teman','step_by_step'=>'Bertahap & terstruktur','storytelling'=>'Bercerita / tampil'] as $value => $label)
                            <label>
                                <input type="checkbox" name="learning_preferences[]" value="{{ $value }}" @checked(in_array($value, $child->learning_preferences ?? []))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-section-title compact">
                        <div><span>03</span><h3>Catatan tambahan</h3></div>
                        <small>Opsional, untuk mentor</small>
                    </div>
                    <label class="notes-field">
                        <textarea name="notes" rows="3" placeholder="Contoh: alergi makanan tertentu, mudah gugup di keramaian...">{{ $child->notes }}</textarea>
                    </label>

                    <div class="form-actions">
                        <button class="btn btn-soft">Simpan Perubahan Anak</button>
                        <a class="text-link" href="{{ route('parent.learning-path', $child) }}">Lihat jalur belajar <x-icon name="arrow-right" /></a>
                    </div>
                </form>
            </article>
        @empty
            <div class="panel empty-profile">
                <x-icon name="child" />
                <div><h3>Belum ada profil anak</h3><p>Buat profil anak lewat onboarding agar rekomendasi dapat dipersonalisasi.</p></div>
            </div>
        @endforelse
    </div>
</section>
@endsection
