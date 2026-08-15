@extends('layouts.app')
@section('title','Profil Keluarga')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Keluarga</span>
            <h1>Kelola data orang tua dan anak</h1>
            <p>Lengkapi foto dan informasi keluarga agar pengalaman SkillPath lebih personal.</p>
        </div>
        <a class="btn btn-soft" href="{{ route('parent.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="profile-grid">
        <div class="panel profile-card">
            <div class="panel-heading">
                <div><span class="panel-kicker">Akun orang tua</span><h2>Data Saya</h2></div>
            </div>

            <form method="POST" action="{{ route('parent.profile.update') }}" enctype="multipart/form-data" class="profile-form">
                @csrf
                @method('PUT')

                <div class="profile-photo-block">
                    <div class="profile-photo large">
                        @if($user->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="Foto {{ $user->name }}">
                        @else
                            {{ strtoupper(substr($user->name,0,1)) }}
                        @endif
                    </div>
                    <div>
                        <b>Foto profil orang tua</b>
                        <small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                        <label class="file-button">Pilih foto
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                        </label>
                    </div>
                </div>

                <div class="form-grid">
                    <label>Nama lengkap
                        <input name="name" value="{{ old('name', $user->name) }}" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </label>
                    <label>No. HP
                        <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08xxxxxxxxxx">
                    </label>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary"><x-icon name="check" /> Simpan Profil</button>
                </div>
            </form>
        </div>

        <div class="panel profile-summary-card">
            <span class="panel-kicker">Ringkasan keluarga</span>
            <div class="family-summary">
                <div class="summary-avatar">
                    @if($user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="">
                    @else
                        {{ strtoupper(substr($user->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
            </div>
            <div class="summary-stats">
                <div><b>{{ $children->count() }}</b><span>Anak</span></div>
                <div><b>{{ $children->sum(fn($child) => count($child->interests ?? [])) }}</b><span>Minat tersimpan</span></div>
            </div>
        </div>
    </div>

    <div class="section-heading">
        <div>
            <span class="panel-kicker">Profil Anak</span>
            <h2>Anak dalam keluarga</h2>
        </div>
        <span class="section-note">Foto dan data anak dapat diperbarui kapan saja.</span>
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

                <form method="POST" action="{{ route('parent.profile.children.update', $child) }}" enctype="multipart/form-data" class="profile-form">
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

                    <label>Catatan tambahan
                        <textarea name="notes" rows="3" placeholder="Catatan yang perlu diketahui mentor...">{{ $child->notes }}</textarea>
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
                <div><h3>Belum ada profil anak</h3><p>Tambahkan anak agar rekomendasi dan jalur belajar dapat dipersonalisasi.</p></div>
            </div>
        @endforelse
    </div>

    <div class="panel add-child-card" id="tambah-anak">
        <div class="section-heading">
            <div>
                <span class="panel-kicker">Profil Baru</span>
                <h2>Tambahkan anak</h2>
                <p>Buat profil baru tanpa harus keluar dari halaman profil keluarga.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('parent.profile.children.store') }}" enctype="multipart/form-data" class="profile-form">
            @csrf

            <div class="profile-photo-block">
                <div class="profile-photo child-placeholder"><x-icon name="child" /></div>
                <div>
                    <b>Foto anak</b>
                    <small>Opsional. JPG, PNG, atau WEBP, maksimal 2 MB.</small>
                    <label class="file-button">Pilih foto
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                    </label>
                </div>
            </div>

            <div class="form-grid">
                <label>Nama anak
                    <input name="name" required placeholder="Contoh: Alya">
                </label>
                <label>Nama panggilan
                    <input name="nickname" placeholder="Contoh: Aly">
                </label>
                <label>Tanggal lahir
                    <input type="date" name="birth_date" required>
                </label>
            </div>

            <div class="form-section-title compact">
                <div><span>01</span><h3>Minat awal</h3></div>
                <small>Pilih maksimal 3</small>
            </div>
            <div class="profile-interest-list">
                @foreach(['Arts'=>'Seni','Music'=>'Musik','Languages'=>'Bahasa','Sports'=>'Olahraga','Self Improvement'=>'Pengembangan diri','Technology'=>'Teknologi'] as $value => $label)
                    <label><input type="checkbox" name="interests[]" value="{{ $value }}"><span>{{ $label }}</span></label>
                @endforeach
            </div>

            <label>Catatan tambahan
                <textarea name="notes" rows="3" placeholder="Hal yang ingin orang tua sampaikan kepada mentor..."></textarea>
            </label>

            <div class="form-actions">
                <button class="btn btn-primary"><x-icon name="child" /> Tambahkan Profil Anak</button>
            </div>
        </form>
    </div>
</section>
@endsection
