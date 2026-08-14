@extends('admin.layouts.app')

@section('title', 'Edit Jadwal | Admin SKILLPATH')
@section('page-title', 'Edit Jadwal Pengajaran')

@section('content')
<div class="admin-page-toolbar">
    <a class="admin-btn ghost" href="{{ route('admin.schedules.index') }}">← Jadwal Pengajaran</a>
    <span class="admin-status {{ $liveSession->status }}">{{ ['scheduled'=>'TERJADWAL','live'=>'BERLANGSUNG','completed'=>'SELESAI','cancelled'=>'DIBATALKAN'][$liveSession->status] ?? strtoupper($liveSession->status) }}</span>
</div>

<section class="admin-section-card admin-form-card">
    <x-admin.section-header
        eyebrow="Perbarui Sesi"
        title="Edit jadwal pengajaran"
        description="Perubahan waktu akan divalidasi agar tidak bertabrakan dengan jadwal pengajar lainnya."
    />

    @php($selectedCourseId = $liveSession->learning_path_id)
    @php($formTitle = $liveSession->title)
    @php($formStartsAt = $liveSession->starts_at?->format('Y-m-d\TH:i'))
    @php($formEndsAt = $liveSession->ends_at?->format('Y-m-d\TH:i'))
    @php($formLocation = $liveSession->location)
    @php($formCapacity = $liveSession->capacity)
    @php($formStatus = $liveSession->status)
    @php($formMeetingUrl = $liveSession->meeting_url)
    @php($formRecordingUrl = $liveSession->recording_url)
    @php($formDescription = $liveSession->description)

    <form method="POST" action="{{ route('admin.schedules.update', $liveSession) }}">
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
        <input type="text" name="title" value="{{ old('title', $formTitle) }}" maxlength="150" required placeholder="Contoh: Kelas Tatap Muka Coding Visual 1">
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

    <label class="admin-form-field full">
        <span>Lokasi kelas</span>
        <input type="text" name="location" value="{{ old('location', $formLocation) }}" maxlength="255" placeholder="Contoh: Studio Kreatif Menteng, Jakarta Pusat">
        <small>Masukkan nama tempat dan alamat singkat agar orang tua mudah menemukan lokasi kelas.</small>
        @error('location') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Kapasitas peserta</span>
        <input type="number" name="capacity" min="1" max="500" value="{{ old('capacity', $formCapacity) }}" required>
        @error('capacity') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field">
        <span>Status</span>
        <select name="status" required>
            @foreach(['scheduled' => 'Terjadwal', 'live' => 'Berlangsung', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $formStatus) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Tautan Lokasi (Google Maps)</span>
        <input type="url" name="meeting_url" value="{{ old('meeting_url', $formMeetingUrl) }}" placeholder="https://maps.google.com/...">
        @error('meeting_url') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Tautan Dokumentasi (opsional)</span>
        <input type="url" name="recording_url" value="{{ old('recording_url', $formRecordingUrl) }}" placeholder="Opsional">
        @error('recording_url') <span class="admin-field-error">{{ $message }}</span> @enderror
    </label>

    <label class="admin-form-field full">
        <span>Deskripsi</span>
        <textarea name="description" rows="5" maxlength="2000" placeholder="Tujuan sesi, aktivitas, perlengkapan, atau catatan untuk peserta.">{{ old('description', $formDescription) }}</textarea>
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
