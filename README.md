# SKILLPATH

**SKILLPATH: Platform Upskilling Nonakademik Berbasis User-Centered Design dengan Jalur Belajar Adaptif Sesuai Minat Anak Usia 5–14 Tahun**

Project ini disusun langsung mengikuti struktur aplikasi Laravel, bukan lagi dalam folder `overlay`.

## Struktur Utama

```text
skillpath/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   └── js/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
```

Folder `vendor/` dan `node_modules/` tidak disertakan karena keduanya dibuat oleh dependency manager.

## Persyaratan

- PHP 8.3+
- Composer
- MySQL
- Node.js dan npm hanya diperlukan jika Anda ingin memakai Vite

## Instalasi

### 1. Ekstrak project

Masuk ke folder:

```bash
cd skillpath
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Buat file environment

Linux/macOS:

```bash
cp .env.example .env
```

Windows:

```powershell
copy .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Buat database MySQL

```sql
CREATE DATABASE skillpath
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### 6. Periksa `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skillpath
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Migrasi dan seeder

```bash
php artisan migrate
php artisan db:seed
```

### 8. Jalankan server

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

## Fitur MVP

- Landing page ramah anak
- Register dan login orang tua
- Profil anak usia 5–14 tahun
- Pemilihan minat
- Jalur belajar nonakademik
- Modul dan aktivitas
- Penyimpanan progres
- Poin belajar
- Dashboard anak
- Rekomendasi adaptif berbasis usia, minat, dan progres

## File Penting SKILLPATH

```text
app/Services/AdaptiveLearningService.php
routes/web.php
database/seeders/DatabaseSeeder.php
resources/views/home.blade.php
resources/views/dashboard.blade.php
public/css/skillpath.css
```

## Catatan

CSS utama MVP ditempatkan di `public/css/skillpath.css` agar tampilan dapat berjalan tanpa `npm run dev`.

Jika ingin memindahkan frontend ke Vite, pindahkan atau import stylesheet tersebut melalui `resources/css/app.css`, lalu gunakan `@vite(...)` pada layout.


## Fitur Kategori

SKILLPATH sekarang memiliki lima kategori utama:

1. Arts
2. Languages
3. Music
4. Sports
5. Technology

> Penulisan `tecnology` dinormalisasi menjadi `Technology`.

Struktur database kategori:

```text
categories
    ↓ many-to-many
category_learning_path
    ↓
learning_paths
```

Route kategori:

```text
GET /kategori
GET /kategori/{category}
```

File utama:

```text
app/Models/Category.php
app/Http/Controllers/CategoryController.php
database/migrations/2026_08_13_000009_create_categories_table.php
resources/views/categories/index.blade.php
resources/views/categories/show.blade.php
```

Jika database sebelumnya sudah pernah dimigrasikan, jalankan:

```bash
php artisan migrate
php artisan db:seed
```

Untuk mengulang seluruh data pengembangan dari awal:

```bash
php artisan migrate:fresh --seed
```

## Fitur Navbar Lengkap

Navbar sekarang menggunakan halaman dan route khusus:

```text
/                  Beranda
/kategori          Kategori
/jelajah-jalur     Jelajah Jalur + filter
/cara-kerja        Penjelasan alur adaptif dan UCD
/untuk-orang-tua   Fitur dan ringkasan progres orang tua
/dashboard         Dashboard pengguna yang sudah login
/login             Login
/register          Registrasi
```

### Jelajah Jalur

Mendukung filter:

- kata kunci
- kategori
- usia 5–14 tahun
- kelompok skill

### Untuk Orang Tua

Jika pengguna sudah login dan memiliki profil anak, halaman menampilkan:

- jumlah aktivitas selesai
- total poin
- jumlah minat aktif
- tombol menuju dashboard
- tombol mengubah profil dan minat

### Perbaikan Relasi Minat

Relasi `ChildProfile` dan `Interest` secara eksplisit menggunakan tabel pivot `child_interest` agar sesuai dengan migration.


## Upgrade Marketplace Course & Pengajar

Versi ini mengembangkan SKILLPATH menjadi marketplace course nonakademik untuk anak usia 5–14 tahun.

