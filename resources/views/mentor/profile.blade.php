@extends('layouts.app')
@section('title','Profil Pengajar')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Pengajar</span>
            <h1>Kelola profil dan bio kamu</h1>
            <p>Foto dan bio yang lengkap membantu orang tua mengenal gaya mengajarmu.</p>
        </div>
        <a class="btn btn-soft" href="{{ route('mentor.dashboard') }}"><x-icon name="arrow-left" /> Kembali ke Dashboard</a>
    </div>

    <div class="profile-grid">
        <div class="panel profile-card">
            <div class="panel-heading">
                <div><span class="panel-kicker">Akun pengajar</span><h2>Data Saya</h2></div>
            </div>

            <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data" class="profile-form">
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
                        <b>Foto profil pengajar</b>
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
                    <label>Headline
                        <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="Contoh: Mentor Robotika & Coding Anak">
                    </label>
                </div>

                <label>Bio singkat
                    <textarea name="bio" rows="4" placeholder="Ceritakan pengalaman dan gaya mengajarmu kepada orang tua...">{{ old('bio', $user->bio) }}</textarea>
                </label>

                <div class="form-actions">
                    <button class="btn btn-primary"><x-icon name="check" /> Simpan Profil</button>
                </div>
            </form>
        </div>

        <div class="panel profile-summary-card">
            <span class="panel-kicker">Ringkasan mengajar</span>
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
            <div class="mini-tags summary-tags">
                <span>Spesialisasi: {{ $user->category->name }}</span>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
