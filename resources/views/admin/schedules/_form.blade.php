@php
    $editing = isset($classSession);
@endphp

<div class="schedule-form-grid">
    <label class="admin-form-field full">
        <span>Kelas</span>
        <select name="learning_path_id" required>
            <option value="">Pilih kelas</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('learning_path_id', $editing ? $classSession->learning_path_id : '') === (string) $course->id)>
                    {{ $course->title }} · {{ $course->instructor?->name ?? 'Belum ada pengajar' }}
                </option>
            @endforeach
        </select>
        <small>Pengajar sesi akan mengikuti pengajar yang terdaftar pada kelas.</small>
    </label>

    <label class="admin-form-field full">
        <span>Judul sesi</span>
        <input type="text" name="title" maxlength="150" value="{{ old('title', $editing ? $classSession->title : '') }}" placeholder="Contoh: Kelas 1 - Coding Visual" required>
    </label>

    <label class="admin-form-field">
        <span>Mulai</span>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $editing ? $classSession->starts_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="admin-form-field">
        <span>Selesai</span>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $editing ? $classSession->ends_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="admin-form-field">
        <span>Kapasitas peserta</span>
        <input type="number" name="capacity" min="1" max="500" value="{{ old('capacity', $editing ? $classSession->capacity : 20) }}" required>
    </label>

    <label class="admin-form-field">
        <span>Status</span>
        <select name="status" required>
            @foreach(['scheduled'=>'Scheduled','completed'=>'Completed','cancelled'=>'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $editing ? $classSession->status : 'scheduled') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label class="admin-form-field full">
        <span>Tempat / venue</span>
        <input type="text" name="venue_name" value="{{ old('venue_name', $editing ? $classSession->venue_name : '') }}" required>
    </label>

    <label class="admin-form-field full">
        <span>Alamat lengkap</span>
        <input type="text" name="address" value="{{ old('address', $editing ? $classSession->address : '') }}" required>
    </label>

    <label class="admin-form-field full">
        <span>Deskripsi</span>
        <textarea name="description" rows="5" maxlength="2000" placeholder="Agenda kelas, materi yang dibahas, atau catatan untuk sesi.">{{ old('description', $editing ? $classSession->description : '') }}</textarea>
    </label>
</div>
