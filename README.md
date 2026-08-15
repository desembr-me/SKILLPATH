# SkillPath Laravel 13 + MySQL

SkillPath adalah marketplace kursus offline non-akademik untuk anak usia 5-14 tahun. Akun utama dimiliki orang tua karena transaksi, booking, jadwal, kredit sesi, dan monitoring dilakukan oleh orang tua. Anak terlibat pada onboarding minat serta pengalaman belajar.

## Pembaruan UI v3
- Tampilan landing page dirapikan dan dibuat lebih premium.
- Emoji pada UI utama dihilangkan dan diganti ikon SVG garis yang konsisten.
- Card course memakai ilustrasi geometris berbasis CSS, sehingga tidak membutuhkan file gambar tambahan.
- Hero menggunakan visual learning-path dan schedule card.
- Category card, course card, detail course, onboarding, login/register, serta dashboard tiga role memakai design system yang sama.
- Avatar dashboard memakai inisial, bukan emoji.
- Responsive navigation ditambahkan untuk mobile.
- Backend, route, model, migration, serta fitur MVP tetap dipertahankan.

## Requirement
- PHP 8.3+
- Composer
- MySQL 8+
- Laravel 13

## Instalasi
```bash
composer install
cp .env.example .env
php artisan key:generate
```

Pastikan folder runtime Laravel tersedia dan writable:
```bash
mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R u+rwX,g+rwX bootstrap/cache storage
```

Buat database:
```sql
CREATE DATABASE skillpath CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atur `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skillpath
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian jalankan:
```bash
php artisan migrate:fresh --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun demo
- Parent: `parent@skillpath.test`
- Mentor: `mentor@skillpath.test`
- Admin: `admin@skillpath.test`
- Password: `password`

## MVP
1. Flexible Session Credit
2. Automatic Schedule Conflict Detection
3. Exam-Based Certificate dan Retake
4. Self Improvement Category
5. Separate Mentor and Platform Reviews
6. Child Interest Onboarding melalui Co-Design
7. Adaptive Learning Path Recommendation

`PREVIEW.html` dapat dibuka tanpa Laravel untuk melihat arah visual landing page.
