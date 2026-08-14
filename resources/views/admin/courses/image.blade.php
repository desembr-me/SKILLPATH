@extends('admin.layouts.app')

@section('title', 'Ganti Gambar Course | Admin SKILLPATH')
@section('page-title', 'Ganti Gambar Course')

@section('content')
<section class="admin-panel admin-image-editor-panel">
    <div class="admin-panel-head admin-panel-head-wrap">
        <div>
            <span class="admin-eyebrow">Tampilan card course</span>
            <h2>{{ $course->title }}</h2>
            <p>Pilih gambar sendiri dari perangkat. Gambar baru akan digunakan pada card course di seluruh website.</p>
        </div>
        <a class="admin-btn ghost" href="{{ route('admin.courses.index') }}">← Kembali</a>
    </div>

    <div class="admin-image-editor-grid">
        <div class="admin-image-current-card">
            <span class="admin-image-editor-label">Gambar saat ini</span>
            <div class="admin-image-large-preview">
                @if($course->thumbnailSrc())
                    <img src="{{ $course->thumbnailSrc() }}" alt="Gambar {{ $course->title }}">
                @else
                    <div class="admin-image-empty">{{ $course->icon ?: '✦' }}</div>
                @endif
            </div>
            <div class="admin-image-course-meta">
                <strong>{{ $course->title }}</strong>
                <span>{{ $course->level }} · Usia {{ $course->min_age }}–{{ $course->max_age }} tahun</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.image.update', $course) }}" enctype="multipart/form-data" class="admin-image-upload-card" data-course-image-form>
            @csrf
            @method('PATCH')

            <span class="admin-image-editor-label">Pilih gambar baru</span>
            <label class="admin-course-dropzone" for="course-thumbnail-input" data-course-dropzone>
                <input id="course-thumbnail-input" type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required data-course-image-input>
                <span class="admin-dropzone-icon">▧</span>
                <strong>Klik untuk memilih gambar</strong>
                <small>Bisa menggunakan foto atau ilustrasi sesuai keinginan Anda.</small>
                <small>JPG, PNG, WebP · maksimal 5 MB · ukuran gambar bebas. Preview dan card otomatis diseragamkan ke 16:10 dengan crop proporsional.</small>
            </label>

            <div class="admin-new-image-preview" data-course-image-preview hidden>
                <span>Preview gambar baru</span>
                <img alt="Preview gambar course baru" data-course-image-preview-img>
                <div class="admin-new-image-info">
                    <strong data-course-image-name></strong>
                    <button type="button" class="admin-text-link" data-course-image-clear>Batalkan pilihan</button>
                </div>
            </div>

            @error('thumbnail')
                <div class="admin-field-error">{{ $message }}</div>
            @enderror

            <div class="admin-offline-notice">
                <strong>Bebas ganti kapan saja</strong>
                <p>Gambar bawaan hanya sebagai contoh. Admin dapat menggantinya dengan gambar sendiri tanpa mengubah data course lainnya.</p>
            </div>

            <div class="admin-form-actions">
                <button class="admin-btn primary" type="submit">Simpan Gambar Baru</button>
                <a class="admin-btn ghost" href="{{ route('admin.courses.edit', $course) }}">Edit Data Course</a>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('[data-course-image-input]');
    const preview = document.querySelector('[data-course-image-preview]');
    const image = document.querySelector('[data-course-image-preview-img]');
    const name = document.querySelector('[data-course-image-name]');
    const clear = document.querySelector('[data-course-image-clear]');
    const dropzone = document.querySelector('[data-course-dropzone]');

    if (!input || !preview || !image) return;

    function showFile(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (event) {
            image.src = event.target.result;
            preview.hidden = false;
            if (name) name.textContent = file.name;
            if (dropzone) dropzone.classList.add('has-file');
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function () {
        showFile(input.files && input.files[0]);
    });

    if (clear) {
        clear.addEventListener('click', function () {
            input.value = '';
            image.removeAttribute('src');
            preview.hidden = true;
            if (name) name.textContent = '';
            if (dropzone) dropzone.classList.remove('has-file');
        });
    }

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone?.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add('dragging');
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone?.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove('dragging');
        });
    });

    dropzone?.addEventListener('drop', function (event) {
        const files = event.dataTransfer?.files;
        if (!files || !files.length) return;
        input.files = files;
        showFile(files[0]);
    });
});
</script>
@endsection
