# SKILLPATH Marketplace Course

SKILLPATH dikembangkan menjadi marketplace course nonakademik untuk anak usia 5–14 tahun.

## Fitur Orang Tua dan Anak

1. Marketplace course
2. Filter kategori, usia, tipe kelas, harga, dan pencarian
3. Detail course
4. Profil pengajar
5. Harga normal dan harga promo
6. Wishlist
7. Keranjang
8. Checkout
9. Simulasi pembayaran prototype
10. Riwayat pesanan
11. Enrollment otomatis setelah pembayaran
12. Course Saya
13. Progres aktivitas
14. Live class
15. Booking kursi live class
16. Tanya pengajar
17. Review dan rating
18. Sertifikat penyelesaian
19. Dashboard orang tua
20. Rekomendasi adaptif berdasarkan usia dan minat

## Fitur Pengajar

1. Profil publik
2. Status pengajar terverifikasi
3. Dashboard pengajar
4. Statistik course dan peserta
5. Pengaturan harga course
6. Harga promo
7. Tipe self-paced, live, atau hybrid
8. Pengaturan hasil belajar dan persyaratan
9. Pembuatan jadwal live class
10. Penghapusan jadwal live class
11. Menjawab pertanyaan peserta

## Alur Pembelian

```text
Pilih Course
    ↓
Lihat Detail & Pengajar
    ↓
Tambah ke Keranjang
    ↓
Checkout
    ↓
Pesanan Pending
    ↓
Pembayaran Berhasil
    ↓
Enrollment Aktif
    ↓
Course Saya
    ↓
Belajar / Live Class
    ↓
Progres 100%
    ↓
Sertifikat
```

## Database Tambahan

```text
users
├── instructor_profiles
├── orders
├── wishlists
├── course_reviews
└── course_questions

learning_paths
├── instructor_id
├── price
├── sale_price
├── course_type
├── live_sessions
├── enrollments
├── order_items
└── certificates

orders
└── order_items

child_profiles
├── enrollments
├── session_bookings
└── certificates
```

## Catatan Pembayaran

Checkout saat ini adalah simulasi untuk kebutuhan prototype penelitian. Jangan mengaktifkan enrollment dari tombol frontend pada sistem produksi. Integrasi produksi harus memakai payment gateway dan callback/webhook yang telah diverifikasi server.
