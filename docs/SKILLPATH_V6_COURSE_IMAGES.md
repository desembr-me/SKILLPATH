# SKILLPATH V6 — Course Card Images

Update ini menambahkan gambar card untuk seluruh katalog course SKILLPATH.

## Yang ditambahkan

- 108 ilustrasi SVG lokal di `public/images/courses/`.
- Setiap ilustrasi dibuat sesuai tema course, kategori, dan level.
- Palet tetap menggunakan warna asli SKILLPATH: kuning, cream, biru, hijau, orange, pink, putih, dan dark ink.
- Gambar muncul pada landing page, Explore, halaman kategori, profil pengajar, detail course, Wishlist, Keranjang, dan Kelas Saya.
- Admin sekarang melihat thumbnail pada daftar course.
- Saat Admin menambahkan course baru, gambar card wajib diunggah.
- Saat mengedit course, gambar hanya diganti jika Admin mengunggah file baru.
- Format upload Admin: JPG, JPEG, PNG, WebP, maksimal 4 MB. Rasio disarankan 16:10.

## Untuk katalog 108 course yang sudah ada

Jalankan:

```bash
php artisan db:seed --class=CourseCatalogSeeder
```

Seeder akan mengisi `thumbnail_url` setiap course ke ilustrasi yang sesuai pada `public/images/courses/`.

Tidak ada migration baru karena field `thumbnail_url` sudah tersedia pada tabel `learning_paths`.
