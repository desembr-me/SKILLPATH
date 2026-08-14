# SKILLPATH V8 - Uniform Course Image Layout

Pada versi ini, gambar course yang diunggah Admin boleh memiliki ukuran dan rasio apa pun.

Tampilan gambar pada card tidak lagi mengikuti ukuran asli file. Semua gambar course ditampilkan dalam frame tetap 16:10. CSS menggunakan `object-fit: cover` dan `object-position: center`, sehingga gambar tetap proporsional dan bagian berlebih dipotong otomatis tanpa membuat gambar gepeng atau melar.

Aturan ini berlaku pada landing page, Explore Course, kategori, profil pengajar, Wishlist, Kelas Saya, detail course, serta preview pengelolaan gambar di Admin. Thumbnail mini pada keranjang tetap menggunakan ukuran tetap 96 x 86 px.
