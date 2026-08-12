# SKILLPATH

**SKILLPATH: Platform Upskilling Nonakademik Berbasis User-Centered Design dengan Jalur Belajar Adaptif Sesuai Minat Anak Usia 5–14 Tahun**

Project ini disusun langsung mengikuti struktur aplikasi Laravel, bukan lagi dalam folder `overlay`.

## Struktur Utama

```text
skillpath/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   └── js/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
```

Folder `vendor/` dan `node_modules/` tidak disertakan karena keduanya dibuat oleh dependency manager.

## Persyaratan

- PHP 8.3+
- Composer
- MySQL
- Node.js dan npm hanya diperlukan jika Anda ingin memakai Vite

## Instalasi

### 1. Ekstrak project

Masuk ke folder:

```bash
cd skillpath
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Buat file environment

Linux/macOS:

```bash
cp .env.example .env
```

Windows:

```powershell
copy .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Buat database MySQL

```sql
CREATE DATABASE skillpath
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### 6. Periksa `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skillpath
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Migrasi dan seeder

```bash
php artisan migrate
php artisan db:seed
```

### 8. Jalankan server

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

## Fitur MVP

- Landing page ramah anak
- Register dan login orang tua
- Profil anak usia 5–14 tahun
- Pemilihan minat
- Jalur belajar nonakademik
- Modul dan aktivitas
- Penyimpanan progres
- Poin belajar
- Dashboard anak
- Rekomendasi adaptif berbasis usia, minat, dan progres

## File Penting SKILLPATH

```text
app/Services/AdaptiveLearningService.php
routes/web.php
database/seeders/DatabaseSeeder.php
resources/views/home.blade.php
resources/views/dashboard.blade.php
public/css/skillpath.css
```

## Catatan

CSS utama MVP ditempatkan di `public/css/skillpath.css` agar tampilan dapat berjalan tanpa `npm run dev`.

Jika ingin memindahkan frontend ke Vite, pindahkan atau import stylesheet tersebut melalui `resources/css/app.css`, lalu gunakan `@vite(...)` pada layout.
