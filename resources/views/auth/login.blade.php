@extends('layouts.app')

@section('title', 'Masuk | SKILLPATH')

@section('content')
<section class="auth-section">
    <div class="container auth-grid">
        <div class="auth-side">
            <span class="eyebrow">Selamat datang kembali</span>
            <h1>Lanjutkan jalur mengikuti kelas anak.</h1>
            <p>Masuk untuk melihat progres dan rekomendasi berikutnya.</p>
        </div>

        <div class="form-card">
            <h2>Masuk</h2>

            <form method="POST" action="{{ route('login.store') }}" class="form-stack">
                @csrf

                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Kata sandi</span>
                    <input type="password" name="password" required>
                </label>

                <label class="check-row">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya</span>
                </label>

                <button class="btn btn-dark btn-full" type="submit">Masuk</button>
            </form>

            <p class="form-note">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>.</p>
        </div>
    </div>
</section>
@endsection
