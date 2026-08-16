@extends('layouts.admin')
@section('title', 'Edit Course')

@section('content')
<section class="admin-course-form-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">KATALOG BELAJAR</span>
            <h1>Edit Course: {{ $course->title }}</h1>
            <p>Perbarui informasi materi, mentor pengampu, jadwal sesi, atau lokasi pelaksanaan.</p>
        </div>
        <div class="admin-action-group">
            <a href="{{ route('admin.courses.index') }}" class="btn-admin-white">
                <x-icon name="arrow-left" />
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <div class="admin-card" style="max-width: 900px;">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <label style="grid-column: span 2;">Judul Course
                    <input name="title" value="{{ old('title', $course->title) }}" required>
                </label>

                <label style="grid-column: span 2;">Sub Judul / Ringkasan
                    <input name="subtitle" value="{{ old('subtitle', $course->subtitle) }}" required>
                </label>

                <label>Kategori
                    <select name="category_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Pengajar (Mentor)
                    <select name="instructor_id" required>
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ old('instructor_id', $course->instructor_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }} ({{ $inst->category->name ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </label>

                <label>Rentang Usia Minimal (Tahun)
                    <input type="number" name="age_min" value="{{ old('age_min', $course->age_min) }}" min="3" max="18" required>
                </label>

                <label>Rentang Usia Maksimal (Tahun)
                    <input type="number" name="age_max" value="{{ old('age_max', $course->age_max) }}" min="3" max="18" required>
                </label>

                <label>Biaya Pendaftaran (Rp)
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="10000" required>
                </label>

                <label>Jumlah Pertemuan / Sesi
                    <input type="number" name="sessions_count" value="{{ old('sessions_count', $course->sessions_count) }}" min="1" max="50" required>
                </label>

                <label>Durasi per Sesi (Menit)
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes) }}" min="15" max="300" required>
                </label>

                <label>Status Publikasi
                    <select name="status" required>
                        <option value="active" {{ old('status', $course->status) === 'active' ? 'selected' : '' }}>Aktif (Ditampilkan di Website)</option>
                        <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft (Simpan Sementara)</option>
                    </select>
                </label>

                <label>Kota Pelaksanaan
                    <input name="city" value="{{ old('city', $course->city) }}" required>
                </label>

                <label>Nama Tempat / Studio
                    <input name="location_name" value="{{ old('location_name', $course->location_name) }}" required>
                </label>

                <label style="grid-column: span 2;">Alamat Lengkap
                    <input name="address" value="{{ old('address', $course->address) }}" required>
                </label>

                <label style="grid-column: span 2;">Ganti Foto Sampul / Banner Course (Opsional)
                    @if($course->cover_image)
                        <div style="margin-bottom:8px;">
                            <img src="{{ asset('storage/'.$course->cover_image) }}" alt="Sampul saat ini" style="height:60px; border-radius:8px;">
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*">
                </label>

                <label style="grid-column: span 2;">Deskripsi Lengkap & Tujuan Pembelajaran
                    <textarea name="description" rows="5" required>{{ old('description', $course->description) }}</textarea>
                </label>
            </div>

            <div style="display:flex; gap:12px; margin-top:24px;">
                <button type="submit" class="btn-admin-primary">
                    <x-icon name="check" />
                    <span>Simpan Perubahan</span>
                </button>
                <a href="{{ route('admin.courses.index') }}" class="btn-admin-white">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
