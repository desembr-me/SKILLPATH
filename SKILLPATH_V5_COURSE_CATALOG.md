# SKILLPATH V5 — Course Catalog 6 Kategori × 3 Level

Update ini menambahkan katalog course berdasarkan daftar yang diberikan. Setiap kategori mempunyai 6 jalur topik dan masing-masing jalur tersedia pada level **Beginner**, **Intermediate**, dan **Expert** sehingga terdapat **18 course per kategori** atau **108 course baru** secara keseluruhan.

## Arts

| Beginner | Intermediate | Expert |
|---|---|---|
| Menggambar Dasar | Ilustrasi | Ilustrasi Lanjutan |
| Melukis Dasar | Teknik Melukis | Painting & Composition |
| Craft Sederhana | Craft Kreatif | Craft & Creative Design |
| Seni Digital Dasar | Digital Illustration | Digital Art |
| Story Drawing | Komik Dasar | Comic & Character Design |
| Animasi Pengenalan | Animasi 2D | Animasi & Motion Design |

## Music

| Beginner | Intermediate | Expert |
|---|---|---|
| Piano Dasar | Piano Intermediate | Piano Advanced |
| Gitar Dasar | Gitar Intermediate | Gitar Advanced |
| Keyboard Dasar | Keyboard Intermediate | Keyboard Advanced |
| Vocal Dasar | Vocal Development | Vocal Performance |
| Drum Dasar | Drum Intermediate | Drum Performance |
| Musik & Ritme | Music Theory | Music Composition |

## Self Improvement

| Beginner | Intermediate | Expert |
|---|---|---|
| Confidence Building | Self-Confidence | Leadership & Influence |
| Creative Thinking | Creative Problem Solving | Innovation & Critical Thinking |
| Basic Problem Solving | Problem Solving | Advanced Decision Making |
| Basic Communication | Communication Skills | Effective Communication |
| Time Awareness | Time Management | Personal Productivity |
| Emotional Awareness | Emotional Management | Emotional Intelligence |

## Languages

| Beginner | Intermediate | Expert |
|---|---|---|
| English for Kids | English Conversation | English Advanced |
| Japanese Basic | Japanese Conversation | Japanese Intermediate |
| Mandarin Basic | Mandarin Conversation | Mandarin Intermediate |
| Korean Basic | Korean Conversation | Korean Intermediate |
| Basic Storytelling | Storytelling | Advanced Storytelling |
| Basic Vocabulary | Grammar & Vocabulary | Speaking & Presentation |

## Sports

| Beginner | Intermediate | Expert |
|---|---|---|
| Sepak Bola Dasar | Teknik Sepak Bola | Tactical Football |
| Basket Dasar | Teknik Basket | Advanced Basketball |
| Bulu Tangkis Dasar | Teknik Bulu Tangkis | Advanced Badminton |
| Bela Diri Dasar | Teknik Bela Diri | Advanced Martial Arts |
| Senam Dasar | Gymnastics & Fitness | Advanced Gymnastics |
| Dance Basic | Dance Choreography | Dance Performance |

## Technology

| Beginner | Intermediate | Expert |
|---|---|---|
| Coding for Kids | Scratch Programming | Game Development |
| Computer Basics | Web Design | Web Development |
| Robotika Dasar | Robotika Intermediate | Advanced Robotics |
| Digital Design Basic | Graphic Design | UI/UX Design |
| Animasi Dasar | 2D Animation | Advanced Animation |
| Internet Safety | Digital Creativity | Digital Project Development |

## Menambahkan katalog ke database

Jika project V4 sebelumnya sudah pernah di-seed, cukup jalankan:

```bash
php artisan db:seed --class=CourseCatalogSeeder
```

Untuk instalasi database baru/fresh, jalankan:

```bash
php artisan migrate --seed
```

Seeder menggunakan `updateOrCreate`, sehingga aman dijalankan kembali untuk memperbarui course yang sama tanpa membuat duplikat slug.

Semua course baru menggunakan `course_type = offline`, rentang pasar usia **5–14 tahun**, serta level **Beginner / Intermediate / Expert**. Level tidak dikunci berdasarkan kelompok umur tertentu agar kemampuan anak dapat menjadi pertimbangan penempatan level.
