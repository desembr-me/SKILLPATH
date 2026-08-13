# Fitur Admin: Manajemen Sertifikat dan Statistik Platform

## 1. Manajemen Sertifikat

Route utama:

```text
/admin/sertifikat
```

Fungsi:

- daftar seluruh sertifikat,
- pencarian nomor sertifikat, siswa, orang tua, dan course,
- filter status, course, dan tanggal terbit,
- menerbitkan sertifikat untuk siswa yang memenuhi syarat,
- melihat detail sertifikat,
- mencetak sertifikat,
- mencabut sertifikat disertai alasan,
- mengaktifkan kembali sertifikat,
- ekspor CSV.

### Aturan penerbitan

Sertifikat hanya dapat diterbitkan jika:

1. siswa memiliki enrollment aktif,
2. fitur sertifikat pada course aktif,
3. course mempunyai aktivitas,
4. seluruh aktivitas course sudah berstatus `completed`,
5. belum ada sertifikat untuk kombinasi siswa dan course tersebut.

### Status

- `active`: sertifikat berlaku.
- `revoked`: sertifikat dicabut admin dan tidak dapat dibuka siswa sampai diaktifkan kembali.

Migration baru:

```text
2026_08_13_000017_add_admin_management_fields_to_certificates_table.php
```

Kolom baru:

```text
status
issued_by
revoked_at
revoked_reason
```

## 2. Statistik Platform

Route:

```text
/admin/statistik-platform
```

Periode:

- 7 hari,
- 30 hari,
- 90 hari,
- 12 bulan,
- semua waktu.

Indikator:

- total pengguna,
- orang tua,
- pengajar,
- profil siswa,
- course aktif,
- enrollment,
- siswa aktif,
- aktivitas selesai,
- pendapatan,
- pesanan dibayar,
- sertifikat aktif,
- completion rate,
- rating platform,
- pengguna baru,
- enrollment baru,
- live class,
- booking live class,
- keterisian live class,
- review.

Analisis:

- tren aktivitas platform,
- funnel siswa,
- distribusi usia 5–7, 8–10, dan 11–14 tahun,
- popularitas kategori,
- course terpopuler,
- kontribusi pengajar,
- ekspor CSV.

## Instalasi pada project lama

Karena ada migration baru:

```bash
php artisan migrate
php artisan optimize:clear
php artisan view:clear
```

Jangan menggunakan `migrate:fresh` jika database berisi data yang perlu dipertahankan.

Untuk data demo lengkap:

```bash
php artisan migrate:fresh --seed
```
