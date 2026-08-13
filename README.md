# SKILLPATH

SKILLPATH adalah marketplace **kelas non-akademik tatap muka untuk anak usia 5–14 tahun**. Orang tua dapat menemukan program berdasarkan usia dan minat, mendaftarkan anak, menyelesaikan transaksi melalui website, memilih jadwal kelas offline, melihat lokasi dan persiapan, serta memantau kehadiran.

Versi ini merupakan perubahan konsep dari platform course online menjadi platform pendaftaran kelas offline. Fitur live class, meeting URL, recording, video pembelajaran, progres aktivitas online, dan penyelesaian modul oleh siswa sudah dikeluarkan dari alur produk.

## Konsep produk

Alur utama pengguna:

1. Orang tua membuat akun dan profil anak.
2. Sistem merekomendasikan kelas non-akademik sesuai usia dan minat.
3. Orang tua memilih kelas gratis atau berbayar.
4. Untuk kelas berbayar, pembayaran dilakukan melalui alur checkout marketplace.
5. Setelah pendaftaran aktif, orang tua memilih sesi tatap muka yang tersedia.
6. Detail sesi berisi tanggal, waktu, venue, alamat, ruangan/titik temu, kapasitas, tautan peta, dan catatan persiapan.
7. Pengajar mencatat peserta sebagai hadir, tidak hadir, atau dibatalkan.
8. Sertifikat opsional diterbitkan setelah seluruh sesi wajib selesai dan peserta memenuhi kehadiran.

## Jenis kelas

`learning_paths.class_type` mendukung:

- `regular` → Kelas Rutin
- `workshop` → Workshop
- `private` → Privat

`modules` dan `activities` tetap dipertahankan sebagai **rangkaian/program kegiatan offline** yang ditampilkan sebagai gambaran isi kelas. Keduanya bukan lagi aktivitas e-learning yang harus diklik selesai oleh peserta.

## Role

### Parent

- membuat profil anak,
- memilih kelas,
- membeli atau mendaftar kelas gratis,
- menyimpan wishlist,
- melihat kelas yang sudah terdaftar,
- memilih dan membatalkan pemesanan kursi,
- melihat lokasi dan persiapan kelas,
- melihat riwayat transaksi,
- memberi ulasan,
- bertanya kepada pengajar,
- melihat sertifikat yang memenuhi syarat.

### Instructor

- melihat kelas yang diajar,
- mengubah informasi program dan area kelas,
- membuat jadwal tatap muka,
- menentukan venue, alamat, ruangan, peta, kapasitas, dan persiapan,
- melihat daftar peserta,
- mencatat status kehadiran,
- menandai sesi selesai,
- menjawab pertanyaan peserta.

### Admin

- dashboard operasional,
- manajemen kelas, kategori, pengajar, pengguna, pesanan, dan review,
- manajemen jadwal kelas offline,
- monitoring kehadiran peserta,
- laporan pendapatan,
- penerbitan dan manajemen sertifikat,
- statistik platform,
- recycle bin untuk data yang menggunakan soft delete.

## Data offline utama

### `class_sessions`

Menyimpan pelaksanaan fisik sebuah kelas:

- `learning_path_id`
- `instructor_id`
- `title`
- `description`
- `starts_at`
- `ends_at`
- `venue_name`
- `address`
- `room`
- `map_url`
- `capacity`
- `status`: `scheduled`, `completed`, atau `cancelled`
- `preparation_notes`

### `session_bookings`

Menyimpan pemesanan kursi dan kehadiran anak:

- `class_session_id`
- `child_profile_id`
- `status`: `booked`, `attended`, `absent`, atau `cancelled`
- `booked_at`
- `checked_in_at`
- `notes`

Kombinasi `class_session_id` dan `child_profile_id` unik agar satu anak tidak memiliki booking ganda pada sesi yang sama.

## Fitur online yang dihapus

Konsep baru tidak menggunakan:

- live class,
- meeting URL,
- recording URL,
- video promosi/pembelajaran sebagai bagian inti course,
- tipe `self_paced`, `live`, dan `hybrid`,
- masa akses course online,
- progres aktivitas siswa,
- poin aktivitas,
- halaman player/modul pembelajaran,
- aksi peserta untuk menandai aktivitas selesai.

## Struktur utama

```text
app/
├── Http/Controllers/
│   ├── ClassScheduleController.php
│   ├── CourseController.php
│   ├── MyCourseController.php
│   ├── InstructorScheduleController.php
│   └── Admin/
│       ├── AdminAttendanceController.php
│       └── AdminTeachingScheduleController.php
├── Models/
│   ├── LearningPath.php
│   ├── ClassSession.php
│   ├── SessionBooking.php
│   └── Enrollment.php
└── Services/
    ├── AdaptiveLearningService.php
    ├── AttendanceService.php
    └── CertificateService.php

resources/views/
├── classes/
├── courses/
├── my-courses/
├── instructor/
└── admin/
    ├── attendance/
    └── schedules/
```

## Instalasi

Kebutuhan utama:

- PHP 8.3+
- Composer
- MySQL/MariaDB
- Node.js + npm bila ingin menggunakan pipeline Vite

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur koneksi database MySQL di `.env`, kemudian:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

CSS utama aplikasi juga tersedia di `public/css`, sehingga tampilan inti tidak bergantung sepenuhnya pada proses build frontend.

## Data demo

Seeder menyediakan contoh:

- kelas seni/cerita kreatif,
- coding kreatif,
- eksperimen sains,
- public speaking,
- English conversation,
- musik,
- olahraga,
- workshop gratis,
- pengajar,
- parent dan profil anak,
- jadwal tatap muka,
- booking mendatang,
- contoh hadir/tidak hadir,
- pesanan berbayar,
- sertifikat contoh.

## Catatan migrasi dari konsep lama

Database sebaiknya dibuat ulang dengan `php artisan migrate:fresh --seed` saat berpindah dari versi course online ke versi offline ini. Struktur migration lama untuk `progress` dan live learning telah dikeluarkan dan diganti dengan `class_sessions` serta `session_bookings`.

Jika aplikasi lama sudah berisi data produksi, jangan menjalankan `migrate:fresh`. Buat backup terlebih dahulu lalu gunakan `php artisan migrate`. Migration `2026_08_13_000018_convert_online_course_fields_to_offline.php` menangani field katalog lama dan menghapus tabel progres, sedangkan migration offline session memindahkan jadwal/booking lama ke struktur kelas tatap muka. Jadwal yang berasal dari live class diberi lokasi placeholder `Lokasi perlu diperbarui`, sehingga admin wajib mengisi venue dan alamat yang benar sebelum jadwal digunakan.

Dokumentasi konsep lebih rinci tersedia di folder `docs/`.
