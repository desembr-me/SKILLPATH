@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal | Admin SKILLPATH')
@section('page-title', 'Tambah Jadwal Pengajaran')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Jadwal Pengajaran</a>
</div>

<section class="admin-section-card admin-form-card">
    <x-admin.section-header
        eyebrow="Jadwal Baru"
        title="Tambah sesi pengajaran"
        description="Tentukan course, waktu, kapasitas, dan tautan kelas. Sistem akan memeriksa benturan jadwal pengajar."
    />

    @php($selectedCourseId = null)
    @php($formTitle = '')
    @php($formStartsAt = '')
    @php($formEndsAt = '')
    @php($formCapacity = 30)
    @php($formStatus = 'scheduled')
    @php($formMeetingUrl = '')
    @php($formRecordingUrl = '')
    @php($formDescription = '')

    <form method="POST" action="{{ route('admin.schedules.store') }}">
        @csrf

<div class="admin-form-grid">
    <label class="admin-form-field full">
        <span>Course</span>
        <select name="learning_path_id" required>
            <option value="">Pilih course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('learning_path_id', $selectedCourseId) === (string) $course->id)>
                    {{ $course->title }} · {{ $course->instructor?->name ?? 'Belum ada pengajar' }}
                </option>
            @endforeach
        </select>
        <small>Pengajar otomatis mengikuti pengajar yang terhubung ke course.</small>
        @error('learning_path_id') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Judul sesi</span>
        <input type="text" name="title" value="{{ old('title', $formTitle) }}" maxlength="150" required placeholder="Contoh: Live Class Coding Visual 1">
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
            @foreach(['scheduled' => 'Scheduled', 'live' => 'Live', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $formStatus) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Meeting URL</span>
        <input type="url" name="meeting_url" value="{{ old('meeting_url', $formMeetingUrl) }}" placeholder="https://meet.google.com/...">
        @error('meeting_url') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Recording URL</span>
        <input type="url" name="recording_url" value="{{ old('recording_url', $formRecordingUrl) }}" placeholder="Opsional">
        @error('recording_url') <span class="admin-field-error">{{ $message }}</span> @enderror
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
