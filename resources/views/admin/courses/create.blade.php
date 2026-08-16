@extends('layouts.admin')
@section('title', 'Tambah Course Baru')

@section('content')
<section class="admin-course-form-view">
    <div class="admin-header-row" style="margin-bottom: 24px;">
        <div class="admin-header-copy">
            <span class="eyebrow">KATALOG BELAJAR</span>
            <h1>Tambah Course Baru</h1>
            <p>Buat program belajar offline baru dengan mentor, kurikulum terstruktur, dan jadwal sesi.</p>
        </div>
        <div class="admin-action-group">
            <a href="{{ route('admin.courses.index') }}" class="btn-admin-white">
                <x-icon name="arrow-left" />
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <div class="admin-card" style="max-width: 900px;">
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <label style="grid-column: span 2;">Judul Course
                    <input name="title" value="{{ old('title') }}" required placeholder="Contoh: Robotik Cilik: Merakit Robot Pertama">
                </label>

                <label style="grid-column: span 2;">Sub Judul / Ringkasan
                    <input name="subtitle" value="{{ old('subtitle') }}" required placeholder="Contoh: Eksplorasi sensor dan logika pemrograman dasar anak usia 7-10 tahun">
                </label>

                <label>Kategori
                    <select name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Pengajar (Mentor)
                    <select name="instructor_id" required>
                        <option value="">Pilih Mentor Pengampu</option>
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }} ({{ $inst->category->name ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </label>

                <label>Rentang Usia Minimal (Tahun)
                    <input type="number" name="age_min" value="{{ old('age_min', 6) }}" min="3" max="18" required>
                </label>

                <label>Rentang Usia Maksimal (Tahun)
                    <input type="number" name="age_max" value="{{ old('age_max', 12) }}" min="3" max="18" required>
                </label>

                <label>Biaya Pendaftaran (Rp)
                    <input type="number" name="price" value="{{ old('price', 350000) }}" min="0" step="10000" required>
                </label>

                <label>Jumlah Pertemuan / Sesi
                    <input type="number" name="sessions_count" value="{{ old('sessions_count', 4) }}" min="1" max="50" required>
                </label>

                <label>Durasi per Sesi (Menit)
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 90) }}" min="15" max="300" required>
                </label>

                <label>Status Publikasi
                    <select name="status" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif (Ditampilkan di Website)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Simpan Sementara)</option>
                    </select>
                </label>

                <label>Kota Pelaksanaan
                    <input name="city" value="{{ old('city', 'Bandung') }}" required placeholder="Contoh: Bandung">
                </label>

                <label>Nama Tempat / Studio
                    <input name="location_name" value="{{ old('location_name', 'SkillPath Hub Dago') }}" required placeholder="Contoh: SkillPath Hub Studio 2">
                </label>

                <label style="grid-column: span 2;">Alamat Lengkap
                    <input name="address" value="{{ old('address', 'Jl. Ir. H. Juanda No. 123, Dago, Bandung') }}" required>
                </label>

                <label style="grid-column: span 2;">Foto Sampul / Banner Course (Opsional)
                    <input type="file" name="cover_image" accept="image/*">
                </label>

                <label style="grid-column: span 2;">Deskripsi Lengkap & Tujuan Pembelajaran
                    <textarea name="description" rows="5" required placeholder="Jelaskan apa yang akan dipelajari anak, metode bimbingan, dan hasil akhir kelas.">{{ old('description') }}</textarea>
                </label>
            </div>

            <div style="display:flex; gap:12px; margin-top:24px;">
                <button type="submit" class="btn-admin-primary">
                    <x-icon name="check" />
                    <span>Simpan & Terbitkan Course</span>
                </button>
                <a href="{{ route('admin.courses.index') }}" class="btn-admin-white">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
