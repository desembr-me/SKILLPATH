# Fitur Fleksibel, Co-Design, Ujian Akhir, dan Penyempurnaan

Dokumen ini mencatat implementasi fitur tambahan SKILLPATH beserta penyempurnaan integritas data dan edge case. Seluruh fitur menggunakan komponen visual yang sudah ada; tidak ada stylesheet utama atau aset di `public/` yang diubah.

## 1. Sistem Kredit Sesi Fleksibel

### Alur utama

- Booking live class aktif dapat dikonversi menjadi kredit karena `sakit`, `bentrok`, `keluarga`, atau `lainnya`.
- Pembatalan sesi oleh Admin otomatis mengonversi seluruh booking aktif menjadi kredit.
- Kredit hanya berlaku pada course yang sama.
- Kredit mengikuti masa aktif enrollment. Kredit dengan `expires_at` yang sudah lewat tidak dapat dipakai.
- Halaman Live Class menampilkan kredit aktif dan riwayat kredit terbaru.

### Proteksi penyempurnaan

- Pemakaian kredit memakai database transaction dan row lock.
- Booking langsung juga mengunci profil anak dan sesi sehingga dua request bersamaan tidak dapat membuat jadwal bentrok atau melebihi kapasitas.
- Kredit yang sudah dipakai untuk reschedule lalu dikonversi lagi **diaktifkan kembali**, bukan membuat kredit baru. Ini mencegah penggandaan hak sesi melalui reschedule berulang.
- Booking ulang sesi yang sebelumnya dikonversi akan memakai kembali kredit yang terkait sehingga siswa tidak memiliki sesi plus kredit secara bersamaan.
- Riwayat menyimpan jumlah reaktivasi kredit dan waktu reaktivasi terakhir.

Model/service utama:

- `App\Models\SessionCredit`
- `App\Services\SessionCreditService`
- `App\Http\Controllers\LiveClassController`

## 2. Deteksi Jadwal Bentrok Otomatis

`App\Services\ScheduleConflictService` memakai aturan overlap:

```text
existing.starts_at < target.ends_at
AND existing.ends_at > target.starts_at
```

Pemeriksaan dilakukan terhadap booking aktif pada sesi berstatus `scheduled` atau `live`.

### Penyempurnaan

- Konflik diperiksa saat halaman konfirmasi dibuka dan **diperiksa ulang di dalam transaction saat booking disimpan**.
- Operasi booking diserialisasi per anak dengan row lock sehingga dua request untuk dua sesi yang saling bertabrakan tidak dapat lolos bersamaan.
- Alternatif hanya menawarkan sesi pada course yang sama, masih akan datang, berstatus scheduled, memiliki kursi, belum dibooking anak, dan bebas bentrok.
- Admin tidak dapat memindahkan sesi yang sudah memiliki peserta ke waktu yang menyebabkan bentrok bagi peserta tersebut.
- Admin tidak dapat menurunkan kapasitas di bawah jumlah booking aktif.
- Course dari sebuah sesi tidak dapat diganti ketika sudah memiliki booking aktif.

## 3. Sertifikat Berbasis Ujian Akhir dan Retake

Tabel inti:

- `final_exams`
- `exam_attempts`

Sertifikat hanya dapat terbit jika:

1. enrollment masih aktif,
2. seluruh aktivitas course selesai,
3. sertifikat diaktifkan pada course,
4. ujian akhir aktif dan memiliki konfigurasi,
5. siswa memiliki attempt berstatus lulus.

### Retake dan audit

- Pengajar menentukan nilai minimum kelulusan dan maksimal attempt.
- Batas attempt tidak dapat diturunkan di bawah attempt yang sudah digunakan siswa.
- Setiap attempt menyimpan nilai, jumlah soal, jumlah jawaban benar, snapshot passing score, jawaban ternormalisasi, dan waktu selesai.
- Riwayat attempt dan nilai terbaik tampil pada halaman ujian.
- Perubahan passing score di masa depan tidak mengubah fakta apakah attempt lama lulus pada aturan saat attempt dilakukan.

### Pengacakan soal

`App\Services\FinalExamAttemptService` membuat urutan pertanyaan dan opsi yang berbeda per anak dan nomor attempt menggunakan deterministic HMAC. Server dapat merekonstruksi urutan yang sama saat submit tanpa mengirim indeks jawaban benar ke browser.

`exam_version` juga dikirim bersama form. Jika pengajar mengubah soal atau aturan setelah siswa membuka halaman, submit ditolak dan siswa diminta memuat ulang agar tidak dinilai menggunakan dua versi ujian yang berbeda.

### Konfigurasi pengajar

Pengajar dapat mengedit langsung dari halaman kelola course:

- judul ujian,
- passing score,
- maksimal attempt,
- teks pertanyaan,
- opsi jawaban,
- jawaban benar.

Opsi duplikat dalam satu pertanyaan ditolak.

## 4. Kategori Self-Improvement

