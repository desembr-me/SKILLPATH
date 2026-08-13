@extends('admin.layouts.app')

@section('title', 'Edit Jadwal | Admin SKILLPATH')
@section('page-title', 'Edit Jadwal Kelas')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Jadwal Kelas</a>
    <span class="admin-status {{ $classSession->status }}">{{ strtoupper($classSession->status) }}</span>
</div>

<section class="admin-section-card admin-form-card">
    <x-admin.section-header
        eyebrow="Perbarui Sesi"
        title="Edit jadwal kelas tatap muka"
        description="Perubahan waktu akan divalidasi agar tidak bertabrakan dengan jadwal pengajar lainnya."
    />

    @php($selectedCourseId = $classSession->learning_path_id)
    @php($formTitle = $classSession->title)
    @php($formStartsAt = $classSession->starts_at?->format('Y-m-d\TH:i'))
    @php($formEndsAt = $classSession->ends_at?->format('Y-m-d\TH:i'))
    @php($formCapacity = $classSession->capacity)
    @php($formStatus = $classSession->status)
    @php($formVenueName = $classSession->venue_name)
    @php($formAddress = $classSession->address)
    @php($formDescription = $classSession->description)

    <form method="POST" action="{{ route('admin.schedules.update', $classSession) }}">
        @csrf
        @method('PUT')

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
        <input type="text" name="title" value="{{ old('title', $formTitle) }}" maxlength="150" required placeholder="Contoh: Workshop Coding Kreatif">
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
            @foreach(['scheduled' => 'Terjadwal', 'live' => 'Sedang berlangsung', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $formStatus) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Tempat / venue</span>
        <input type="text" name="venue_name" value="{{ old('venue_name', $formVenueName) }}" placeholder="Contoh: Studio Kreatif Makassar" required>
        @error('venue_name') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Alamat lengkap</span>
        <input type="text" name="address" value="{{ old('address', $formAddress) }}" placeholder="Alamat lokasi kelas" required>
        @error('address') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Deskripsi</span>
        <textarea name="description" rows="5" maxlength="2000" placeholder="Tujuan sesi, kegiatan, atau catatan untuk pengajar dan peserta.">{{ old('description', $formDescription) }}</textarea>
        @error('description') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>
</div>

        <div class="admin-form-actions">
            <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">Batal</a>
            <button class="admin-btn primary" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</section>
@endsection
