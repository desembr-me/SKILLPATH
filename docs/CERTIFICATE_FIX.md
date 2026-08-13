# Perbaikan Sertifikat SKILLPATH

Perbaikan utama:

- satu komponen sertifikat dipakai oleh halaman siswa dan admin,
- ukuran mengikuti A4 landscape 297 × 210 mm,
- hasil cetak memakai area 277 × 190 mm setelah margin 10 mm,
- tampilan lebih formal dan simetris,
- nama siswa menjadi fokus utama,
- informasi course, nilai, tanggal, nomor sertifikat, pengajar, dan penerbit tersusun rapi,
- tersedia tombol Cetak / Simpan PDF,
- status revoked tetap diberi watermark DICABUT,
- halaman admin dan siswa menghasilkan desain sertifikat yang sama,
- controller memuat kategori course secara eksplisit.

Tidak ada migration baru.

Setelah mengganti file:

```bash
php artisan optimize:clear
php artisan view:clear
php artisan serve
```
