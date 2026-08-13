# Monitoring Progres Siswa

Fitur ini tersedia untuk role `admin` melalui:

```text
/admin/progres-siswa
```

## Fungsi

Admin dapat:

- melihat seluruh siswa yang memiliki enrollment aktif,
- melihat jumlah course aktif,
- melihat aktivitas selesai dan total aktivitas,
- melihat persentase progres,
- melihat total poin,
- melihat rata-rata nilai,
- melihat waktu aktivitas terakhir,
- memfilter berdasarkan course,
- memfilter berdasarkan status progres,
- mencari siswa berdasarkan nama, orang tua, atau email,
- membuka detail progres per siswa,
- melihat progres per course,
- melihat 20 aktivitas terakhir,
- mengekspor hasil monitoring ke CSV.

## Status Monitoring

### Aktif

Siswa sudah menyelesaikan aktivitas dan aktivitas terakhir masih berada dalam 14 hari terakhir.

### Belum mulai

Siswa memiliki enrollment, tetapi belum menyelesaikan aktivitas dan usia enrollment belum lebih dari 7 hari.

### Perlu perhatian

Siswa memenuhi salah satu kondisi:

- belum menyelesaikan aktivitas setelah lebih dari 7 hari sejak enrollment, atau
- aktivitas terakhir lebih dari 14 hari yang lalu dan progres belum 100%.

### Selesai

Seluruh aktivitas pada course aktif sudah selesai.

## Data Demo Seeder

Seeder menyediakan tiga pola data untuk memudahkan pengujian admin:

- `Alya`: progres aktif pada beberapa course,
- `Bima`: enrollment lama tanpa aktivitas sehingga masuk status perlu perhatian,
- `Citra`: satu course selesai 100%.

Akun orang tua demo menggunakan password:

```text
password
```

## File Utama

```text
app/Http/Controllers/Admin/AdminStudentProgressController.php
app/Services/StudentProgressService.php
resources/views/admin/progress/index.blade.php
resources/views/admin/progress/show.blade.php
resources/views/admin/layouts/app.blade.php
public/css/admin.css
routes/web.php
```

Fitur ini tidak membutuhkan migration baru karena data monitoring dihitung dari tabel `child_profiles`, `enrollments`, `progress`, `activities`, `modules`, dan `learning_paths`.
