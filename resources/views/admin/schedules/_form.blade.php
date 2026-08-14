@php
    $editing = isset($liveSession);
@endphp

<div class="schedule-form-grid">
    <label class="admin-form-field full">
        <span>Kelas</span>
        <select name="learning_path_id" required>
            <option value="">Pilih kelas</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('learning_path_id', $editing ? $liveSession->learning_path_id : '') === (string) $course->id)>
                    {{ $course->title }} · {{ $course->instructor?->name ?? 'Belum ada pengajar' }}
                </option>
            @endforeach
        </select>
        <small>Pengajar sesi akan mengikuti pengajar yang terdaftar pada kelas.</small>
    </label>

    <label class="admin-form-field full">
        <span>Judul sesi</span>
        <input type="text" name="title" maxlength="150" value="{{ old('title', $editing ? $liveSession->title : '') }}" placeholder="Contoh: Kelas Tatap Muka 1 - Coding Visual" required>
    </label>

    <label class="admin-form-field">
        <span>Mulai</span>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $editing ? $liveSession->starts_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="admin-form-field">
        <span>Selesai</span>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $editing ? $liveSession->ends_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="admin-form-field full">
        <span>Lokasi kelas</span>
        <input type="text" name="location" maxlength="255" value="{{ old('location', $editing ? $liveSession->location : '') }}" placeholder="Contoh: Studio Kreatif Menteng, Jakarta Pusat">
    </label>

    <label class="admin-form-field">
        <span>Kapasitas peserta</span>
        <input type="number" name="capacity" min="1" max="500" value="{{ old('capacity', $editing ? $liveSession->capacity : 20) }}" required>
    </label>

    <label class="admin-form-field">
        <span>Status</span>
        <select name="status" required>
            @foreach(['scheduled'=>'Terjadwal','live'=>'Berlangsung','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $editing ? $liveSession->status : 'scheduled') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label class="admin-form-field full">
        <span>Tautan Lokasi (Google Maps)</span>
        <input type="url" name="meeting_url" value="{{ old('meeting_url', $editing ? $liveSession->meeting_url : '') }}" placeholder="https://maps.google.com/...">
    </label>

    <label class="admin-form-field full">
        <span>Tautan Dokumentasi (opsional)</span>
        <input type="url" name="recording_url" value="{{ old('recording_url', $editing ? $liveSession->recording_url : '') }}" placeholder="https://...">
    </label>

    <label class="admin-form-field full">
        <span>Deskripsi</span>
        <textarea name="description" rows="5" maxlength="2000" placeholder="Agenda kelas, materi yang dibahas, atau catatan untuk sesi.">{{ old('description', $editing ? $liveSession->description : '') }}</textarea>
    </label>
</div>
