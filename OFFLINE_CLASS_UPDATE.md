# SKILLPATH — Offline Class Update

Versi ini mengubah tampilan dan alur utama SKILLPATH agar konsisten sebagai marketplace **kelas nonakademik tatap muka/offline untuk anak usia 5–14 tahun**.

Perubahan utama:
- Landing page tidak lagi menggunakan istilah video/live class/kelas online.
- Navigasi publik menggunakan istilah **Kelas** dan **Jadwal Kelas**.
- Hero menampilkan konsep **Tatap Muka · Praktik · Proyek**.
- Halaman pencarian, detail kelas, dashboard orang tua, kelas saya, dan jadwal diperbarui untuk konteks offline.
- Jadwal kelas sekarang mendukung **lokasi fisik** dan tautan petunjuk lokasi/Google Maps.
- Form jadwal admin diperbarui untuk memasukkan lokasi kelas.
- Data demo/seeder diperbarui agar tidak lagi menggunakan Google Meet atau persyaratan kelas online.

## Setelah menimpa patch pada project lama

Jalankan migrasi berikut agar kolom `location` ditambahkan ke tabel `live_sessions`:

```bash
php artisan migrate
```

Route, nama model, dan field internal lama seperti `live_sessions` / `meeting_url` tetap dipertahankan agar fitur yang sudah ada tidak rusak. Pada antarmuka, `meeting_url` digunakan sebagai tautan petunjuk lokasi (misalnya Google Maps).
