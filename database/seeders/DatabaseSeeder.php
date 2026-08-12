<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Interest;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $interestData = [
            ['name' => 'Teknologi', 'icon' => '⌨', 'description' => 'Coding, perangkat digital, dan kreasi teknologi.'],
            ['name' => 'Seni', 'icon' => '✎', 'description' => 'Menggambar, bercerita, desain, dan ekspresi kreatif.'],
            ['name' => 'Sains', 'icon' => '⚗', 'description' => 'Eksperimen, observasi, dan rasa ingin tahu.'],
            ['name' => 'Komunikasi', 'icon' => '☏', 'description' => 'Berbicara, mendengar, dan menyampaikan ide.'],
            ['name' => 'Kewirausahaan', 'icon' => '◎', 'description' => 'Ide usaha, nilai, perencanaan, dan tanggung jawab.'],
            ['name' => 'Kehidupan Sehari-hari', 'icon' => '⌂', 'description' => 'Kemandirian, kebiasaan baik, dan keterampilan praktis.'],
        ];

        $interests = collect($interestData)->mapWithKeys(function ($item) {
            $model = Interest::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + ['slug' => Str::slug($item['name'])]
            );

            return [$model->slug => $model];
        });

        $skillData = [
            ['name' => 'Kreativitas', 'icon' => '✦', 'description' => 'Mengembangkan ide dan membuat karya.'],
            ['name' => 'Literasi Digital', 'icon' => '▣', 'description' => 'Menggunakan teknologi secara produktif dan bertanggung jawab.'],
            ['name' => 'Komunikasi', 'icon' => '●', 'description' => 'Menyampaikan gagasan secara jelas dan percaya diri.'],
            ['name' => 'Problem Solving', 'icon' => '◇', 'description' => 'Menganalisis masalah dan memilih solusi.'],
            ['name' => 'Kemandirian', 'icon' => '✓', 'description' => 'Mengatur tugas dan kebiasaan sehari-hari.'],
        ];

        $skills = collect($skillData)->mapWithKeys(function ($item) {
            $model = Skill::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + ['slug' => Str::slug($item['name'])]
            );

            return [$model->slug => $model];
        });

        $paths = [
            [
                'title' => 'Studio Cerita Kreatif',
                'skill' => 'kreativitas',
                'min_age' => 5,
                'max_age' => 10,
                'level' => 'Pemula',
                'duration_minutes' => 75,
                'icon' => '✎',
                'interests' => ['seni', 'komunikasi'],
                'description' => 'Belajar menyusun tokoh, alur sederhana, dan menyampaikan cerita melalui gambar serta suara.',
                'modules' => [
                    [
                        'title' => 'Kenali Tokoh Ceritamu',
                        'summary' => 'Membuat tokoh dengan ciri, tujuan, dan emosi yang mudah dipahami.',
                        'activities' => [
                            ['title' => 'Pilih 3 ciri tokoh', 'type' => 'reflection', 'instructions' => 'Pilih tiga ciri untuk tokohmu. Contoh: berani, suka menolong, dan lucu.', 'points' => 10],
                            ['title' => 'Gambar tokoh utama', 'type' => 'project', 'instructions' => 'Gambar tokoh utama. Tambahkan nama dan satu benda favoritnya.', 'points' => 20],
                        ],
                    ],
                    [
                        'title' => 'Buat Cerita 3 Bagian',
                        'summary' => 'Menyusun awal, masalah, dan penyelesaian.',
                        'activities' => [
                            ['title' => 'Susun awal cerita', 'type' => 'project', 'instructions' => 'Tulis atau ceritakan siapa tokohnya dan di mana cerita dimulai.', 'points' => 15],
                            ['title' => 'Ceritakan hasil akhir', 'type' => 'project', 'instructions' => 'Ceritakan bagaimana tokoh menyelesaikan masalahnya.', 'points' => 20],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Penjelajah Coding Visual',
                'skill' => 'literasi-digital',
                'min_age' => 8,
                'max_age' => 14,
                'level' => 'Pemula',
                'duration_minutes' => 90,
                'icon' => '⌨',
                'interests' => ['teknologi'],
                'description' => 'Mengenal logika urutan, pola, dan instruksi melalui tantangan coding visual tanpa sintaks yang rumit.',
                'modules' => [
                    [
                        'title' => 'Urutan dan Instruksi',
                        'summary' => 'Memahami bahwa komputer menjalankan instruksi secara berurutan.',
                        'activities' => [
                            ['title' => 'Susun langkah membuat minuman', 'type' => 'quiz', 'instructions' => 'Urutkan langkah dari menyiapkan gelas sampai minuman siap.', 'points' => 10],
                            ['title' => 'Buat algoritma 5 langkah', 'type' => 'project', 'instructions' => 'Pilih satu kegiatan harian dan tulis lima langkah yang berurutan.', 'points' => 20],
                        ],
                    ],
                    [
                        'title' => 'Pola dan Perulangan',
                        'summary' => 'Mengenali tindakan yang berulang dan menyederhanakannya.',
                        'activities' => [
                            ['title' => 'Temukan pola', 'type' => 'quiz', 'instructions' => 'Cari bagian yang berulang pada contoh gerakan atau bentuk.', 'points' => 10],
                            ['title' => 'Rancang loop gerakan', 'type' => 'project', 'instructions' => 'Buat rangkaian tiga gerakan dan tentukan berapa kali diulang.', 'points' => 20],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Eksperimen Sains Mini',
                'skill' => 'problem-solving',
                'min_age' => 7,
                'max_age' => 12,
                'level' => 'Pemula',
                'duration_minutes' => 80,
                'icon' => '⚗',
                'interests' => ['sains'],
                'description' => 'Melatih observasi, membuat prediksi, dan menarik kesimpulan melalui eksperimen sederhana dan aman.',
                'modules' => [
                    [
                        'title' => 'Lihat, Tebak, Uji',
                        'summary' => 'Membandingkan prediksi dengan hasil pengamatan.',
                        'activities' => [
                            ['title' => 'Buat prediksi', 'type' => 'reflection', 'instructions' => 'Pilih dua benda. Tebak benda mana yang lebih cepat jatuh dan jelaskan alasanmu.', 'points' => 10],
                            ['title' => 'Catat hasil pengamatan', 'type' => 'project', 'instructions' => 'Lakukan pengamatan bersama orang dewasa lalu tulis hasilnya dalam tiga kalimat.', 'points' => 20],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Berani Bicara',
                'skill' => 'komunikasi',
                'min_age' => 6,
                'max_age' => 14,
                'level' => 'Pemula',
                'duration_minutes' => 60,
                'icon' => '●',
                'interests' => ['komunikasi'],
                'description' => 'Melatih struktur bicara, volume suara, kontak mata, dan cara menjelaskan ide dengan percaya diri.',
                'modules' => [
                    [
                        'title' => 'Bicara 30 Detik',
                        'summary' => 'Berlatih menyampaikan satu topik secara singkat.',
                        'activities' => [
                            ['title' => 'Pilih topik favorit', 'type' => 'reflection', 'instructions' => 'Pilih satu hal yang kamu sukai dan tulis tiga poin yang ingin disampaikan.', 'points' => 10],
                            ['title' => 'Presentasi 30 detik', 'type' => 'project', 'instructions' => 'Ceritakan topikmu selama sekitar 30 detik kepada keluarga atau teman.', 'points' => 20],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Keterampilan Mandiri',
                'skill' => 'kemandirian',
                'min_age' => 5,
                'max_age' => 10,
                'level' => 'Pemula',
                'duration_minutes' => 55,
                'icon' => '✓',
                'interests' => ['kehidupan-sehari-hari'],
                'description' => 'Membangun kebiasaan sederhana seperti merapikan barang, menyiapkan kebutuhan, dan menyelesaikan tugas kecil.',
                'modules' => [
                    [
                        'title' => 'Aku Bisa Menyiapkan Sendiri',
                        'summary' => 'Melatih perencanaan kecil dan tanggung jawab.',
                        'activities' => [
                            ['title' => 'Checklist tas harian', 'type' => 'project', 'instructions' => 'Buat daftar barang yang perlu disiapkan untuk kegiatan besok.', 'points' => 15],
                            ['title' => 'Rapikan satu area', 'type' => 'project', 'instructions' => 'Pilih meja atau rak kecil, rapikan, lalu cek apakah semua benda mudah ditemukan.', 'points' => 20],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Wirausaha Cilik',
                'skill' => 'problem-solving',
                'min_age' => 10,
                'max_age' => 14,
                'level' => 'Pemula',
                'duration_minutes' => 100,
                'icon' => '◎',
                'interests' => ['kewirausahaan', 'komunikasi'],
                'description' => 'Mengenal masalah pelanggan, ide produk sederhana, harga, dan cara menjelaskan nilai suatu produk.',
                'modules' => [
                    [
                        'title' => 'Temukan Masalah Kecil',
                        'summary' => 'Belajar melihat kebutuhan di sekitar.',
                        'activities' => [
                            ['title' => 'Wawancara mini', 'type' => 'project', 'instructions' => 'Tanya satu anggota keluarga tentang masalah kecil yang sering mereka alami.', 'points' => 20],
                            ['title' => 'Buat satu ide solusi', 'type' => 'project', 'instructions' => 'Tulis satu solusi sederhana dan jelaskan siapa yang akan terbantu.', 'points' => 20],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($paths as $pathData) {
            $skill = $skills[$pathData['skill']];

            $path = LearningPath::updateOrCreate(
                ['slug' => Str::slug($pathData['title'])],
                [
                    'skill_id' => $skill->id,
                    'title' => $pathData['title'],
                    'slug' => Str::slug($pathData['title']),
                    'description' => $pathData['description'],
                    'min_age' => $pathData['min_age'],
                    'max_age' => $pathData['max_age'],
                    'level' => $pathData['level'],
                    'duration_minutes' => $pathData['duration_minutes'],
                    'icon' => $pathData['icon'],
                    'is_published' => true,
                ]
            );

            $path->interests()->sync(
                collect($pathData['interests'])
                    ->map(fn ($slug) => $interests[$slug]->id)
                    ->all()
            );

            foreach ($pathData['modules'] as $moduleIndex => $moduleData) {
                $moduleSlug = Str::slug($pathData['title'].'-'.$moduleData['title']);

                $module = Module::updateOrCreate(
                    ['slug' => $moduleSlug],
                    [
                        'learning_path_id' => $path->id,
                        'title' => $moduleData['title'],
                        'slug' => $moduleSlug,
                        'summary' => $moduleData['summary'],
                        'order_index' => $moduleIndex + 1,
                        'estimated_minutes' => 20,
                    ]
                );

                foreach ($moduleData['activities'] as $activityIndex => $activityData) {
                    Activity::updateOrCreate(
                        [
                            'module_id' => $module->id,
                            'title' => $activityData['title'],
                        ],
                        [
                            'type' => $activityData['type'],
                            'instructions' => $activityData['instructions'],
                            'points' => $activityData['points'],
                            'order_index' => $activityIndex + 1,
                        ]
                    );
                }
            }
        }
    }
}
