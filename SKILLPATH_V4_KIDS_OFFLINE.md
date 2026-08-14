# SKILLPATH V4 — Kids Offline Course Marketplace

Pembaruan ini mengarahkan SKILLPATH secara konsisten sebagai marketplace **kelas nonakademik offline/tatap muka untuk anak usia 5–14 tahun**.

## Perubahan utama

- Landing page dibuat lebih playful dan menarik untuk anak tanpa mengganti palet warna asli SKILLPATH.
- Katalog dikunci menjadi 6 kategori:
  1. Arts
  2. Music
  3. Self Improvement
  4. Languages
  5. Sports
  6. Technology
- Setiap kategori menggunakan 3 level tetap:
  - Beginner
  - Intermediate
  - Expert
- Filter level ditambahkan pada halaman pencarian kelas dan Admin Course.
- Halaman kategori memisahkan course berdasarkan tiga level tersebut.
- Seeder demo menyediakan course pada ketiga level di seluruh 6 kategori.
- Profil pengajar sekarang mendukung foto profil publik.
- Pengajar dapat mengunggah/mengganti/menghapus foto profil dari **Dashboard Pengajar → Profil Saya**.
- Foto pengajar tampil pada landing page, daftar pengajar, profil pengajar, detail course, dan daftar pengajar Admin.
- Admin dapat menambahkan dan mengedit course melalui **Admin → Course → Tambah Course**.
- Course yang dibuat Admin otomatis berformat **offline** dan memilih satu dari 6 kategori serta salah satu dari 3 level.
- Enam kategori inti tidak dapat ditambah/dihapus melalui Admin agar struktur katalog tetap konsisten.

## Setelah mengganti project lama

Jalankan:

```bash
php artisan migrate
```

Jika ingin memuat ulang data demo lengkap (18+ course demo lintas kategori/level), gunakan database development yang aman lalu jalankan seeder sesuai workflow project Anda.

## Upload foto pengajar

Foto disimpan pada:

```text
public/uploads/instructors/
```

Format yang diterima: JPG, JPEG, PNG, WebP. Maksimal 2 MB.

## Catatan pengujian

- Seluruh file PHP yang diubah telah lolos `php -l`.
- Seluruh 64 Blade view berhasil dikompilasi dan hasil kompilasinya lolos `php -l`.
- Migrasi runtime tidak dapat dijalankan penuh pada environment pembuatan karena PDO SQLite driver tidak tersedia. Jalankan `php artisan migrate` pada environment Laravel Anda yang memiliki driver database sesuai `.env`.