Kategori `Self-Improvement` dan minat `Pengembangan Diri` tersedia melalui migration dan seeder. Seeder juga menyediakan course `Kenali Emosi dan Percaya Diri`.

Pengajar juga dapat memperbarui kategori course dan tag minat dari halaman edit course yang sudah ada. Dengan demikian course lama dapat dimasukkan ke Self-Improvement dan rekomendasi tidak bergantung pada data seeder saja.

Kategori ini ikut menjadi sinyal rekomendasi untuk kebutuhan:

- percaya diri,
- komunikasi/kemampuan sosial,
- kemandirian,
- sinyal suara anak seperti emosi, rasa malu, atau keberanian.

## 5. Ulasan Mentor dan Platform Terpisah

Kolom:

- `mentor_rating`
- `platform_rating`
- `mentor_review`
- `platform_review`

### Penyempurnaan moderasi

- Review baru atau yang diedit kembali ke status menunggu moderasi.
- Form review memuat kembali nilai dan teks milik pengguna sehingga update tidak menghapus data lama secara tidak sengaja.
- Alumni yang pernah memiliki enrollment tetap boleh memberi review walaupun masa akses course sudah berakhir.
- Review yang sebelumnya masuk Recycle Bin dapat dipulihkan secara aman ketika pengguna mengirim review lagi, tanpa melanggar unique constraint.

### Rating publik vs diagnostik Admin

- Rating publik dan rating profil mentor hanya memakai review yang sudah disetujui.
- Statistik diagnostik Admin memakai seluruh review non-deleted, termasuk yang belum dipublikasikan, agar moderasi tidak menyembunyikan sinyal masalah.
- Admin dapat memfilter sumber masalah: mentor, platform, keduanya, atau tidak ada indikasi.
- Rating mentor dihitung ulang setelah review dibuat/diedit, di-approve, disembunyikan, dihapus, dipulihkan, atau dihapus permanen.

Service utama: `App\Services\ReviewRatingService`.

## 6. Onboarding Minat Anak melalui Co-Design

Profil aktif menyimpan:

- `favorite_interest_id`
- `learning_need`
- `child_voice`
- `co_design_completed_at`

Penyempurnaan menambahkan tabel `co_design_sessions` sebagai histori snapshot.

Setiap penyimpanan onboarding mencatat:

- 1–5 minat yang dipilih bersama anak,
- minat utama pilihan anak,
- kebutuhan belajar utama,
- suara anak,
- waktu sesi co-design.

Histori memungkinkan sistem membedakan preferensi terbaru dan minat yang konsisten dari beberapa sesi co-design. Data histori ikut terhapus jika profil anak dihapus.

## 7. Rekomendasi Jalur Belajar Adaptif

`App\Services\AdaptiveLearningService` sekarang menilai course dari kombinasi:

- kecocokan minat aktif,
- minat utama pilihan anak,
- minat yang konsisten pada hingga 3 sesi co-design terakhir,
- kebutuhan belajar,
- sinyal kata dari `child_voice`,
- kecocokan Self-Improvement,
- kontinuitas course yang sedang dikerjakan,
- eksplorasi course baru,
- penalti besar untuk course yang sudah selesai.

Rekomendasi juga didiversifikasi agar, ketika pilihan memadai, daftar tidak didominasi satu kategori. Course yang sedang dikerjakan tetap diprioritaskan untuk menjaga kontinuitas.

Alasan yang dapat tampil antara lain:

- `Lanjutkan progres`
- `Pilihan anak & kebutuhan`
- `Pilihan utama anak`
- `Minat yang konsisten`
- `Sesuai suara anak`
- `Sesuai kebutuhan`
- `Sesuai minat`
- `Eksplorasi baru`

## Migration Penyempurnaan

Untuk pengguna yang sudah memakai versi fitur sebelumnya, jalankan migration baru:

```bash
php artisan migrate
php artisan optimize:clear
```

Migration `2026_08_13_000023_refine_flexible_learning_features.php`:

- menambah masa berlaku dan audit reaktivasi kredit,
- menambah snapshot audit attempt ujian,
- membuat histori `co_design_sessions`,
- melakukan backfill histori co-design untuk profil lama,
- melakukan backfill masa berlaku kredit dari enrollment bila tersedia.

Untuk instalasi fresh:

```bash
php artisan migrate:fresh --seed
```

## Validasi Teknis

Penyempurnaan dirancang agar source tetap kompatibel dengan arsitektur Laravel saat ini. Pengujian yang disediakan mencakup deterministic exam shuffle/grading dan status masa berlaku kredit.

Pada environment pengembangan arsip ini, PHPUnit penuh tetap membutuhkan ekstensi PHP `dom`, `mbstring`, dan `xmlwriter`. Pemeriksaan source dapat tetap dilakukan melalui PHP lint, kompilasi Blade langsung, pemeriksaan route, dan pemeriksaan service tanpa database.
