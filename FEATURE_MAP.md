# SkillPath MVP Feature Map

## 1. Sistem Kredit Sesi Fleksibel
- Model: `SessionCredit`, `Attendance`, `CourseSession`
- Service: `SessionCreditService`
- Mentor mencatat ketidakhadiran dan menentukan eligibility.
- Kredit dibuat otomatis untuk sesi yang memenuhi syarat.
- Parent memilih sesi pengganti dari halaman kredit.

## 2. Deteksi Jadwal Bentrok Otomatis
- Service: `ScheduleConflictService`
- Dijalankan sebelum enrollment dan transaksi dibuat.
- Mengecek overlap hari dan jam terhadap enrollment aktif anak.
- Menyediakan alternatif schedule untuk course yang sama.

## 3. Sertifikat Berbasis Kelulusan + Retake
- Model: `Exam`, `ExamAttempt`, `Certificate`
- Controller: `Mentor/ExamController`
- Passing grade dan max attempts tersimpan per exam.
- Certificate otomatis dibuat saat attempt dinyatakan passed.

## 4. Self Improvement
- Category seed: `Self Improvement`
- Contoh course: `Confident Kids Club`, `Social Skills Playgroup`

## 5. Ulasan Mentor dan Platform Terpisah
- Satu review menyimpan `mentor_rating`, `mentor_review`, `platform_rating`, `platform_review`.
- Dashboard Admin menampilkan rata-rata keduanya secara terpisah.

## 6. Co-Design Onboarding
- Model: `CoDesignSession`
- Controller: `Parent/OnboardingController`
- Anak memilih maksimal tiga minat.
- Hasil pilihan disimpan sebagai input learning path.

## 7. Adaptive Learning Path
- Model: `LearningPath`, `LearningPathItem`
- Service: `LearningPathService`
- Rekomendasi awal memakai usia, minat hasil co-design, dan availability course.

## Role
- Parent: anak, onboarding, booking, transaksi, kredit, learning path, exam, certificate, review.
- Mentor: course, schedule, attendance, credit eligibility, exam, retake, evaluation.
- Admin: users, course operations, transactions, mentor quality, platform quality.

## UI/UX Revision v3
- Emoji tidak lagi digunakan sebagai elemen visual utama pada Blade views.
- Ikon utama menggunakan reusable Blade component `resources/views/components/icon.blade.php`.
- Cover course menggunakan `resources/views/components/course-art.blade.php` dengan ilustrasi CSS geometris.
- Category tile menggunakan `resources/views/components/category-tile.blade.php`.
- Landing page, katalog, detail course, onboarding, auth, dan dashboard tiga role memakai design system yang konsisten.
- Mobile navigation tersedia melalui `public/js/skillpath.js`.
