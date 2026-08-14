@php($editing = isset($course))
@php($selectedCategory = old('category_id', $editing ? optional($course->categories->first())->id : null))

<div class="admin-course-form-grid">
    <section class="admin-panel admin-course-form-main">
        <div class="admin-panel-head">
            <div>
                <span class="admin-eyebrow">Informasi utama</span>
                <h2>{{ $editing ? 'Edit course' : 'Course offline baru' }}</h2>
                <p>Course akan tampil di marketplace kelas nonakademik untuk anak usia 5–14 tahun.</p>
            </div>
        </div>

        <div class="admin-form-grid two-col">
            <label class="admin-field span-2"><span>Nama course</span><input name="title" value="{{ old('title', $course->title ?? '') }}" maxlength="120" placeholder="Contoh: Painting Adventure" required></label>

            <label class="admin-field"><span>Kategori</span>
                <select name="category_id" required>
                    <option value="">Pilih kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string)$selectedCategory === (string)$category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field"><span>Level</span>
                <select name="level" required>
                    @foreach($levels as $level)
                        <option value="{{ $level }}" @selected(old('level', $course->level ?? 'Beginner') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field"><span>Pengajar</span>
                <select name="instructor_id" required>
                    <option value="">Pilih pengajar</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected((string)old('instructor_id', $course->instructor_id ?? '') === (string)$instructor->id)>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field"><span>Skill utama</span>
                <select name="skill_id" required>
                    <option value="">Pilih skill</option>
                    @foreach($skills as $skill)
                        <option value="{{ $skill->id }}" @selected((string)old('skill_id', $course->skill_id ?? '') === (string)$skill->id)>{{ $skill->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field"><span>Usia minimum</span><input type="number" name="min_age" min="5" max="14" value="{{ old('min_age', $course->min_age ?? 5) }}" required></label>
            <label class="admin-field"><span>Usia maksimum</span><input type="number" name="max_age" min="5" max="14" value="{{ old('max_age', $course->max_age ?? 14) }}" required></label>

            <label class="admin-field"><span>Durasi program (menit)</span><input type="number" name="duration_minutes" min="30" value="{{ old('duration_minutes', $course->duration_minutes ?? 120) }}" required></label>
            <label class="admin-field"><span>Ikon singkat</span><input name="icon" maxlength="20" value="{{ old('icon', $course->icon ?? '✦') }}" placeholder="✦"></label>

            <label class="admin-field span-2" id="course-image">
                <span>Gambar card course {{ $editing ? '(boleh diganti kapan saja)' : '' }}</span>
                @if($editing && $course->thumbnailSrc())
                    <div class="admin-course-image-preview">
                        <img src="{{ $course->thumbnailSrc() }}" alt="Gambar {{ $course->title }}">
                        <small>Gambar aktif saat ini</small>
                    </div>
                @endif
                <input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" {{ $editing ? '' : 'required' }} data-course-form-thumbnail>
                <small>Pilih gambar sendiri dari perangkat. JPG, PNG, atau WebP maksimal 5 MB. Ukuran asli gambar bebas; tampilan card akan otomatis dibuat seragam 16:10 dan gambar dipotong secara proporsional tanpa ditarik atau dipipihkan.</small>
                @if($editing)
                    <a class="admin-text-link" href="{{ route('admin.courses.image.edit', $course) }}">Buka halaman khusus Ganti Gambar →</a>
                @endif
                <div class="admin-course-image-preview admin-course-selected-preview" data-course-form-preview hidden>
                    <img alt="Preview gambar yang dipilih" data-course-form-preview-img>
                    <small>Preview gambar baru sebelum disimpan</small>
                </div>
                @error('thumbnail')<small class="admin-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="admin-field span-2"><span>Deskripsi course</span><textarea name="description" rows="6" maxlength="3000" placeholder="Jelaskan pengalaman kelas, kegiatan praktik, dan manfaat untuk anak..." required>{{ old('description', $course->description ?? '') }}</textarea></label>
            <label class="admin-field span-2"><span>Hasil belajar</span><textarea name="learning_outcomes" rows="4" maxlength="3000" placeholder="Contoh: Anak mampu membuat satu karya dan menjelaskan prosesnya.">{{ old('learning_outcomes', $course->learning_outcomes ?? '') }}</textarea></label>
            <label class="admin-field span-2"><span>Perlengkapan / persyaratan</span><textarea name="requirements" rows="4" maxlength="3000" placeholder="Contoh: Bawa botol minum dan alat tulis.">{{ old('requirements', $course->requirements ?? '') }}</textarea></label>
        </div>
    </section>

    <aside class="admin-panel admin-course-form-side">
        <span class="admin-eyebrow">Harga & publikasi</span>
        <h2>Pengaturan course</h2>

        <label class="admin-check-card">
            <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $course->is_free ?? false))>
            <span><strong>Course gratis</strong><small>Jika aktif, harga otomatis menjadi Rp0.</small></span>
        </label>

        <label class="admin-field"><span>Harga normal</span><input type="number" name="price" min="0" step="1000" value="{{ old('price', isset($course) ? (float)$course->price : 0) }}"></label>
        <label class="admin-field"><span>Harga promo</span><input type="number" name="sale_price" min="0" step="1000" value="{{ old('sale_price', isset($course) && $course->sale_price !== null ? (float)$course->sale_price : '') }}" placeholder="Opsional"></label>

        <div class="admin-offline-notice">
            <strong>Mode: OFFLINE</strong>
            <p>Course baru otomatis menggunakan sistem tatap muka. Jadwal dan lokasi kelas dapat diatur melalui menu Jadwal Pengajaran.</p>
        </div>

        <label class="admin-check-card">
            <input type="checkbox" name="certificate_enabled" value="1" @checked(old('certificate_enabled', $course->certificate_enabled ?? true))>
            <span><strong>Sertifikat</strong><small>Aktifkan sertifikat penyelesaian course.</small></span>
        </label>

        <label class="admin-check-card">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published ?? false))>
            <span><strong>Publikasikan</strong><small>Course langsung terlihat oleh orang tua.</small></span>
        </label>

        <div class="admin-form-actions vertical">
            <button class="admin-btn primary full" type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah Course' }}</button>
            <a class="admin-btn ghost full" href="{{ route('admin.courses.index') }}">Batal</a>
        </div>
    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('[data-course-form-thumbnail]');
    const wrap = document.querySelector('[data-course-form-preview]');
    const image = document.querySelector('[data-course-form-preview-img]');
    if (!input || !wrap || !image) return;
    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) { wrap.hidden = true; return; }
        const reader = new FileReader();
        reader.onload = function (event) { image.src = event.target.result; wrap.hidden = false; };
        reader.readAsDataURL(file);
    });
});
</script>
