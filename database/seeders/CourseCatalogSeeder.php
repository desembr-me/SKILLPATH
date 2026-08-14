<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\FinalExam;
use App\Models\Interest;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\Module;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class CourseCatalogSeeder extends Seeder
{
    /**
     * Menambahkan katalog course SKILLPATH sesuai matriks Beginner / Intermediate / Expert.
     * Setiap kategori memiliki 6 jalur topik x 3 level = 18 course.
     * Total katalog pada seeder ini = 108 course.
     */
    public function run(): void
    {
        $catalog = [
            'arts' => [
                ['Menggambar Dasar', 'Ilustrasi', 'Ilustrasi Lanjutan'],
                ['Melukis Dasar', 'Teknik Melukis', 'Painting & Composition'],
                ['Craft Sederhana', 'Craft Kreatif', 'Craft & Creative Design'],
                ['Seni Digital Dasar', 'Digital Illustration', 'Digital Art'],
                ['Story Drawing', 'Komik Dasar', 'Comic & Character Design'],
                ['Animasi Pengenalan', 'Animasi 2D', 'Animasi & Motion Design'],
            ],
            'music' => [
                ['Piano Dasar', 'Piano Intermediate', 'Piano Advanced'],
                ['Gitar Dasar', 'Gitar Intermediate', 'Gitar Advanced'],
                ['Keyboard Dasar', 'Keyboard Intermediate', 'Keyboard Advanced'],
                ['Vocal Dasar', 'Vocal Development', 'Vocal Performance'],
                ['Drum Dasar', 'Drum Intermediate', 'Drum Performance'],
                ['Musik & Ritme', 'Music Theory', 'Music Composition'],
            ],
            'self-improvement' => [
                ['Confidence Building', 'Self-Confidence', 'Leadership & Influence'],
                ['Creative Thinking', 'Creative Problem Solving', 'Innovation & Critical Thinking'],
                ['Basic Problem Solving', 'Problem Solving', 'Advanced Decision Making'],
                ['Basic Communication', 'Communication Skills', 'Effective Communication'],
                ['Time Awareness', 'Time Management', 'Personal Productivity'],
                ['Emotional Awareness', 'Emotional Management', 'Emotional Intelligence'],
            ],
            'languages' => [
                ['English for Kids', 'English Conversation', 'English Advanced'],
                ['Japanese Basic', 'Japanese Conversation', 'Japanese Intermediate'],
                ['Mandarin Basic', 'Mandarin Conversation', 'Mandarin Intermediate'],
                ['Korean Basic', 'Korean Conversation', 'Korean Intermediate'],
                ['Basic Storytelling', 'Storytelling', 'Advanced Storytelling'],
                ['Basic Vocabulary', 'Grammar & Vocabulary', 'Speaking & Presentation'],
            ],
            'sports' => [
                ['Sepak Bola Dasar', 'Teknik Sepak Bola', 'Tactical Football'],
                ['Basket Dasar', 'Teknik Basket', 'Advanced Basketball'],
                ['Bulu Tangkis Dasar', 'Teknik Bulu Tangkis', 'Advanced Badminton'],
                ['Bela Diri Dasar', 'Teknik Bela Diri', 'Advanced Martial Arts'],
                ['Senam Dasar', 'Gymnastics & Fitness', 'Advanced Gymnastics'],
                ['Dance Basic', 'Dance Choreography', 'Dance Performance'],
            ],
            'technology' => [
                ['Coding for Kids', 'Scratch Programming', 'Game Development'],
                ['Computer Basics', 'Web Design', 'Web Development'],
                ['Robotika Dasar', 'Robotika Intermediate', 'Advanced Robotics'],
                ['Digital Design Basic', 'Graphic Design', 'UI/UX Design'],
                ['Animasi Dasar', '2D Animation', 'Advanced Animation'],
                ['Internet Safety', 'Digital Creativity', 'Digital Project Development'],
            ],
        ];

        $levelNames = ['Beginner', 'Intermediate', 'Expert'];
        $levelConfig = [
            'Beginner' => [
                'duration' => 120,
                'price' => 149000,
                'sale' => 119000,
                'summary' => 'Pengenalan konsep dan praktik dasar dengan aktivitas yang ringan, menyenangkan, dan terarah.',
                'outcome' => 'memahami dasar, mengikuti instruksi, dan menyelesaikan satu latihan atau karya sederhana',
            ],
            'Intermediate' => [
                'duration' => 180,
                'price' => 199000,
                'sale' => 169000,
                'summary' => 'Penguatan teknik melalui latihan bertahap, tantangan, dan proyek kelompok atau individu.',
                'outcome' => 'menerapkan teknik dengan lebih mandiri dan menyelesaikan proyek tingkat menengah',
            ],
            'Expert' => [
                'duration' => 240,
                'price' => 269000,
                'sale' => 229000,
                'summary' => 'Pendalaman skill melalui proyek yang lebih kompleks, evaluasi, presentasi, atau performa.',
                'outcome' => 'menggabungkan berbagai keterampilan dan menghasilkan proyek atau performa yang lebih matang',
            ],
        ];

        // Foto asli (public/foto) yang benar-benar menggambarkan track tersebut.
        // Track tanpa foto yang sesuai dibiarkan memakai ilustrasi SVG default.
        $trackPhotos = [
            'arts' => [
                0 => 'foto/arts-01.jpg', // Menggambar/Ilustrasi - sketsa pensil
                1 => 'foto/arts-05.jpg', // Melukis/Painting - kuas di atas kanvas
                2 => 'foto/arts-03.jpg', // Craft - pot botol daur ulang
                3 => 'foto/arts-06.jpg', // Seni Digital - ilustrasi digital di tablet
                4 => 'foto/advanced-animation.jpg', // Comic & Character Design - desain karakter animasi
                5 => 'foto/advanced-animation.jpg', // Animasi
            ],
            'music' => [
                0 => 'foto/music-02.jpg', // Piano - anak belajar piano
                1 => 'foto/music-01.jpg', // Gitar
                2 => 'foto/music-07.jpg', // Keyboard - studio dengan keyboard controller
                3 => 'foto/music-03.jpg', // Vocal - kelas vokal dengan mic
                4 => 'foto/music-04.jpg', // Drum
                5 => 'foto/music-06.jpg', // Musik & Ritme/Teori - lembar not balok
            ],
            'sports' => [
                0 => 'foto/sports-01-sepakbola.jpg',
                1 => 'foto/sports-02-basket.jpg',
                2 => 'foto/sports-04-bulutangkis.jpg',
                3 => 'foto/sports-06-karate.jpg', // Bela Diri
                4 => 'foto/sports-05-senam.jpg', // Senam
                5 => 'foto/dance.jpg', // Dance
            ],
            'technology' => [
                0 => 'foto/tech-04.jpg', // Coding for Kids - visual block coding
                1 => 'foto/tech-01.jpg', // Web Design/Development - buku pemrograman web
                2 => 'foto/tech-06.jpg', // Robotika
                3 => 'foto/tech-05.jpg', // Digital/Graphic Design
                4 => 'foto/advanced-animation.jpg', // Animasi Dasar/2D Animation/Advanced Animation
                5 => 'foto/tech-07.jpg', // Internet Safety - cyber security
            ],
            'languages' => [
                0 => 'foto/english-convo.jpg', // English for Kids/Conversation/Advanced
                1 => 'foto/japanese-convo.jpg', // Japanese
                2 => 'foto/mandarin.jpg', // Mandarin
                3 => 'foto/korean-convo.jpg', // Korean
                4 => 'foto/arts-01.jpg', // Storytelling - sketsa karakter cerita
                5 => 'foto/english-convo.jpg', // Vocabulary/Speaking - word cloud vocabulary
            ],
            'self-improvement' => [
                0 => 'foto/confidence-building.jpg', // Confidence Building/Self-Confidence/Leadership
                1 => 'foto/emotional-intelligence.jpg', // Creative Thinking/Creative Problem Solving/Innovation & Critical Thinking
                2 => 'foto/emotional-intelligence.jpg', // Basic Problem Solving/Problem Solving/Advanced Decision Making
                3 => 'foto/effective-communication.jpg', // Basic Communication/Communication Skills/Effective Communication
                4 => 'foto/time-management.jpg', // Time Awareness/Time Management/Personal Productivity
                5 => 'foto/emotional-intelligence.jpg', // Emotional Awareness/Emotional Management/Emotional Intelligence
            ],
        ];

        $categoryConfig = [
            'arts' => [
                'instructor' => 'naila@skillpath.test',
                'skill' => 'kreativitas',
                'interests' => ['seni'],
                'icon' => '✎',
                'label' => 'Arts',
                'focus' => 'kreativitas visual, teknik berkarya, imajinasi, dan ekspresi anak',
                'requirements' => 'Bawa alat gambar atau perlengkapan seni sesuai informasi kelas. Semua kegiatan dilakukan secara tatap muka.',
            ],
            'music' => [
                'instructor' => 'dinda@skillpath.test',
                'skill' => 'kreativitas',
                'interests' => ['seni'],
                'icon' => '♫',
                'label' => 'Music',
                'focus' => 'ritme, musikalitas, teknik instrumen atau vokal, dan kepercayaan diri saat tampil',
                'requirements' => 'Alat musik disesuaikan dengan jenis kelas. Untuk kelas tertentu peserta dapat menggunakan alat yang tersedia di lokasi.',
            ],
            'self-improvement' => [
                'instructor' => 'maya@skillpath.test',
                'skill' => 'kemandirian',
                'interests' => ['pengembangan-diri', 'komunikasi'],
                'icon' => '✧',
                'label' => 'Self Improvement',
                'focus' => 'percaya diri, komunikasi, pengelolaan diri, emosi, kreativitas berpikir, dan kepemimpinan',
                'requirements' => 'Peserta bersedia mengikuti aktivitas kelompok, role play, diskusi, dan refleksi sederhana sesuai usia.',
            ],
            'languages' => [
                'instructor' => 'maya@skillpath.test',
                'skill' => 'komunikasi',
                'interests' => ['komunikasi'],
                'icon' => 'Aa',
                'label' => 'Languages',
                'focus' => 'percakapan, kosakata, storytelling, listening, dan kemampuan presentasi',
                'requirements' => 'Bawa alat tulis. Penempatan level dapat disesuaikan dengan kemampuan anak, tidak hanya berdasarkan usia.',
            ],
            'sports' => [
                'instructor' => 'raka@skillpath.test',
                'skill' => 'kemandirian',
                'interests' => ['kehidupan-sehari-hari'],
                'icon' => '●',
                'label' => 'Sports',
                'focus' => 'koordinasi, teknik gerak, kebugaran, disiplin, strategi, teamwork, dan sportivitas',
                'requirements' => 'Gunakan pakaian olahraga yang nyaman, sepatu sesuai aktivitas, dan bawa botol minum.',
            ],
            'technology' => [
                'instructor' => 'arif@skillpath.test',
                'skill' => 'literasi-digital',
                'interests' => ['teknologi'],
                'icon' => '⌨',
                'label' => 'Technology',
                'focus' => 'literasi digital, coding, desain, robotika, keamanan internet, dan penyelesaian proyek teknologi',
                'requirements' => 'Bawa laptop bila diminta pada detail kelas. Perangkat digunakan di kelas secara langsung bersama pengajar.',
            ],
        ];

        $categories = Category::whereIn('slug', array_keys($catalog))->get()->keyBy('slug');
        $skills = Skill::whereIn('slug', collect($categoryConfig)->pluck('skill')->unique()->values())->get()->keyBy('slug');
        $interestSlugs = collect($categoryConfig)->flatMap(fn ($config) => $config['interests'])->unique()->values();
        $interests = Interest::whereIn('slug', $interestSlugs)->get()->keyBy('slug');
        $instructorEmails = collect($categoryConfig)->pluck('instructor')->unique()->values();
        $instructors = User::whereIn('email', $instructorEmails)->where('role', 'instructor')->get()->keyBy('email');

        foreach (array_keys($catalog) as $categorySlug) {
            if (! $categories->has($categorySlug)) {
                throw new RuntimeException("Kategori {$categorySlug} belum tersedia. Jalankan DatabaseSeeder utama terlebih dahulu.");
            }
        }

        $sequence = 0;

        foreach ($catalog as $categorySlug => $tracks) {
            $config = $categoryConfig[$categorySlug];
            $category = $categories[$categorySlug];
            $skill = $skills->get($config['skill']);
            $instructor = $instructors->get($config['instructor']);

            if (! $skill || ! $instructor) {
                throw new RuntimeException("Data skill atau pengajar untuk kategori {$categorySlug} belum lengkap.");
            }

            foreach ($tracks as $trackIndex => $track) {
                $photo = $trackPhotos[$categorySlug][$trackIndex] ?? null;

                foreach ($track as $levelIndex => $title) {
                    $sequence++;
                    $level = $levelNames[$levelIndex];
                    $levelData = $levelConfig[$level];
                    $slug = Str::slug($title);

                    $description = $title.' adalah kelas '.$config['label'].' offline level '.$level.' untuk anak usia 5–14 tahun. '
                        .$levelData['summary'].' Fokus kegiatan pada '.$config['focus'].'.';

                    $outcomes = 'Setelah mengikuti '.$title.', anak diharapkan mampu '.$levelData['outcome'].'.';

                    $path = LearningPath::withTrashed()->updateOrCreate(
                        ['slug' => $slug],
                        [
                            'skill_id' => $skill->id,
                            'instructor_id' => $instructor->id,
                            'title' => $title,
                            'description' => $description,
                            'price' => $levelData['price'],
                            'sale_price' => $levelData['sale'],
                            'is_free' => false,
                            'course_type' => 'offline',
                            'min_age' => 5,
                            'max_age' => 14,
                            'level' => $level,
                            'duration_minutes' => $levelData['duration'],
                            'icon' => $config['icon'],
                            'thumbnail_url' => $photo ?? 'images/courses/'.$slug.'.svg',
                            'certificate_enabled' => true,
                            'live_class_enabled' => true,
                            'access_days' => null,
                            'learning_outcomes' => $outcomes,
                            'requirements' => $config['requirements'],
                            'is_published' => true,
                            'published_at' => now()->subDays(($sequence % 28) + 1),
                        ]
                    );

                    if ($path->trashed()) {
                        $path->restore();
                    }

                    $path->categories()->sync([$category->id]);
                    $path->interests()->sync(
                        collect($config['interests'])
                            ->map(fn ($interestSlug) => $interests->get($interestSlug)?->id)
                            ->filter()
                            ->values()
                            ->all()
                    );

                    $this->seedCourseContent($path, $level);
                }
            }
        }
    }

    private function seedCourseContent(LearningPath $path, string $level): void
    {
        $moduleDefinitions = [
            [
                'title' => 'Kenali Dasar',
                'summary' => 'Pengenalan konsep dan contoh langsung bersama pengajar.',
                'activities' => [
                    ['Pengenalan & Demonstrasi', 'reflection', 10],
                    ['Latihan Dasar', 'project', 20],
                ],
            ],
            [
                'title' => 'Latihan '.$level,
                'summary' => 'Praktik terarah sesuai level course dengan umpan balik pengajar.',
                'activities' => [
                    ['Latihan Terarah', 'quiz', 15],
                    ['Tantangan Skill', 'project', 25],
                ],
            ],
            [
                'title' => 'Project & Refleksi',
                'summary' => 'Menyelesaikan karya, performa, tantangan, atau mini project dan mengevaluasi perkembangan.',
                'activities' => [
                    ['Mini Project Akhir', 'project', 30],
                    ['Refleksi Perkembangan', 'reflection', 15],
                ],
            ],
        ];

        foreach ($moduleDefinitions as $moduleIndex => $definition) {
            $moduleSlug = Str::slug($path->slug.'-'.$definition['title']);
            $module = Module::updateOrCreate(
                ['slug' => $moduleSlug],
                [
                    'learning_path_id' => $path->id,
                    'title' => $definition['title'],
                    'summary' => $definition['summary'],
                    'order_index' => $moduleIndex + 1,
                    'estimated_minutes' => max(30, intdiv((int) $path->duration_minutes, 3)),
                ]
            );

            foreach ($definition['activities'] as $activityIndex => $activity) {
                Activity::updateOrCreate(
                    ['module_id' => $module->id, 'title' => $activity[0]],
                    [
                        'type' => $activity[1],
                        'instructions' => 'Ikuti arahan pengajar di kelas, lakukan praktik dengan aman, lalu selesaikan aktivitas bersama atau secara mandiri sesuai instruksi.',
                        'points' => $activity[2],
                        'order_index' => $activityIndex + 1,
                    ]
                );
            }
        }

        FinalExam::updateOrCreate(
            ['learning_path_id' => $path->id],
            [
                'title' => 'Evaluasi Akhir - '.$path->title,
                'passing_score' => 75,
                'max_attempts' => 3,
                'questions' => [
                    [
                        'question' => 'Apa tujuan utama mengikuti kelas '.$path->title.'?',
                        'options' => [
                            $path->learning_outcomes,
                            'Hanya menyelesaikan kelas tanpa praktik',
                            'Menghafal materi tanpa mencoba',
                            'Menghindari tantangan baru',
                        ],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Apa yang sebaiknya dilakukan ketika latihan terasa sulit?',
                        'options' => [
                            'Mencoba kembali secara bertahap dan meminta arahan pengajar',
                            'Langsung berhenti',
                            'Melewati semua aktivitas',
                            'Menyalin hasil teman',
                        ],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Mengapa praktik langsung penting dalam kelas offline?',
                        'options' => [
                            'Agar anak dapat mencoba skill dan mendapat umpan balik langsung',
                            'Agar kelas selesai lebih cepat',
                            'Agar tidak perlu memahami konsep',
                            'Agar semua peserta menghasilkan karya yang sama',
                        ],
                        'correct' => 0,
                    ],
                ],
                'is_active' => true,
            ]
        );

        for ($session = 1; $session <= 2; $session++) {
            LiveSession::updateOrCreate(
                [
                    'learning_path_id' => $path->id,
                    'title' => 'Kelas Tatap Muka '.$session.' - '.$path->title,
                ],
                [
                    'instructor_id' => $path->instructor_id,
                    'description' => 'Sesi kelas offline untuk demonstrasi, praktik, tanya jawab, aktivitas kelompok, dan umpan balik langsung dari pengajar.',
                    'starts_at' => now()->addDays(5 + $session * 5)->setTime(16, 0),
                    'ends_at' => now()->addDays(5 + $session * 5)->setTime(17, 30),
                    'location' => 'SKILLPATH Learning Hub, pusat kota',
                    'meeting_url' => 'https://maps.google.com/',
                    'capacity' => 16,
                    'status' => 'scheduled',
                ]
            );
        }
    }
}
