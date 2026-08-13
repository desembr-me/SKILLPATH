# Fitur Admin: Jadwal Pengajaran dan Laporan Pendapatan

Dokumen ini menjelaskan dua fitur tambahan untuk role `admin` pada SKILLPATH.

## 1. Jadwal Pengajaran

Route utama:

```text
/admin/jadwal-pengajaran
```

Fitur:

- melihat seluruh live session,
- mencari berdasarkan judul sesi, course, atau pengajar,
- filter course,
- filter pengajar,
- filter status,
- filter periode,
- filter rentang tanggal,
- melihat booking dan kapasitas,
- membuka meeting URL,
- membuat jadwal,
- mengubah jadwal,
- membatalkan jadwal,
- ekspor CSV.

Status sesi:

```text
scheduled
live
completed
cancelled
```

Saat admin membuat atau mengubah jadwal, pengajar sesi mengikuti `instructor_id` milik course yang dipilih. Cara ini mencegah jadwal menggunakan pengajar yang berbeda dari pengajar utama course tanpa model pengajar tamu yang eksplisit.

Data menggunakan tabel yang sudah tersedia:

```text
live_sessions
session_bookings
learning_paths
users
```

Tidak diperlukan migration baru.

## 2. Laporan Pendapatan

Route utama:

```text
/admin/laporan-pendapatan
```

Laporan hanya menghitung transaksi dengan:

```text
orders.payment_status = paid
```

Filter:

- tanggal mulai,
- tanggal akhir,
- course,
- pengajar,
- metode pembayaran.

Metrik:

- pendapatan penjualan,
- jumlah pesanan dibayar,
- jumlah course terjual,
- rata-rata nilai pesanan,
- total diskon,
- harga normal sebelum diskon,
- tren pendapatan,
- pendapatan per course,
- kontribusi course per pengajar,
- komposisi metode pembayaran,
- detail transaksi.

Ekspor:

```text
/admin/laporan-pendapatan/export
```

CSV mengikuti filter yang sedang digunakan.

### Definisi pendapatan

Nilai pendapatan saat ini berasal dari:

```text
SUM(order_items.final_price)
```

untuk item yang berada pada order berstatus `paid`.

Laporan ini merupakan laporan penjualan marketplace. Sistem belum menghitung:

- komisi platform,
- bagi hasil pengajar,
- biaya payment gateway,
- pajak,
- refund parsial.

Jika mekanisme tersebut ditambahkan, gunakan tabel ledger atau payout terpisah agar laporan keuangan tidak hanya bergantung pada nilai order.

## 3. Dashboard Admin

Dashboard admin sekarang menampilkan:

- pendapatan bulan berjalan,
- pendapatan keseluruhan,
- jumlah jadwal hari ini,
- jumlah jadwal akan datang,
- lima jadwal terdekat,
- shortcut menuju Jadwal Pengajaran,
- shortcut menuju Laporan Pendapatan.

## 4. Seeder Demo

Seeder menyediakan contoh:

- sesi yang sudah selesai,
- sesi pada hari ini,
- beberapa sesi akan datang,
- transaksi paid dengan QRIS,
- Virtual Account,
- E-Wallet,
- Transfer Bank,
- transaksi pada bulan berjalan dan bulan sebelumnya.

Untuk memuat data demo dari awal:

```bash
php artisan migrate:fresh --seed
```

Jika database berisi data yang harus dipertahankan, jangan menjalankan `migrate:fresh`.

## 5. File Utama

```text
app/Http/Controllers/Admin/AdminTeachingScheduleController.php
app/Http/Controllers/Admin/AdminRevenueReportController.php
resources/views/admin/schedules/index.blade.php
resources/views/admin/schedules/create.blade.php
resources/views/admin/schedules/edit.blade.php
resources/views/admin/schedules/_form.blade.php
resources/views/admin/revenue/index.blade.php
resources/views/admin/layouts/app.blade.php
resources/views/admin/dashboard.blade.php
public/css/admin.css
routes/web.php
database/seeders/DatabaseSeeder.php
```
