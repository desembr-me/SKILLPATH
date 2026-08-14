# SKILLPATH V7 — Custom Course Images

Update ini membuat gambar card setiap course bebas diganti oleh Admin sesuai kebutuhan.

## Fitur
- Tombol **Ganti Gambar** langsung pada daftar Course di dashboard Admin.
- Halaman khusus penggantian gambar tanpa perlu mengubah data course lain.
- Upload gambar sendiri dari perangkat (JPG, JPEG, PNG, WebP, maksimum 5 MB).
- Preview gambar baru sebelum disimpan.
- Mendukung drag & drop pada halaman Ganti Gambar.
- Gambar yang sudah diunggah sebelumnya dibersihkan saat diganti lagi agar file upload tidak menumpuk.
- Tampilan card di seluruh website otomatis mengikuti `thumbnail_url` terbaru.
- Form Tambah/Edit Course tetap menyediakan upload gambar dan sekarang memiliki preview pilihan file.

## Cara menggunakan
1. Masuk sebagai Admin.
2. Buka **Course**.
3. Pada course yang ingin diubah, tekan **Ganti Gambar**.
4. Pilih atau drag gambar yang diinginkan.
5. Periksa preview lalu tekan **Simpan Gambar Baru**.

Tidak ada perubahan struktur database sehingga tidak perlu menjalankan migration baru.
