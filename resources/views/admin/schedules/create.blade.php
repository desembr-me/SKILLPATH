@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal | Admin SKILLPATH')
@section('page-title', 'Tambah Jadwal Kelas')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Jadwal Kelas</a>
</div>

<section class="admin-section-card admin-form-card">
    <x-admin.section-header
        eyebrow="Jadwal Baru"
        title="Tambah sesi kelas tatap muka"
        description="Tentukan kelas, waktu, kapasitas, dan lokasi. Sistem akan memeriksa benturan jadwal pengajar."
    />

    @php($selectedCourseId = null)
    @php($formTitle = '')
    @php($formStartsAt = '')
    @php($formEndsAt = '')
    @php($formCapacity = 30)
    @php($formStatus = 'scheduled')
    @php($formVenueName = '')
    @php($formAddress = '')
    @php($formDescription = '')

    <form method="POST" action="{{ route('admin.schedules.store') }}">
        @csrf

<div class="admin-form-grid">
    <label class="admin-form-field full">
        <span>Kelas</span>
        <select name="learning_path_id" required>
            <option value="">Pilih kelas</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('learning_path_id', $selectedCourseId) === (string) $course->id)>
                    {{ $course->title }} · {{ $course->instructor?->name ?? 'Belum ada pengajar' }}
                </option>
            @endforeach
        </select>
        <small>Pengajar otomatis mengikuti pengajar yang terhubung ke kelas.</small>
        @error('learning_path_id') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Judul sesi</span>
        <input type="text" name="title" value="{{ old('title', $formTitle) }}" maxlength="150" required placeholder="Contoh: Kelas Coding Visual 1">
        @error('title') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Mulai</span>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $formStartsAt) }}" required>
        @error('starts_at') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Selesai</span>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $formEndsAt) }}" required>
        @error('ends_at') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Kapasitas peserta</span>
        <input type="number" name="capacity" min="1" max="500" value="{{ old('capacity', $formCapacity) }}" required>
        @error('capacity') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Status</span>
        <select name="status" required>
            @foreach(['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $formStatus) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Tempat / venue</span>
        <input type="text" name="venue_name" value="{{ old('venue_name', $formVenueName) }}" placeholder="Nama studio, lapangan, sanggar, dll." required>
        @error('venue_name') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Alamat lengkap</span>
        <input type="text" name="address" value="{{ old('address', $formAddress) }}" placeholder="Alamat lokasi kelas" required>
        @error('address') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Deskripsi</span>
        <textarea name="description" rows="5" maxlength="2000" placeholder="Tujuan sesi, materi, atau catatan untuk pengajar.">{{ old('description', $formDescription) }}</textarea>
        @error('description') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>
</div>

        <div class="admin-form-actions">
            <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">Batal</a>
            <button class="admin-btn primary" type="submit">Simpan Jadwal</button>
        </div>
    </form>
</section>
@endsection