### Fitur pengguna/orang tua
- katalog course dan filter usia/kategori/tipe/harga
- detail course, kurikulum, harga promo, rating, pengajar
- wishlist dan keranjang
- checkout dan simulasi pembayaran
- riwayat pesanan
- enrollment course untuk profil anak
- Course Saya dan progres
- live class dan booking kursi
- tanya pengajar
- review course
- sertifikat setelah seluruh aktivitas selesai

### Fitur pengajar
- profil pengajar publik
- dashboard pengajar
- statistik course dan peserta
- edit harga, tipe course, hasil belajar, persyaratan
- membuat dan menghapus live class
- menjawab pertanyaan peserta

### Akun demo seeder
- Orang tua: `parent@skillpath.test` / `password`
- Pengajar: `naila@skillpath.test` / `password`

### Menjalankan migrasi
Jika masih development dan data lama boleh dihapus:

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan serve
```

Checkout pada starter ini masih berupa simulasi. Untuk produksi, sambungkan `CheckoutController` ke payment gateway seperti Midtrans/Xendit dan validasi callback server-to-server sebelum membuat enrollment aktif.


## Admin Panel

Akses administrator:

```text
URL      : /admin
Email    : admin@skillpath.test
Password : password
```

Fitur admin:

- Dashboard statistik platform
- Manajemen publikasi course
- Verifikasi pengajar
- Daftar pengguna dan peran
- Monitoring pesanan
- Pembaruan status transaksi
- Manajemen kategori
- Moderasi review

Setelah mengambil versi ini, gunakan:

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan serve
```

## Recycle Bin Admin

Admin memiliki fitur Recycle Bin pada:

```text
/admin/recycle-bin
```

Data yang mendukung Soft Delete:

- Course
- Kategori
- Pengguna/Pengajar
- Review

Admin dapat memulihkan data, memulihkan semua data, atau menghapus permanen data yang aman untuk dihapus. Course dan pengguna yang memiliki riwayat transaksi/enrollment dilindungi dari penghapusan permanen.

Setelah menambahkan fitur ini ke project lama, jalankan:

```bash
php artisan migrate
php artisan optimize:clear
```

Dokumentasi lengkap tersedia di `docs/RECYCLE_BIN.md`.


## Monitoring Progres Siswa

Admin dapat membuka `/admin/progres-siswa` untuk memantau progres pembelajaran siswa, memfilter berdasarkan course dan status, melihat detail per course, serta mengekspor data ke CSV. Dokumentasi lengkap tersedia di `docs/MONITORING_PROGRESS.md`.

## Fitur Admin: Jadwal Pengajaran dan Laporan Pendapatan

Admin sekarang memiliki dua fitur operasional tambahan.

### Jadwal Pengajaran

```text
/admin/jadwal-pengajaran
```

Admin dapat memantau, memfilter, membuat, mengubah, membatalkan, dan mengekspor jadwal live class.

### Laporan Pendapatan

```text
/admin/laporan-pendapatan
```

Admin dapat menganalisis transaksi `PAID` berdasarkan rentang tanggal, course, pengajar, dan metode pembayaran. Laporan menyediakan ringkasan pendapatan, diskon, jumlah penjualan, tren, kontribusi course, kontribusi pengajar, dan ekspor CSV.

Kedua fitur menggunakan tabel yang sudah ada sehingga tidak membutuhkan migration baru.

Dokumentasi lengkap:

```text
docs/ADMIN_SCHEDULE_REVENUE.md
```


## Manajemen Sertifikat Admin

Admin dapat mengelola sertifikat melalui:

```text
/admin/sertifikat
```

Fitur:

- penerbitan sertifikat untuk siswa yang sudah menyelesaikan course,
- status aktif/dicabut,
- alasan pencabutan,
- aktivasi ulang,
- cetak,
- pencarian dan filter,
- ekspor CSV.

## Statistik Platform Admin

Admin dapat membuka:

```text
/admin/statistik-platform
```

Statistik mencakup pengguna, siswa, enrollment, aktivitas belajar, pendapatan, sertifikat, rating, live class, distribusi usia, popularitas kategori, course, dan pengajar.

Dokumentasi lengkap:

```text
docs/ADMIN_CERTIFICATES_STATISTICS.md
```
