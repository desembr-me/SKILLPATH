# Recycle Bin Admin

Recycle Bin mempertahankan soft delete untuk kelas (`LearningPath`), kategori, pengguna, dan review.

Kelas yang dipindahkan ke Recycle Bin tidak ditampilkan pada katalog publik. Relasi transaksi dan pendaftaran dipertahankan untuk menjaga integritas riwayat.

Data kelas atau pengguna yang masih mempunyai transaksi, enrollment, atau relasi penting tidak dapat dihapus permanen secara sembarangan. Pemulihan kelas mengembalikannya dalam kondisi draft sehingga admin dapat memeriksa jadwal dan detail lokasi sebelum memublikasikannya kembali.
