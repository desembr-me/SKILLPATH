<?php

namespace Database\Seeders;

use App\Models\{
    ActivityCompletion,
    Attendance,
    Category,
    Certificate,
    Child,
    CoDesignSession,
    Course,
    CourseModule,
    CourseSchedule,
    CourseSession,
    Enrollment,
    Exam,
    ExamAttempt,
    LearningPath,
    PlatformReview,
    RescheduleRequest,
    Review,
    SessionCredit,
    Transaction,
    User,
    Wishlist
};
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Parents
        $parent1 = User::create([
            'name' => 'Nadia Putri',
            'email' => 'parent@skillpath.test',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'parent',
        ]);

        $parent2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@skillpath.test',
            'phone' => '081345678901',
            'password' => Hash::make('password'),
            'role' => 'parent',
        ]);

        $parent3 = User::create([
            'name' => 'Rina Wijaya',
            'email' => 'rina@skillpath.test',
            'phone' => '081456789012',
            'password' => Hash::make('password'),
            'role' => 'parent',
        ]);

        // 2. Admin
        User::create([
            'name' => 'Admin SkillPath',
            'email' => 'admin@skillpath.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. 6 Official Categories
        $categoryConfigs = [
            ['Arts', 'arts', 'Kreativitas visual, melukis, craft, komik, seni digital, dan animasi anak.', '#FFE6D8'],
            ['Languages', 'languages', 'Bahasa Inggris, Jepang, Mandarin, Korea, storytelling, dan public speaking anak.', '#DDF2FF'],
            ['Music', 'music', 'Piano, gitar, keyboard, vokal, drum, dan eksplorasi ritme musikal anak.', '#FFF2B9'],
            ['Sports', 'sports', 'Sepak bola, basket, bulu tangkis, bela diri, senam kebugaran, dan modern dance.', '#DFF4E5'],
            ['Technology', 'technology', 'Coding anak, Scratch programming, robotika, desain grafis, game design, dan literasi digital.', '#EAE5FF'],
            ['Self-Improvement', 'self-improvement', 'Public speaking, creative thinking, problem solving, leadership, time management, dan kecerdasan emosional.', '#FFE3EC'],
        ];

        $categories = collect($categoryConfigs)->mapWithKeys(function ($x) {
            $c = Category::create([
                'name' => $x[0],
                'slug' => $x[1],
                'description' => $x[2],
                'accent' => $x[3],
            ]);
            return [$x[0] => $c];
        });

        // 4. Mentors (6 Dedicated Mentors for each Category)
        $mentorArts = User::create([
            'name' => 'Sari Wulandari, S.Sn.',
            'email' => 'sari@skillpath.test',
            'phone' => '081298765434',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Arts']->id,
            'avatar' => 'avatars/mentors/sari.png',
            'headline' => 'Mentor Seni Rupa & Ilustrasi Anak',
            'bio' => 'Lulusan FSRD ITB, berpengalaman 8 tahun membimbing kreativitas visual anak lewat pendekatan eksplorasi rasa dan cerita.',
        ]);

        $mentorLanguages = User::create([
            'name' => 'Clara Simanjuntak, M.Pd.',
            'email' => 'clara@skillpath.test',
            'phone' => '081298765436',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Languages']->id,
            'avatar' => 'avatars/mentors/clara.png',
            'headline' => 'Mentor Bahasa & Komunikasi Anak',
            'bio' => 'Praktisi pendidikan bahasa dengan sertifikasi TESOL & pembicara edukasi anak lewat metode storytelling dan drama roleplay.',
        ]);

        $mentorMusic = User::create([
            'name' => 'Naya Rahma, S.Mus.',
            'email' => 'mentor@skillpath.test',
            'phone' => '081298765432',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Music']->id,
            'avatar' => 'avatars/mentors/naya.png',
            'headline' => 'Mentor Piano & Musik Anak',
            'bio' => 'Pianis dan pendidik musik anak sejak 7 tahun, fokus pada fondasi ritme ceria dan kepercayaan diri tampil panggung.',
        ]);

        $mentorSports = User::create([
            'name' => 'Fajar Nugroho, S.Or.',
            'email' => 'fajar@skillpath.test',
            'phone' => '081298765437',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Sports']->id,
            'avatar' => 'avatars/mentors/fajar.png',
            'headline' => 'Mentor Olahraga & Kebugaran Anak',
            'bio' => 'Pelatih fisik anak bersertifikat nasional, mengutamakan keselamatan gerak, koordinasi motorik kasar, dan sportivitas.',
        ]);

        $mentorTech = User::create([
            'name' => 'Dimas Pratama, S.Kom.',
            'email' => 'dimas@skillpath.test',
            'phone' => '081298765433',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Technology']->id,
            'avatar' => 'avatars/mentors/dimas.png',
            'headline' => 'Mentor Robotika & Coding Anak',
            'bio' => 'Software Engineer & Educator, membimbing anak merakit robot dan belajar logika pemrograman lewat proyek nyata yang seru.',
        ]);

        $mentorSelf = User::create([
            'name' => 'Bimo Aditya, M.Psi.',
            'email' => 'bimo@skillpath.test',
            'phone' => '081298765435',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Self-Improvement']->id,
            'avatar' => 'avatars/mentors/bimo.png',
            'headline' => 'Mentor Pengembangan Diri & Psikologi Anak',
            'bio' => 'Psikolog anak dan coach kepemimpinan muda, fokus membangun rasa percaya diri, ketahanan emosi, dan public speaking.',
        ]);

        // 5. Complete 6 Categories x 6 Tracks x 3 Levels (108 Courses) Matrix
        $matrix = [
            // ==================== 1. ARTS ====================
            'Arts' => [
                'mentor' => $mentorArts,
                'location_base' => 'Studio Warna Tebet',
                'city' => 'Jakarta Selatan',
                'tracks' => [
                    [
                        'theme' => 'Menggambar & Ilustrasi',
                        'levels' => [
                            ['Beginner', 'Menggambar Dasar', 'Garis, bentuk, dan ekspresi visual pertama anak.', 'Mengenalkan anak pada teknik memegang alat gambar dengan nyaman, eksplorasi bentuk geometris dasar, dan menuangkan imajinasi ke dalam kertas.', 5, 7, 580000, 6, 75, '✏️', 'arts-01.jpg'],
                            ['Intermediate', 'Ilustrasi', 'Teknik visual storytelling dan sketsa karakter.', 'Mengembangkan keterampilan menggambar karakter, memahami proporsi anatomi kartun sederhana, dan menyusun cerita visual secara terstruktur.', 8, 10, 680000, 8, 90, '🎨', 'arts-02.jpg'],
                            ['Expert', 'Ilustrasi Lanjutan', 'Komposisi mendalam, pewarnaan lanjut, & portofolio.', 'Fokus pada penyusunan portofolio seni personal, teknik pencahayaan dan bayangan (shading & lighting), serta penemuan gaya ilustrasi orisinal anak.', 11, 14, 780000, 8, 90, '🖌️', 'Young Artist Portfolio.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Melukis Kreatif',
                        'levels' => [
                            ['Beginner', 'Melukis Dasar', 'Eksplorasi warna primer dan goresan kuas ceria.', 'Anak belajar mencampur warna dasar, mengenal tekstur cat air dan akrilik aman, serta berani berekspresi tanpa takut salah di atas kanvas mini.', 5, 7, 600000, 6, 75, '🎨', 'arts-03.jpg'],
                            ['Intermediate', 'Teknik Melukis', 'Blending warna, tekstur, dan perspektif ruang.', 'Mempelajari teknik blending kuas halus dan kering, gradasi warna alam, serta menciptakan lukisan pemandangan dan objek nyata bernilai estetika.', 8, 10, 700000, 8, 90, '🖼️', 'arts-04.jpg'],
                            ['Expert', 'Painting & Composition', 'Karya kanvas tematik dan pameran mini seni.', 'Eksplorasi komposisi kanvas berukuran besar, teknik multi-media cat akrilik dan pasta tekstur, serta kurasi karya untuk pameran seni studio.', 11, 14, 820000, 8, 90, '🎨', 'arts-05.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Kerajinan Tangan',
                        'levels' => [
                            ['Beginner', 'Craft Sederhana', 'Melatih motorik halus lewat kreasi kertas & clay.', 'Aktivitas seru menggunting, melipat (origami), dan membentuk playdough/clay untuk menstimulasi ketangkasan tangan anak.', 5, 7, 560000, 6, 75, '✂️', 'arts-06.jpg'],
                            ['Intermediate', 'Craft Kreatif', 'Kreasi bahan daur ulang dan anyaman interaktif.', 'Membuat produk fungsional dan dekoratif menggunakan berbagai kombinasi material seperti kain flanel, kayu stik, dan bahan daur ulang.', 8, 10, 660000, 8, 90, '🧶', 'arts-07.jpg'],
                            ['Expert', 'Craft & Creative Design', 'Kriya modern, miniatur 3D, dan desain produk.', 'Menciptakan karya kriya 3 dimensi yang detail, teknik decoupage, tanah liat keramik dingin, serta perancangan produk kreatif mandiri.', 11, 14, 760000, 8, 90, '🏺', 'arts-08.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Seni Digital',
                        'levels' => [
                            ['Beginner', 'Seni Digital Dasar', 'Mengenal tablet gambar, kuas digital, dan layer.', 'Pengenalan menggambar di layar tablet digital, memahami layer, kuas warna digital, dan membuat stiker digital lucu buatan sendiri.', 7, 9, 680000, 6, 75, '📱', 'arts-09.jpg'],
                            ['Intermediate', 'Digital Illustration', 'Line art rapi, palet warna, dan shading digital.', 'Membuat ilustrasi digital yang rapi dan menarik dengan pemahaman teknik blending warna digital dan efek visual modern.', 9, 12, 780000, 8, 90, '💻', 'arts-10.jpg'],
                            ['Expert', 'Digital Art', 'Concept art, background painting, & digital asset.', 'Pembuatan concept art berkualitas tinggi untuk game/buku cerita anak, perancangan karakter 360 derajat, dan aset digital.', 11, 14, 880000, 8, 90, '✨', 'Young Artist Portfolio.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Komik & Story Drawing',
                        'levels' => [
                            ['Beginner', 'Story Drawing', 'Bercerita lewat gambar 1 panel dan balon kata.', 'Menggabungkan imajinasi cerita anak dengan gambar sederhana, ekspresi wajah senang/sedih, dan balon percakapan ceria.', 6, 8, 620000, 6, 75, '📖', 'arts-01.jpg'],
                            ['Intermediate', 'Komik Dasar', 'Layout panel komik, alur cerita, & dinamika aksi.', 'Merancang strip komik 4 panel, sudut pandang kamera (camera angle), efek suara onomatope, dan klimaks cerita.', 8, 11, 720000, 8, 90, '🗨️', 'arts-02.jpg'],
                            ['Expert', 'Comic & Character Design', 'Mini novel grafis & karakter orisinal lengkap.', 'Memproduksi buku komik pendek karya orisinal anak lengkap dengan world-building, naskah dialog, dan desain sampul siap cetak.', 11, 14, 840000, 8, 90, '🦸', 'arts-05.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Animasi Dasar',
                        'levels' => [
                            ['Beginner', 'Animasi Pengenalan', 'Flipbook klasik dan stop-motion playdough.', 'Memahami prinsip ilusi gerak melalui flipbook kertas tradisional dan eksperimen stop-motion menyenangkan menggunakan figurine/clay.', 7, 9, 700000, 6, 75, '🎞️', 'Advanced Animation.jpg'],
                            ['Intermediate', 'Animasi 2D', 'Keyframing 2D, timing, dan karakter gerak digital.', 'Membuat animasi digital 2D bergerak frame-by-frame, gerak berjalan karakter (walk cycle), dan ekspresi gerak dinamis.', 9, 12, 800000, 8, 90, '🎬', 'tech-07.jpg'],
                            ['Expert', 'Animasi & Motion Design', 'Short movie animasi dengan efek suara & scoring.', 'Memproduksi film animasi pendek berdurasi 30-60 detik lengkap dengan alur cerita, background gerak, dubbing suara, dan efek suara.', 11, 14, 920000, 8, 90, '🚀', 'tech-08.jpg'],
                        ]
                    ],
                ]
            ],

            // ==================== 2. LANGUAGES ====================
            'Languages' => [
                'mentor' => $mentorLanguages,
                'location_base' => 'BrightTalk Depok',
                'city' => 'Depok',
                'tracks' => [
                    [
                        'theme' => 'Bahasa Inggris Anak',
                        'levels' => [
                            ['Beginner', 'English for Kids', 'Fonik ceria, lagu interaktif, dan kosakata harian.', 'Membangun kecintaan anak pada bahasa Inggris melalui nyanyian interaktif, pengenalan bunyi huruf (phonics), dan mengenal benda di sekitar.', 5, 7, 650000, 6, 75, '🔤', 'english convo.jpg'],
                            ['Intermediate', 'English Conversation', 'Percakapan aktif, tanya jawab, & roleplay seru.', 'Membiasakan anak berbicara aktif dalam bahasa Inggris sehari-hari, menceritakan pengalaman liburan, dan simulasi situasi nyata bersama teman.', 8, 10, 750000, 8, 90, '💬', 'english convo.jpg'],
                            ['Expert', 'English Advanced', 'Debat santun, esai kreatif, dan presentasi lancar.', 'Mengasah kemampuan berbahasa Inggris tingkat lanjut untuk diskusi kelompok, menyampaikan argumen kritis secara santun, dan presentasi publik.', 11, 14, 850000, 8, 90, '🎓', 'english convo.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Bahasa Jepang Anak',
                        'levels' => [
                            ['Beginner', 'Japanese Basic', 'Hiragana ceria, salam sapaan, & budaya Jepang.', 'Mengenal huruf Hiragana dengan metode visual menyenangkan, salam sehari-hari (Aisatsu), angka, dan etika kesopanan budaya Jepang.', 6, 8, 660000, 6, 75, '🌸', 'japanese convo.jpg'],
                            ['Intermediate', 'Japanese Conversation', 'Katakana, dialog anime favorit, & pola kalimat.', 'Belajar huruf Katakana, menyusun kalimat sederhana untuk bercakap-cakap dengan teman, dan memahami percakapan bertema hobi.', 8, 11, 760000, 8, 90, '⛩️', 'japanese convo.jpg'],
                            ['Expert', 'Japanese Intermediate', 'Kanji dasar anak, membaca cerita, & roleplay.', 'Mengenal 50 Kanji dasar, membaca cerita pendek Jepang, serta berkomunikasi lancar dalam situasi perjalanan dan perkenalan formal.', 11, 14, 860000, 8, 90, '🎌', 'japanese convo.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Bahasa Mandarin Anak',
                        'levels' => [
                            ['Beginner', 'Mandarin Basic', 'Pinyin, 4 nada vokal, dan kosakata ramah anak.', 'Fondasi pelafalan Pinyin yang tepat dengan 4 nada Mandarin, mengenal angka, anggota keluarga, dan lagu anak tradisional Tiongkok.', 6, 8, 680000, 6, 75, '🏮', 'mandarinjpg.jpg'],
                            ['Intermediate', 'Mandarin Conversation', 'Goresan Hanzi dasar dan dialog interaktif.', 'Belajar urutan goresan Hanzi (Bishun), percakapan di sekolah/toko, dan merespons pertanyaan mentor secara spontan.', 8, 11, 780000, 8, 90, '🎋', 'mandarinjpg.jpg'],
                            ['Expert', 'Mandarin Intermediate', 'Persiapan HSK anak, pidato pendek, & teks.', 'Penguasaan 150+ karakter Hanzi, kemampuan menyusun paragraf narasi, dan persiapan sertifikasi kemahiran bahasa Mandarin muda.', 11, 14, 880000, 8, 90, '🐉', 'mandarinjpg.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Bahasa Korea Anak',
                        'levels' => [
                            ['Beginner', 'Korean Basic', 'Membaca Hangeul mudah, sapaan hormat, & lagu.', 'Metode cepat membaca abjad Hangeul (vokal & konsonan), percakapan perkenalan diri, dan mengenal kebudayaan Korea yang positif.', 6, 8, 660000, 6, 75, '✨', 'korean convo.jpg'],
                            ['Intermediate', 'Korean Conversation', 'Menyusun kalimat percakapan harian & hobi.', 'Mempelajari partikel tata bahasa Korea, percakapan seputar makanan, sekolah, dan ekspresi sehari-hari yang natural.', 8, 11, 760000, 8, 90, '🇰🇷', 'korean convo.jpg'],
                            ['Expert', 'Korean Intermediate', 'Membaca dongeng, diary pendek, & drama.', 'Membaca dongeng Korea, menulis jurnal harian berbahasa Korea, dan bermain peran (roleplay drama) untuk kelancaran berbicara.', 11, 14, 860000, 8, 90, '🌟', 'korean convo.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Public Speaking Anak',
                        'levels' => [
                            ['Beginner', 'Basic Storytelling', 'Keberanian bersuara lantang dan gestur ceria.', 'Melatih anak berani tampil di depan teman-teman, mengatasi rasa malu, dan menggunakan ekspresi wajah saat berbicara.', 5, 7, 620000, 6, 75, '🗣️', 'berani bicara.jpg'],
                            ['Intermediate', 'Storytelling', 'Teknik intonasi panggung & struktur 3 babak.', 'Mengasah modulasi suara, jeda dramatis, penggunaan properti panggung, dan membawa pendengar larut dalam cerita.', 8, 10, 720000, 8, 90, '🎙️', 'berani bicara.jpg'],
                            ['Expert', 'Advanced Storytelling', 'Showcase panggung, multi-karakter, & MC.', 'Penguasaan panggung tingkat lanjut, membawakan cerita dengan berbagai suara karakter, dan memandu acara (Master of Ceremony).', 11, 14, 840000, 8, 90, '📢', 'berani bicara.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Storytelling & Vocabulary',
                        'levels' => [
                            ['Beginner', 'Basic Vocabulary', 'Mengenal 300+ kata benda & aksi lewat kartu.', 'Memperkaya bank kosakata anak secara visual melalui flashcard bergambar, permainan tebak kata, dan gerakan asosiasi kata.', 5, 7, 600000, 6, 75, '📚', 'Effective Communication.jpg'],
                            ['Intermediate', 'Grammar & Vocabulary', 'Menyusun kalimat efektif tanpa rasa bosan.', 'Memahami tata bahasa dengan permainan kartu dan teka-teki logika kalimat sehingga anak terbiasa menyusun kalimat yang runtut.', 8, 10, 700000, 8, 90, '📖', 'Effective Communication.jpg'],
                            ['Expert', 'Speaking & Presentation', 'Presentasi ide brilian dengan artikulasi prima.', 'Mengajarkan anak merancang poin presentasi yang menarik, menyampaikan pesan dengan jelas, dan menjawab pertanyaan audiens.', 11, 14, 820000, 8, 90, '🌟', 'Effective Communication.jpg'],
                        ]
                    ],
                ]
            ],

            // ==================== 3. MUSIC ====================
            'Music' => [
                'mentor' => $mentorMusic,
                'location_base' => 'Nada Studio Cipete',
                'city' => 'Jakarta Selatan',
                'tracks' => [
                    [
                        'theme' => 'Piano Anak',
                        'levels' => [
                            ['Beginner', 'Piano Dasar', 'Posisi jari yang benar, not balok, & melodi.', 'Mengenal tuts piano, posisi tangan lengkung alami, membaca notasi balok kunci G dasar, dan memainkan lagu anak pertama.', 5, 7, 720000, 6, 60, '🎹', 'music-Piano.jpg'],
                            ['Intermediate', 'Piano Intermediate', 'Koordinasi dua tangan, akord mayor & minor.', 'Melatih independensi tangan kanan memainkan melodi dan tangan kiri memainkan akord pengiring dengan ketukan metronom konsisten.', 8, 10, 840000, 8, 60, '🎼', 'music-Piano.jpg'],
                            ['Expert', 'Piano Advanced', 'Repertoar klasik & pop, dinamika, & resital.', 'Memainkan repertoar lagu dengan penghayatan musikalitas tinggi, penguasaan pedal sustain ekspresif, dan persiapan panggung resital akhir.', 11, 14, 960000, 8, 60, '🌟', 'music-Piano.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Gitar Anak',
                        'levels' => [
                            ['Beginner', 'Gitar Dasar', 'Petikan senar terbuka, kunci dasar, & ritme.', 'Menggunakan gitar mini ramah anak, melatih kekuatan ujung jari, petikan jempol, dan memainkan kunci dasar C, G, Am, F.', 6, 8, 680000, 6, 60, '🎸', 'music-Gitar.jpg'],
                            ['Intermediate', 'Gitar Intermediate', 'Strumming bervariasi, arpeggio petikan, & tempo.', 'Mempelajari berbagai pola genjrengan (strumming pattern), teknik petikan arpeggio, dan transisi akord yang mulus.', 8, 11, 780000, 8, 60, '🎶', 'music-Gitar.jpg'],
                            ['Expert', 'Gitar Advanced', 'Fingerstyle solo, tangga nada, & jamming band.', 'Bermain fingerstyle solo instrumental, melodi meliuk dengan skala pentatonik, serta jamming bersama ansambel musik.', 11, 14, 900000, 8, 60, '⚡', 'music-Gitar.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Keyboard Dasar',
                        'levels' => [
                            ['Beginner', 'Keyboard Dasar', 'Fitur keyboard elektrik, ritme otomatis, & melodi.', 'Mengenal suara instrumen digital keyboard, menyelaraskan ketukan dengan auto-accompaniment, dan melodi lagu modern.', 6, 8, 650000, 6, 60, '🎹', 'music-03.jpg'],
                            ['Intermediate', 'Keyboard Intermediate', 'Pengaturan sound layer, split keyboard, & aransemen.', 'Mengatur dual-voice suara instrumen, split keyboard bass di kiri dan lead di kanan, serta aransemen lagu sederhana.', 8, 11, 750000, 8, 60, '🎛️', 'music-04.jpg'],
                            ['Expert', 'Keyboard Advanced', 'MIDI production, synthesizers, & penampilan panggung.', 'Menghubungkan keyboard ke software musik digital (DAW), eksplorasi synthesizer, dan tampil sebagai keyboardist band.', 11, 14, 880000, 8, 60, '🎧', 'music-06.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Menyanyi & Vocal',
                        'levels' => [
                            ['Beginner', 'Vocal Dasar', 'Pernapasan diafragma, artikulasi jelas, & pitch.', 'Latihan postur bernyanyi yang benar, pernapasan diafragma ceria, artikulasi vokal jelas A-I-U-E-O, dan melatih nada tepat.', 5, 7, 640000, 6, 60, '🎤', 'music-07.jpg'],
                            ['Intermediate', 'Vocal Development', 'Jangkauan nada, vibrato halus, & penghayatan.', 'Memperluas rentang vokal anak (head voice & chest voice), melatih kontrol vibrato alami, dan penghayatan makna lagu.', 8, 10, 760000, 8, 60, '🎙️', 'music-09.jpg'],
                            ['Expert', 'Vocal Performance', 'Aksi panggung percaya diri & rekaman studio.', 'Penguasaan panggung dengan mikrofon, teknik improvisasi vokal melismatis, dan pengalaman rekaman vokal profesional.', 11, 14, 890000, 8, 60, '🌟', 'music-10.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Drum Dasar',
                        'levels' => [
                            ['Beginner', 'Drum Dasar', 'Stick grip, ketukan snare & kick 4/4 sederhana.', 'Memegang stick drum dengan nyaman (matched grip), koordinasi tangan kanan pada hi-hat, tangan kiri pada snare, dan pedal kick.', 6, 8, 750000, 6, 60, '🥁', 'music-03.jpg'],
                            ['Intermediate', 'Drum Intermediate', 'Drum fills, sinkopasi, & 4 anggota tubuh.', 'Memainkan variasi ketukan drum fills yang bertenaga, transisi antar bagian lagu, dan melatih independensi tangan-kaki.', 8, 11, 860000, 8, 60, '🥁', 'music-04.jpg'],
                            ['Expert', 'Drum Performance', 'Odd-time beats, solo drum, & pertunjukan live.', 'Penguasaan ritme kompleks seperti funk, rock, jazz dasar, solo drum berkecepatan tinggi, dan aksi panggung konser.', 11, 14, 980000, 8, 60, '⚡', 'music-06.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Musik & Ritme Kreatif',
                        'levels' => [
                            ['Beginner', 'Musik & Ritme', 'Perkusi seru, tepuk tangan, & mengenal tempo.', 'Bermain alat perkusi sederhana seperti marakas, tamborin, dan xilofon untuk menanamkan kepekaan ritmik sejak dini.', 5, 7, 620000, 6, 60, '🪘', 'music-Ukulele.jpg'],
                            ['Intermediate', 'Music Theory', 'Membaca tanda birama, tangga nada, & interval.', 'Memahami teori musik secara visual: tanda kunci, birama 3/4 dan 4/4, tangga nada diatonik, dan interval harmoni.', 8, 10, 720000, 8, 60, '🎼', 'music-Biola.jpg'],
                            ['Expert', 'Music Composition', 'Menciptakan melodi orisinal & aransemen mini.', 'Membimbing anak menciptakan lagu karya sendiri dari lirik, chord progression, hingga aransemen melodi instrumen lengkap.', 11, 14, 860000, 8, 60, '🎵', 'music-09.jpg'],
                        ]
                    ],
                ]
            ],

            // ==================== 4. SPORTS ====================
            'Sports' => [
                'mentor' => $mentorSports,
                'location_base' => 'Arena Cilandak',
                'city' => 'Jakarta Selatan',
                'tracks' => [
                    [
                        'theme' => 'Sepak Bola',
                        'levels' => [
                            ['Beginner', 'Sepak Bola Dasar', 'Dribbling santai, passing akurat, & sportivitas.', 'Melatih sentuhan bola pertama anak, mengontrol bola dengan kaki bagian dalam, lari kelincahan zig-zag, dan kerja sama tim.', 5, 7, 650000, 6, 90, '⚽', 'sports-sepakBola.jpg'],
                            ['Intermediate', 'Teknik Sepak Bola', 'Shooting bertenaga, umpan silang, & kontrol udara.', 'Mengasah teknik tendangan melengkung, kontrol bola dada dan paha, serta penempatan posisi tanpa bola di lapangan.', 8, 10, 750000, 8, 90, '🥅', 'sports-sepakBola.jpg'],
                            ['Expert', 'Tactical Football', 'Strategi formasi, transisi bermain, & turnamen.', 'Memahami visi taktik sepak bola modern, kepemimpinan kapten di lapangan, eksekusi set-piece bola mati, dan turnamen.', 11, 14, 850000, 8, 90, '🏆', 'sports-sepakBola.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Basket Anak',
                        'levels' => [
                            ['Beginner', 'Basket Dasar', 'Dribble rendah & tinggi, chest pass, & lay-up.', 'Pengenalan bola basket ukuran mini, melatih koordinasi pantulan bola dengan tangan, operan dada terarah, dan lay-up.', 6, 8, 660000, 6, 90, '🏀', 'sports-Basket.jpg'],
                            ['Intermediate', 'Teknik Basket', 'Crossover dribble, jump shoot, & man-to-man.', 'Mengembangkan kelincahan gerakan tipuan (crossover), tembakan melompat yang konsisten, dan posisi bertahan membendung lawan.', 8, 11, 760000, 8, 90, '⛹️', 'sports-Basket.jpg'],
                            ['Expert', 'Advanced Basketball', 'Pick and roll, fast break, & strategi tim 5v5.', 'Penerapan taktik pick and roll, rotasi cepat serangan balik (fast break), ketahanan stamina, dan simulasi tanding 5v5.', 11, 14, 880000, 8, 90, '🔥', 'sports-Basket.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Bulu Tangkis',
                        'levels' => [
                            ['Beginner', 'Bulu Tangkis Dasar', 'Grip handshake, servis pendek, & pukulan lob.', 'Membiasakan pegangan raket yang benar (forehand & backhand), servis melambung tepat sasaran, dan timing shuttlecock.', 6, 8, 650000, 6, 90, '🏸', 'sports-Bultang.jpg'],
                            ['Intermediate', 'Teknik Bulu Tangkis', 'Footwork lincah 6 sudut lapangan & smash tajam.', 'Melatih langkah kaki (footwork) yang efisien, pukulan dropshot tipis di atas net, dan smash keras terarah ke sudut lapangan.', 8, 11, 760000, 8, 90, '🏸', 'sports-Bultang.jpg'],
                            ['Expert', 'Advanced Badminton', 'Permainan reli panjang, taktik ganda & tunggal.', 'Strategi menekan lawan dalam reli cepat, penempatan bola akrobatik, analisis kelemahan lawan, dan ketahanan fisik prima.', 11, 14, 880000, 8, 90, '🥇', 'sports-Bultang.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Bela Diri',
                        'levels' => [
                            ['Beginner', 'Bela Diri Dasar', 'Kuda-kuda kokoh, pukulan lurus, & disiplin.', 'Menanamkan rasa hormat, disiplin diri tinggi, fondasi kuda-kuda seimbang, serta tangkisan dasar untuk melindungi diri.', 6, 8, 660000, 6, 90, '🥋', 'sports-Karate.jpg'],
                            ['Intermediate', 'Teknik Bela Diri', 'Kombinasi tendangan, bantingan aman, & kata.', 'Melatih jurus rangkaian gerak (kata), tendangan putar seimbang, dan teknik jatuh yang aman tanpa cedera fisik.', 8, 11, 770000, 8, 90, '🥋', 'sports-Karate.jpg'],
                            ['Expert', 'Advanced Martial Arts', 'Sparring terkontrol dengan pelindung & sabuk.', 'Latihan tanding terkontrol (kumite) dengan peralatan proteksi lengkap, strategi reaksi cepat, dan kenaikan tingkat sabuk.', 11, 14, 890000, 8, 90, '🥋', 'sports-Karate.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Senam & Kebugaran Anak',
                        'levels' => [
                            ['Beginner', 'Senam Dasar', 'Kelenturan tubuh, forward roll, & melompat.', 'Aktivitas motorik kasar melompat rintangan busa, melatih fleksibilitas otot, keseimbangan balok, dan gulingan matras.', 5, 7, 600000, 6, 75, '🤸', 'sports-Senam.jpg'],
                            ['Intermediate', 'Gymnastics & Fitness', 'Cartwheel meroda, handstand dinding, & postur.', 'Menguasai gerakan meroda yang stabil, kekuatan lengan untuk handstand, serta latihan kebugaran kardiovaskular anak.', 8, 10, 720000, 8, 90, '🤸‍♀️', 'sports-Senam.jpg'],
                            ['Expert', 'Advanced Gymnastics', 'Rangkaian gerak akrobatik lantai & kelenturan.', 'Menggabungkan kelenturan artistik, round-off, backbend kickover, dan koreografi senam lantai yang memukau.', 11, 14, 850000, 8, 90, '⭐', 'sports-Senam.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Dance / Modern Dance',
                        'levels' => [
                            ['Beginner', 'Dance Basic', 'Mendengar ketukan beat, isolasi gerak, & riang.', 'Mengikuti irama lagu pop anak yang energik, menggerakkan bahu, pinggul, dan langkah kaki dengan riang gembira.', 5, 7, 620000, 6, 75, '💃', 'dance.jpg'],
                            ['Intermediate', 'Dance Choreography', 'Koreografi modern hip-hop / K-Pop cover cilik.', 'Menghafal 8-count koreografi modern dance, sinkronisasi gerakan dalam kelompok (dance crew), dan power gerak.', 8, 10, 740000, 8, 90, '🕺', 'dance.jpg'],
                            ['Expert', 'Dance Performance', 'Aksi panggung showcase, ekspresi, & freestyle.', 'Memadukan improvisasi freestyle, transisi formasi panggung dinamis, dan penampilan dalam video showcase profesional.', 11, 14, 870000, 8, 90, '✨', 'dance.jpg'],
                        ]
                    ],
                ]
            ],

            // ==================== 5. TECHNOLOGY ====================
            'Technology' => [
                'mentor' => $mentorTech,
                'location_base' => 'SkillHub Kemang',
                'city' => 'Jakarta Selatan',
                'tracks' => [
                    [
                        'theme' => 'Coding untuk Anak',
                        'levels' => [
                            ['Beginner', 'Coding Dasar Anak', 'Logika blok visual, urutan instruksi, & puzzle.', 'Belajar cara berpikir komputasional tanpa rumus rumit: menyusun blok logika (if/else), perulangan (loops), dan puzzle labirin.', 6, 8, 690000, 6, 75, '🧩', 'tech-08.jpg'],
                            ['Intermediate', 'Logic & Coding', 'Variabel data, fungsi logika, & mini game.', 'Mengenal konsep variabel, operator logika perbandingan, sistem poin dan nyawa dalam pembuatan game arcade interaktif.', 8, 11, 820000, 8, 90, '💻', 'tech-01.jpg'],
                            ['Expert', 'Advanced Junior Coding', 'Transisi Python dasar, algoritma cerdas, & web.', 'Menulis baris kode teks Python nyata untuk membangun kalkulator cerdas, bot kuis interaktif, dan algoritma cerdas.', 11, 14, 950000, 8, 90, '⚡', 'tech-02.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Scratch Programming',
                        'levels' => [
                            ['Beginner', 'Scratch Dasar', 'Menggerakkan sprite, suara, & dialog animasi.', 'Membuat animasi cerita interaktif pertama anak di platform MIT Scratch: menambahkan karakter, latar tempat, dan efek suara.', 6, 8, 650000, 6, 75, '🐱', 'tech-03.jpg'],
                            ['Intermediate', 'Scratch Game Maker', 'Game platformer 2D, gravitasi fisik, & skor.', 'Membangun game melompat platformer: mengatur gravitasi buatan, rintangan bergerak, skor tertinggi, dan level bertingkat.', 8, 11, 780000, 8, 90, '🎮', 'tech-04.jpg'],
                            ['Expert', 'Advanced Scratch Creator', 'Game multi-level, cloud variables, & integrasi AI.', 'Membuat proyek game epik dengan sistem leaderboard, kloning sprite kompleks, dan pengenalan sensor kamera Scratch.', 11, 14, 890000, 8, 90, '🚀', 'tech-05.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Robotika Dasar',
                        'levels' => [
                            ['Beginner', 'Robotika Pengenalan', 'Merakit komponen mekanik, roda gigi, & motor DC.', 'Mengenal prinsip kerja mesin sederhana: roda gigi (gears), poros penggerak, motor listrik mini, dan merakit robot mobil pertama.', 6, 8, 780000, 6, 75, '🤖', 'tech-06.jpg'],
                            ['Intermediate', 'Robotika & Sensor', 'Sensor ultrasonik jarak & line tracking pintar.', 'Memprogram mikrokontroler agar robot mampu mendeteksi dinding dengan sensor jarak ultrasonik dan mengikuti jalur garis.', 8, 11, 920000, 8, 90, '🦾', 'tech-09.jpg'],
                            ['Expert', 'Advanced Robotics & AI', 'Lengan robotik cerdas, Bluetooth control, & IoT.', 'Merakit lengan mekanik servo (robotic arm), mengontrol pergerakan robot via Bluetooth smartphone, dan otomasi cerdas.', 11, 14, 1050000, 8, 90, '🚀', 'tech-10.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Desain Grafis Anak',
                        'levels' => [
                            ['Beginner', 'Desain Grafis Dasar', 'Kombinasi warna, tipografi ramah, & poster pesta.', 'Belajar tata letak visual, memilih font huruf yang mudah dibaca, menyelaraskan warna, dan membuat kartu ucapan/poster kreatif.', 7, 9, 650000, 6, 75, '📐', 'arts-09.jpg'],
                            ['Intermediate', 'Digital Graphic Design', 'Desain feed medsos edukatif, infografis, & stiker.', 'Menggunakan tools desain digital untuk membuat infografis pengetahuan sains, stiker ilustrasi, dan banner kegiatan sekolah.', 9, 12, 760000, 8, 90, '🎨', 'arts-10.jpg'],
                            ['Expert', 'Creative Brand & Visual Art', 'Desain logo orisinal, merchandise t-shirt, & mock-up.', 'Merancang identitas merek kreasi anak: membuat logo vektor, desain kaos t-shirt, totebag, dan penyusunan portofolio desainer muda.', 11, 14, 880000, 8, 90, '💼', 'Young Artist Portfolio.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Animasi & Game Design',
                        'levels' => [
                            ['Beginner', 'Game Design Pengenalan', 'Merancang peta level game, karakter, & koin.', 'Mengenal peran seorang Game Designer: merancang level dunia game (level design), rintangan musuh, dan aturan main yang seru.', 7, 9, 720000, 6, 75, '🕹️', 'tech-07.jpg'],
                            ['Intermediate', '2D Game & Animation', 'Game engine 2D, animasi sprite, & efek ledakan.', 'Mengembangkan game 2D mandiri: mengimpor sprite animasi buatan sendiri, efek ledakan partikel, sound effect, dan menu permainan.', 9, 12, 850000, 8, 90, '👾', 'tech-08.jpg'],
                            ['Expert', 'Interactive Game Studio', 'Publishing game web, RPG mechanics, & testing.', 'Membuat game RPG naratif atau puzzle fisika yang kompleks, melakukan playtesting bersama teman, dan mempublikasikan game ke web.', 11, 14, 980000, 8, 90, '🎮', 'Advanced Animation.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Literasi Digital & Internet Aman',
                        'levels' => [
                            ['Beginner', 'Literasi Digital Dasar', 'Mengetik cepat 10 jari ceria & eksplorasi aman.', 'Menguasai keterampilan dasar mengetik 10 jari ceria, navigasi komputer yang aman, dan memanfaatkan komputer untuk belajar mandiri.', 6, 8, 580000, 6, 75, '🔍', 'tech-01.jpg'],
                            ['Intermediate', 'Internet Cerdas & Kreatif', 'Mencari informasi valid, presentasi, & etika.', 'Membedakan informasi benar dan hoax di internet, teknik browsing aman, menyusun riset mini, dan etika bersosialisasi di dunia digital.', 8, 11, 680000, 8, 90, '💡', 'tech-02.jpg'],
                            ['Expert', 'Cyber Safety & Digital Ethics', 'Privasi data akun, keamanan password, & jejak digital.', 'Menanamkan kesadaran keamanan siber: proteksi privasi akun, enkripsi password yang kuat, dan membangun jejak digital positif.', 11, 14, 790000, 8, 90, '🛡️', 'tech-05.jpg'],
                        ]
                    ],
                ]
            ],

            // ==================== 6. SELF-IMPROVEMENT ====================
            'Self-Improvement' => [
                'mentor' => $mentorSelf,
                'location_base' => 'GrowSpace Bintaro',
                'city' => 'Tangerang Selatan',
                'tracks' => [
                    [
                        'theme' => 'Public Speaking',
                        'levels' => [
                            ['Beginner', 'Public Speaking Dasar', 'Berani tampil percaya diri, kontak mata, & senyum.', 'Melatih anak mengatasi demam panggung secara bertahap lewat permainan ekspresi, kontak mata ramah, dan memperkenalkan diri.', 5, 7, 640000, 6, 75, '🎤', 'berani bicara.jpg'],
                            ['Intermediate', 'Public Speaking & Expression', 'Bahasa tubuh dinamis, intonasi, & bercerita runtut.', 'Mengasah penguasaan panggung: gerakan tangan natural, variasi nada suara ceria dan tegas, serta menyusun struktur bicara teratur.', 8, 10, 750000, 8, 90, '🗣️', 'Confidence Building.jpg'],
                            ['Expert', 'Mastery Speech & Presentation', 'Pidato persuasif, debat santun, & memandu forum.', 'Mempersiapkan anak menjadi pembicara muda yang meyakinkan: pidato inspiratif, membela argumen dengan santun, dan memandu acara.', 11, 14, 880000, 8, 90, '🌟', 'Effective Communication.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Creative Thinking',
                        'levels' => [
                            ['Beginner', 'Creative Thinking Dasar', 'Melihat objek dari sudut baru & asosiasi ide unik.', 'Membangkitkan rasa ingin tahu anak lewat pertanyaan "Bagaimana jika?", permainan menghubungkan 2 objek tak biasa, dan ide unik.', 5, 7, 600000, 6, 75, '💡', 'Eksperimen Sains Mini.jpg'],
                            ['Intermediate', 'Mind Mapping & Idea Lab', 'Peta pikiran visual warna-warni & brainstorming.', 'Mengorganisir ide-ide kreatif ke dalam peta pikiran (mind map) visual, teknik SCAMPER untuk modifikasi benda, dan solusi inovatif.', 8, 10, 720000, 8, 90, '🧠', 'Confidence Building.jpg'],
                            ['Expert', 'Innovation & Creative Project', 'Design thinking & membuat proyek solusi nyata.', 'Menerapkan tahapan Design Thinking: berempati pada masalah sekitar, mendefinisikan masalah, dan membuat prototipe solusi kreatif anak.', 11, 14, 840000, 8, 90, '🚀', 'Young Artist Portfolio.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Problem Solving',
                        'levels' => [
                            ['Beginner', 'Problem Solving Dasar', 'Teka-teki logika, pola visual, & pantang menyerah.', 'Melatih pantang menyerah saat menghadapi rintangan melalui puzzle maze, tangram bentuk, dan menemukan jalan keluar bertahap.', 6, 8, 620000, 6, 75, '🧩', 'tech-03.jpg'],
                            ['Intermediate', 'Critical Thinking & Logic', 'Analisis sebab-akibat, deduction puzzle, & strategi.', 'Mengajak anak berpikir mendalam: mengenali sebab dan akibat, membedakan fakta dan opini, serta menyusun langkah penyelesaian teratur.', 8, 11, 740000, 8, 90, '♟️', 'Effective Communication.jpg'],
                            ['Expert', 'Strategic Problem Solving', 'Studi kasus nyata, simulasi krisis, & solusi.', 'Menghadapi simulasi tantangan kelompok (escape room logic), menganalisis alternatif keputusan, dan mengevaluasi hasil tindakan.', 11, 14, 860000, 8, 90, '🎯', 'Confidence Building.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Leadership Anak',
                        'levels' => [
                            ['Beginner', 'Leadership Dasar', 'Menjadi teladan baik, berbagi peran, & inisiatif.', 'Menanamkan fondasi kepemimpinan sejak dini: berani memimpin barisan, merapikan perlengkapan bersama, dan menghargai teman sebaya.', 6, 8, 650000, 6, 75, '🤝', 'youth camp.jpg'],
                            ['Intermediate', 'Teamwork & Collaboration', 'Mendengarkan aktif, delegasi tugas, & empati tim.', 'Belajar bekerja sama dalam tim proyek: mendengarkan usulan teman, membagi tugas sesuai kelebihan masing-masing, dan menyatukan visi.', 8, 11, 760000, 8, 90, '🌟', 'youth camp.jpg'],
                            ['Expert', 'Youth Leadership & Organization', 'Memimpin proyek sosial sekolah & manajemen konflik.', 'Memandu tim merencanakan dan mengeksekusi kegiatan nyata, memediasi perbedaan pendapat, dan menginspirasi rekan sebaya.', 11, 14, 890000, 8, 90, '👑', 'youth camp.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Time Management',
                        'levels' => [
                            ['Beginner', 'Manajemen Waktu Dasar', 'Mengenal jam, jadwal ceria, & anti menunda tugas.', 'Membantu anak memahami konsep waktu secara visual melalui jadwal harian bergambar, menyusun rutinitas pagi-malam, dan timer belajar.', 6, 8, 590000, 6, 75, '⏰', 'Time Management.jpg'],
                            ['Intermediate', 'Focus & Daily Routine', 'Teknik Pomodoro anak & menentukan prioritas.', 'Melatih konsentrasi belajar tanpa terdistraksi gawai, membagi waktu bermain dan belajar dengan seimbang, serta skala prioritas.', 8, 11, 710000, 8, 90, '⏳', 'Time Management.jpg'],
                            ['Expert', 'Productivity & Goal Setting', 'SMART goals, habit tracker mandiri, & disiplin.', 'Menyusun target pencapaian pribadi (SMART Goals), menggunakan buku perencana (planner/bullet journal), dan konsistensi jangka panjang.', 11, 14, 830000, 8, 90, '🎯', 'Time Management.jpg'],
                        ]
                    ],
                    [
                        'theme' => 'Confidence & Emotional Skills',
                        'levels' => [
                            ['Beginner', 'Percaya Diri Dasar', 'Mengenal emosi senang/sedih & afirmasi positif.', 'Mengenalkan roda emosi ramah anak, mengekspresikan perasaan dengan kata-kata bukan tantrum, dan membangun afirmasi percaya diri.', 5, 7, 620000, 6, 75, '☀️', 'Emotional Intelligence.jpg'],
                            ['Intermediate', 'Emotional Intelligence', 'Regulasi emosi, empati sosial, & menenangkan diri.', 'Mempelajari teknik pernapasan menenangkan saat cemas/marah, memahami sudut pandang teman (empati), dan menjaga komunikasi positif.', 8, 10, 740000, 8, 90, '❤️', 'Emotional Intelligence.jpg'],
                            ['Expert', 'Resilience & Self Mastery', 'Growth mindset, bangkit dari gagal, & mental kuat.', 'Menumbuhkan pola pikir berkembang (growth mindset): memandang kegagalan sebagai ruang belajar, mengatasi tekanan, dan harga diri yang kuat.', 11, 14, 860000, 8, 90, '💎', 'Confidence Building.jpg'],
                        ]
                    ],
                ]
            ]
        ];

        $courses = [];
        $featuredCount = 0;

        foreach ($matrix as $catName => $catData) {
            $cat = $categories[$catName];
            $mentor = $catData['mentor'];

            foreach ($catData['tracks'] as $track) {
                foreach ($track['levels'] as $lvl) {
                    [$levelName, $title, $sub, $desc, $minAge, $maxAge, $price, $sessions, $dur, $emoji, $img] = $lvl;
                    
                    // Slug generation with unique level suffix if needed
                    $baseSlug = Str::slug($title);
                    $slug = $baseSlug;
                    $counter = 1;
                    while (isset($courses[$slug])) {
                        $slug = $baseSlug . '-' . Str::slug($levelName) . ($counter > 1 ? "-{$counter}" : '');
                        $counter++;
                    }

                    // Featured selection (feature ~1-2 per category)
                    $isFeat = ($featuredCount < 12 && ($levelName === 'Intermediate' || $levelName === 'Beginner') && rand(0, 3) === 0);
                    if ($isFeat) $featuredCount++;

                    $course = Course::create([
                        'category_id' => $cat->id,
                        'instructor_id' => $mentor->id,
                        'title' => $title,
                        'slug' => $slug,
                        'level' => $levelName,
                        'subtitle' => $sub,
                        'description' => $desc,
                        'age_min' => $minAge,
                        'age_max' => $maxAge,
                        'price' => $price,
                        'sessions_count' => $sessions,
                        'duration_minutes' => $dur,
                        'location_name' => $catData['location_base'],
                        'city' => $catData['city'],
                        'cover_emoji' => $emoji,
                        'cover_image' => $img,
                        'accent' => $cat->accent,
                        'is_featured' => $isFeat,
                        'status' => 'active',
                    ]);

                    // Generate distinct weekly schedules (3 day slots)
                    $scheduleSlots = [
                        [6, '09:00:00', '10:30:00', 'Studio A (Lt. 1)'],
                        [6, '13:30:00', '15:00:00', 'Studio B (Lt. 2)'],
                        [0, '10:00:00', '11:30:00', 'Ruang Aktivitas Utama'],
                    ];

                    foreach ($scheduleSlots as $s) {
                        $sched = CourseSchedule::create([
                            'course_id' => $course->id,
                            'instructor_id' => $course->instructor_id,
                            'day_of_week' => $s[0],
                            'start_time' => $s[1],
                            'end_time' => $s[2],
                            'start_date' => now()->subWeeks(2)->toDateString(),
                            'end_date' => now()->addMonths(3)->toDateString(),
                            'capacity' => 10,
                            'room' => $s[3],
                            'status' => 'open',
                        ]);

                        // Generate sessions for each schedule
                        for ($k = 1; $k <= $course->sessions_count; $k++) {
                            $sessionDate = Carbon::parse($sched->start_date)->addWeeks($k - 1);
                            CourseSession::create([
                                'course_id' => $course->id,
                                'schedule_id' => $sched->id,
                                'session_no' => $k,
                                'session_date' => $sessionDate->toDateString(),
                                'start_time' => $sched->start_time,
                                'end_time' => $sched->end_time,
                                'topic' => "Pertemuan {$k}: Materi Inti & Praktik {$course->title}",
                                'status' => $k <= 2 ? 'completed' : 'scheduled',
                            ]);
                        }
                    }

                    // Exam for each course
                    Exam::create([
                        'course_id' => $course->id,
                        'title' => "Final Challenge: {$course->title} ({$levelName})",
                        'passing_score' => 75,
                        'max_attempts' => 2,
                        'is_active' => true,
                    ]);

                    // Modules & Activities
                    $modulesList = [
                        ['Pengenalan & Fondasi Inti', 'Mengenal konsep dasar, perlengkapan/tools, dan tujuan belajar.'],
                        ['Latihan Praktik Terbimbing', 'Praktik langsung bersama mentor dan interaksi teman sekelas.'],
                        ['Proyek Karya Akhir & Evaluasi', 'Menerapkan seluruh keterampilan pada karya nyata mandiri.'],
                    ];

                    foreach ($modulesList as $mi => $m) {
                        $module = $course->modules()->create([
                            'title' => 'Modul ' . ($mi + 1) . ': ' . $m[0],
                            'description' => $m[1],
                            'sequence' => $mi + 1,
                        ]);

                        foreach ([['Materi: ' . $m[0], 'materi'], ['Latihan: ' . $m[0], 'latihan'], ['Refleksi: ' . $m[0], 'refleksi']] as $ai => $a) {
                            $module->activities()->create([
                                'title' => $a[0],
                                'type' => $a[1],
                                'sequence' => $ai + 1,
                            ]);
                        }
                    }

                    $courses[$slug] = $course;
                }
            }
        }

        // 6. Children Data
        $alya = Child::create([
            'parent_id' => $parent1->id,
            'name' => 'Alya Putri',
            'nickname' => 'Alya',
            'birth_date' => now()->subYears(9)->subMonths(2)->toDateString(),
            'avatar' => '🧒🏻',
            'interests' => ['Technology', 'Arts', 'Self-Improvement', 'Music'],
            'learning_preferences' => ['hands_on', 'group', 'step_by_step'],
        ]);

        $raka = Child::create([
            'parent_id' => $parent1->id,
            'name' => 'Raka Putra',
            'nickname' => 'Raka',
            'birth_date' => now()->subYears(6)->subMonths(1)->toDateString(),
            'avatar' => '👦🏻',
            'interests' => ['Arts', 'Sports', 'Music'],
            'learning_preferences' => ['play', 'hands_on'],
        ]);

        $kenzo = Child::create([
            'parent_id' => $parent2->id,
            'name' => 'Kenzo Pratama',
            'nickname' => 'Kenzo',
            'birth_date' => now()->subYears(10)->toDateString(),
            'avatar' => '👦🏻',
            'interests' => ['Technology', 'Sports', 'Languages'],
            'learning_preferences' => ['hands_on', 'step_by_step'],
        ]);

        $maya = Child::create([
            'parent_id' => $parent3->id,
            'name' => 'Maya Anggraini',
            'nickname' => 'Maya',
            'birth_date' => now()->subYears(8)->toDateString(),
            'avatar' => '👧🏻',
            'interests' => ['Self-Improvement', 'Arts', 'Music'],
            'learning_preferences' => ['group', 'play'],
        ]);

        // Co-design Session
        CoDesignSession::create([
            'child_id' => $alya->id,
            'parent_id' => $parent1->id,
            'child_choices' => ['Technology', 'Arts', 'Self-Improvement'],
            'parent_observations' => [
                'child_voice' => 'Aku suka membuat robot dan suka bercerita saat bikin karya seni lukis.',
                'discussed_with_child' => true,
            ],
            'agreed_interests' => ['Technology', 'Arts', 'Self-Improvement'],
            'learning_preferences' => ['hands_on', 'group', 'step_by_step'],
            'completed_at' => now(),
        ]);

        // Wishlists
        $engWish = Course::where('slug', 'like', 'english-conversation%')->first();
        if ($engWish) {
            Wishlist::create(['parent_id' => $parent1->id, 'course_id' => $engWish->id]);
        }
        $badWish = Course::where('slug', 'like', 'bulu-tangkis-dasar%')->first();
        if ($badWish) {
            Wishlist::create(['parent_id' => $parent1->id, 'course_id' => $badWish->id]);
        }

        // 7. Active Enrollments & Official Invoices
        $piano = Course::where('slug', 'like', 'piano-dasar%')->first();
        $robot = Course::where('slug', 'like', 'robotika-pengenalan%')->first();
        $vocal = Course::where('slug', 'like', 'vocal-dasar%')->first();
        $canvas = Course::where('slug', 'like', 'melukis-dasar%')->first();
        $confident = Course::where('slug', 'like', 'public-speaking-and-expression%')->first();
        $english = Course::where('slug', 'like', 'english-conversation%')->first();
        $badminton = Course::where('slug', 'like', 'bulu-tangkis-dasar%')->first();

        // Enrollment 1: Alya in Piano Dasar (Paid + Reschedule Request)
        if ($piano) {
            $pianoSched = $piano->schedules()->first();
            $pianoSched2 = $piano->schedules()->skip(1)->first();
            $enrollPiano = Enrollment::create([
                'parent_id' => $parent1->id,
                'child_id' => $alya->id,
                'course_id' => $piano->id,
                'schedule_id' => $pianoSched->id,
                'status' => 'active',
                'progress' => 35,
                'enrolled_at' => now()->subMonth(),
            ]);
            Transaction::create([
                'parent_id' => $parent1->id,
                'enrollment_id' => $enrollPiano->id,
                'invoice_code' => 'SP-DEMO-001',
                'subtotal' => $piano->price,
                'platform_fee' => 15000,
                'total' => $piano->price + 15000,
                'payment_method' => 'virtual_account',
                'status' => 'paid',
                'paid_at' => now()->subMonth(),
            ]);

            $pianoSession1 = $pianoSched->sessions()->first();
            $pianoSession2 = $pianoSched->sessions()->skip(1)->first();
            if ($pianoSession1) {
                $att1 = Attendance::create([
                    'enrollment_id' => $enrollPiano->id,
                    'course_session_id' => $pianoSession1->id,
                    'status' => 'excused',
                    'absence_reason' => 'Sakit Flu',
                    'credit_eligible' => true,
                    'mentor_note' => 'Izin sakit, diterbitkan kredit sesi pengganti.',
                ]);
                SessionCredit::create([
                    'child_id' => $alya->id,
                    'enrollment_id' => $enrollPiano->id,
                    'source_attendance_id' => $att1->id,
                    'credit_code' => 'CR-DEMO01',
                    'reason' => 'Sakit Flu',
                    'status' => 'available',
                    'expires_at' => now()->addDays(30),
                ]);
            }
            if ($pianoSession2) {
                Attendance::create([
                    'enrollment_id' => $enrollPiano->id,
                    'course_session_id' => $pianoSession2->id,
                    'status' => 'present',
                    'mentor_note' => 'Mengikuti tempo dengan sangat baik.',
                ]);
            }

            $examPiano = $piano->exams()->first();
            if ($examPiano) {
                ExamAttempt::create([
                    'exam_id' => $examPiano->id,
                    'enrollment_id' => $enrollPiano->id,
                    'attempt_no' => 1,
                    'score' => 78,
                    'status' => 'passed',
                    'mentor_feedback' => 'Bagus dalam membaca not balok dasar.',
                    'taken_at' => now()->subDays(3),
                ]);
            }

            if ($pianoSched2) {
                RescheduleRequest::create([
                    'enrollment_id' => $enrollPiano->id,
                    'parent_id' => $parent1->id,
                    'mentor_id' => $mentorMusic->id,
                    'current_schedule_id' => $pianoSched->id,
                    'requested_schedule_id' => $pianoSched2->id,
                    'reason' => 'Alya ada acara keluarga pada hari Sabtu pagi, mohon pindah ke jadwal siang.',
                    'status' => 'pending',
                    'is_read' => false,
                ]);
            }
        }

        // Enrollment 2: Maya in Vocal Dasar (Paid + Review)
        if ($vocal) {
            $vocalSched = $vocal->schedules()->first();
            $enrollVocal = Enrollment::create([
                'parent_id' => $parent3->id,
                'child_id' => $maya->id,
                'course_id' => $vocal->id,
                'schedule_id' => $vocalSched->id,
                'status' => 'active',
                'progress' => 50,
                'enrolled_at' => now()->subWeeks(2),
            ]);
            Transaction::create([
                'parent_id' => $parent3->id,
                'enrollment_id' => $enrollVocal->id,
                'invoice_code' => 'SP-DEMO-002',
                'subtotal' => $vocal->price,
                'platform_fee' => 15000,
                'total' => $vocal->price + 15000,
                'payment_method' => 'qris',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(2),
            ]);
            Review::create([
                'parent_id' => $parent3->id,
                'enrollment_id' => $enrollVocal->id,
                'course_id' => $vocal->id,
                'instructor_id' => $mentorMusic->id,
                'mentor_rating' => 5,
                'mentor_review' => 'Kak Naya sangat sabar dan membuat anak nyaman berekspresi lewat musik.',
                'platform_rating' => 5,
                'platform_review' => 'Pengalaman belajar sangat memuaskan.',
            ]);
            PlatformReview::create([
                'parent_id' => $parent3->id,
                'rating' => 5,
                'review' => 'Pengalaman belajar sangat memuaskan, antarmuka mudah dipahami dan customer service responsif.',
                'is_published' => true,
            ]);
        }

        // Enrollment 3: Alya in Robotika Pengenalan (Completed + Certificate)
        if ($robot) {
            $robotSched = $robot->schedules()->first();
            $enrollRobot = Enrollment::create([
                'parent_id' => $parent1->id,
                'child_id' => $alya->id,
                'course_id' => $robot->id,
                'schedule_id' => $robotSched->id,
                'status' => 'completed',
                'progress' => 100,
                'enrolled_at' => now()->subMonths(2),
                'completed_at' => now()->subDays(5),
                'final_status' => 'passed',
            ]);
            Transaction::create([
                'parent_id' => $parent1->id,
                'enrollment_id' => $enrollRobot->id,
                'invoice_code' => 'SP-DEMO-003',
                'subtotal' => $robot->price,
                'platform_fee' => 15000,
                'total' => $robot->price + 15000,
                'payment_method' => 'virtual_account',
                'status' => 'paid',
                'paid_at' => now()->subMonths(2),
            ]);
            $robotExam = $robot->exams()->first();
            if ($robotExam) {
                $robotAttempt = ExamAttempt::create([
                    'exam_id' => $robotExam->id,
                    'enrollment_id' => $enrollRobot->id,
                    'attempt_no' => 1,
                    'score' => 88,
                    'status' => 'passed',
                    'mentor_feedback' => 'Konsisten dan detail dalam merakit sensor motor robot.',
                    'taken_at' => now()->subDays(5),
                ]);
                Certificate::create([
                    'enrollment_id' => $enrollRobot->id,
                    'exam_attempt_id' => $robotAttempt->id,
                    'certificate_no' => 'CERT-SP-' . now()->format('Ym') . '-DEMO0001',
                    'issued_at' => now()->subDays(5),
                ]);
            }
            Review::create([
                'parent_id' => $parent1->id,
                'enrollment_id' => $enrollRobot->id,
                'course_id' => $robot->id,
                'instructor_id' => $mentorTech->id,
                'mentor_rating' => 5,
                'mentor_review' => 'Kak Dimas mengajarkan problem solving dengan sangat menyenangkan!',
                'platform_rating' => 5,
                'platform_review' => 'Platformnya rapi dan sertifikat langsung terbit.',
            ]);
            PlatformReview::create([
                'parent_id' => $parent1->id,
                'rating' => 5,
                'review' => 'Platformnya rapi, jadwal transparan, dan sertifikat kelulusan langsung terbit.',
                'is_published' => true,
            ]);
        }

        // Enrollment 4: Kenzo in Robotika Pengenalan (Active)
        if ($robot) {
            $robotSched = $robot->schedules()->first();
            $enrollKenzoRobot = Enrollment::create([
                'parent_id' => $parent2->id,
                'child_id' => $kenzo->id,
                'course_id' => $robot->id,
                'schedule_id' => $robotSched->id,
                'status' => 'active',
                'progress' => 60,
                'enrolled_at' => now()->subWeeks(3),
            ]);
            Transaction::create([
                'parent_id' => $parent2->id,
                'enrollment_id' => $enrollKenzoRobot->id,
                'invoice_code' => 'SP-DEMO-004',
                'subtotal' => $robot->price,
                'platform_fee' => 15000,
                'total' => $robot->price + 15000,
                'payment_method' => 'bank_transfer',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(3),
            ]);
        }

        // Enrollment 5: Raka in Melukis Dasar
        if ($canvas) {
            $canvasSched = $canvas->schedules()->first();
            $enrollCanvas = Enrollment::create([
                'parent_id' => $parent1->id,
                'child_id' => $raka->id,
                'course_id' => $canvas->id,
                'schedule_id' => $canvasSched->id,
                'status' => 'active',
                'progress' => 45,
                'enrolled_at' => now()->subWeeks(3),
            ]);
            Transaction::create([
                'parent_id' => $parent1->id,
                'enrollment_id' => $enrollCanvas->id,
                'invoice_code' => 'SP-DEMO-005',
                'subtotal' => $canvas->price,
                'platform_fee' => 15000,
                'total' => $canvas->price + 15000,
                'payment_method' => 'gopay',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(3),
            ]);
        }

        // Enrollment 6: Maya in Public Speaking & Expression
        if ($confident) {
            $confidentSched = $confident->schedules()->first();
            $enrollConfident = Enrollment::create([
                'parent_id' => $parent3->id,
                'child_id' => $maya->id,
                'course_id' => $confident->id,
                'schedule_id' => $confidentSched->id,
                'status' => 'active',
                'progress' => 50,
                'enrolled_at' => now()->subWeeks(2),
            ]);
            Transaction::create([
                'parent_id' => $parent3->id,
                'enrollment_id' => $enrollConfident->id,
                'invoice_code' => 'SP-DEMO-006',
                'subtotal' => $confident->price,
                'platform_fee' => 15000,
                'total' => $confident->price + 15000,
                'payment_method' => 'virtual_account',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(2),
            ]);
        }

        // Enrollment 7: Kenzo in English Conversation
        if ($english) {
            $englishSched = $english->schedules()->first();
            $enrollEnglish = Enrollment::create([
                'parent_id' => $parent2->id,
                'child_id' => $kenzo->id,
                'course_id' => $english->id,
                'schedule_id' => $englishSched->id,
                'status' => 'active',
                'progress' => 40,
                'enrolled_at' => now()->subWeeks(2),
            ]);
            Transaction::create([
                'parent_id' => $parent2->id,
                'enrollment_id' => $enrollEnglish->id,
                'invoice_code' => 'SP-DEMO-007',
                'subtotal' => $english->price,
                'platform_fee' => 15000,
                'total' => $english->price + 15000,
                'payment_method' => 'shopeepay',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(2),
            ]);
        }

        // Enrollment 8: Kenzo in Bulu Tangkis Dasar
        if ($badminton) {
            $badmintonSched = $badminton->schedules()->first();
            $enrollBadminton = Enrollment::create([
                'parent_id' => $parent2->id,
                'child_id' => $kenzo->id,
                'course_id' => $badminton->id,
                'schedule_id' => $badmintonSched->id,
                'status' => 'active',
                'progress' => 30,
                'enrolled_at' => now()->subWeeks(1),
            ]);
            Transaction::create([
                'parent_id' => $parent2->id,
                'enrollment_id' => $enrollBadminton->id,
                'invoice_code' => 'SP-DEMO-008',
                'subtotal' => $badminton->price,
                'platform_fee' => 15000,
                'total' => $badminton->price + 15000,
                'payment_method' => 'virtual_account',
                'status' => 'paid',
                'paid_at' => now()->subWeeks(1),
            ]);
        }

        // 8. Learning Path for Alya
        $path = LearningPath::create([
            'child_id' => $alya->id,
            'title' => 'Creative Explorer Path',
            'rationale' => 'Rekomendasi kurikulum berjenjang Technology, Arts, dan Self-Improvement dari hasil co-design.',
            'status' => 'active',
            'generated_at' => now(),
        ]);

        $pathCourses = array_filter([
            $confident,
            Course::where('slug', 'like', 'coding-dasar-anak%')->first(),
            Course::where('slug', 'like', 'ilustrasi%')->first(),
        ]);

        foreach (array_values($pathCourses) as $i => $c) {
            $path->items()->create([
                'course_id' => $c->id,
                'sequence' => $i + 1,
                'reason' => 'Sesuai minat anak dan tingkat level perkembangan usianya',
                'status' => $i === 0 ? 'recommended' : 'locked',
                'match_score' => 95 - $i * 8,
            ]);
        }
    }
}
