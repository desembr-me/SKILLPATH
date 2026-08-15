@extends('layouts.app')
@section('title','Profil Saya')

@section('content')
<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow">Profil Saya</span>
            <h1>Kelola data akun orang tua</h1>
            <p>Lengkapi foto dan informasi akun agar pengalaman SkillPath lebih personal.</p>
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
            <span class="panel-kicker">Ringkasan akun</span>
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
        </div>
    </div>
</section>
@endsection
