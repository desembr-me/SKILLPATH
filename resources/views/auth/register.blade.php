@extends('layouts.app')

@section('title', 'Daftar | SKILLPATH')

@section('content')
<section class="auth-section">
    <div class="container auth-grid">
        <div class="auth-side">
            <span class="eyebrow">Mulai dari minat anak</span>
            <h1>Buat akun orang tua terlebih dahulu.</h1>
            <p>Setelah daftar, Anda akan mengisi profil anak dan memilih minat.</p>
        </div>

        <div class="form-card">
            <h2>Daftar</h2>

            <form method="POST" action="{{ route('register.store') }}" class="form-stack">
                @csrf

                <label>
                    <span>Nama orang tua</span>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Kata sandi</span>
                    <input type="password" name="password" required>
                    @error('password') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Ulangi kata sandi</span>
                    <input type="password" name="password_confirmation" required>
                </label>

                <button class="btn btn-dark btn-full" type="submit">Buat Akun</button>
            </form>

            <p class="form-note">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>.</p>
        </div>
    </div>
</section>
@endsection
