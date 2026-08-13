@php($editing = isset($classSession))
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
        <small>Pengajar sesi mengikuti pengajar yang terdaftar pada kelas.</small>
    </label>

    <label class="admin-form-field full"><span>Judul sesi</span><input type="text" name="title" maxlength="150" value="{{ old('title', $editing ? $classSession->title : '') }}" placeholder="Contoh: Pertemuan 1 - Studio Cerita" required></label>
    <label class="admin-form-field"><span>Mulai</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $editing ? $classSession->starts_at?->format('Y-m-d\\TH:i') : '') }}" required></label>
    <label class="admin-form-field"><span>Selesai</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $editing ? $classSession->ends_at?->format('Y-m-d\\TH:i') : '') }}" required></label>
    <label class="admin-form-field"><span>Kapasitas peserta</span><input type="number" name="capacity" min="1" max="500" value="{{ old('capacity', $editing ? $classSession->capacity : 20) }}" required></label>
    <label class="admin-form-field"><span>Status</span><select name="status" required>@foreach(['scheduled'=>'Terjadwal','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $value=>$label)<option value="{{ $value }}" @selected(old('status', $editing ? $classSession->status : 'scheduled') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label class="admin-form-field full"><span>Nama lokasi</span><input type="text" name="venue_name" maxlength="180" value="{{ old('venue_name', $editing ? $classSession->venue_name : '') }}" placeholder="Contoh: Ruang Kreatif SKILLPATH Panakkukang" required></label>
    <label class="admin-form-field full"><span>Alamat lengkap</span><textarea name="address" rows="3" maxlength="1000" required placeholder="Alamat tempat kelas berlangsung">{{ old('address', $editing ? $classSession->address : '') }}</textarea></label>
    <label class="admin-form-field"><span>Ruangan / titik temu</span><input type="text" name="room" maxlength="100" value="{{ old('room', $editing ? $classSession->room : '') }}" placeholder="Studio 2 / Lobby"></label>
    <label class="admin-form-field"><span>Link peta</span><input type="url" name="map_url" value="{{ old('map_url', $editing ? $classSession->map_url : '') }}" placeholder="https://maps.google.com/..."></label>
    <label class="admin-form-field full"><span>Deskripsi sesi</span><textarea name="description" rows="4" maxlength="2000" placeholder="Agenda dan aktivitas utama kelas.">{{ old('description', $editing ? $classSession->description : '') }}</textarea></label>
    <label class="admin-form-field full"><span>Catatan persiapan peserta</span><textarea name="preparation_notes" rows="4" maxlength="2000" placeholder="Pakaian, perlengkapan, waktu datang, atau ketentuan lokasi.">{{ old('preparation_notes', $editing ? $classSession->preparation_notes : '') }}</textarea></label>
</div>
