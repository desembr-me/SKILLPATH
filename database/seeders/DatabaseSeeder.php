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

        // 3. Categories
        $categories = collect([
            ['Arts', 'arts', 'Kreativitas visual, craft, drawing, dan ekspresi artistik.', '#FFE6D8'],
            ['Music', 'music', 'Piano, ritme, vokal, dan eksplorasi musikal.', '#FFF2B9'],
            ['Languages', 'languages', 'Komunikasi, speaking, storytelling, dan bahasa asing.', '#DDF2FF'],
            ['Sports', 'sports', 'Gerak aktif, koordinasi, kebugaran, dan sportivitas.', '#DFF4E5'],
            ['Self Improvement', 'self-improvement', 'Percaya diri, kemampuan sosial, emosi, dan komunikasi.', '#FFE3EC'],
            ['Technology', 'technology', 'Coding, robotik, logika, dan kreasi digital.', '#EAE5FF'],
        ])->mapWithKeys(function ($x) {
            $c = Category::create([
                'name' => $x[0],
                'slug' => $x[1],
                'description' => $x[2],
                'accent' => $x[3],
            ]);
            return [$x[0] => $c];
        });

        // 4. Mentors (6 Full Mentors)
        $mentor1 = User::create([
            'name' => 'Naya Rahma',
            'email' => 'mentor@skillpath.test',
            'phone' => '081298765432',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Music']->id,
            'avatar' => 'avatars/mentors/naya.png',
            'headline' => 'Mentor Piano & Musik Anak',
            'bio' => 'Mengajar piano untuk anak sejak 7 tahun, fokus pada fondasi ritme dan kepercayaan diri tampil.',
        ]);

        $mentor2 = User::create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@skillpath.test',
            'phone' => '081298765433',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Technology']->id,
            'avatar' => 'avatars/mentors/dimas.png',
            'headline' => 'Mentor Robotika & Coding Anak',
            'bio' => 'Membimbing anak merakit robot dan belajar logika pemrograman lewat proyek langsung.',
        ]);

        $mentor3 = User::create([
            'name' => 'Sari Wulandari',
            'email' => 'sari@skillpath.test',
            'phone' => '081298765434',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Arts']->id,
            'avatar' => 'avatars/mentors/sari.png',
            'headline' => 'Mentor Seni & Ilustrasi Anak',
            'bio' => 'Mendampingi anak bereksplorasi warna, bentuk, dan bercerita lewat karya visual.',
        ]);

        $mentor4 = User::create([
            'name' => 'Bimo Aditya',
            'email' => 'bimo@skillpath.test',
            'phone' => '081298765435',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Self Improvement']->id,
            'avatar' => 'avatars/mentors/bimo.png',
            'headline' => 'Mentor Pengembangan Diri Anak',
            'bio' => 'Fokus membangun kepercayaan diri, kemampuan sosial, dan pengelolaan emosi anak lewat aktivitas kelompok.',
        ]);

        $mentor5 = User::create([
            'name' => 'Clara Simanjuntak',
            'email' => 'clara@skillpath.test',
            'phone' => '081298765436',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Languages']->id,
            'avatar' => 'avatars/mentors/clara.png',
            'headline' => 'Mentor Bahasa Inggris Anak',
            'bio' => 'Mengajak anak berani berbahasa Inggris lewat storytelling dan permainan kelompok.',
        ]);

        $mentor6 = User::create([
            'name' => 'Fajar Nugroho',
            'email' => 'fajar@skillpath.test',
            'phone' => '081298765437',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'category_id' => $categories['Sports']->id,
            'avatar' => 'avatars/mentors/fajar.png',
            'headline' => 'Mentor Olahraga Anak',
            'bio' => 'Melatih teknik dasar olahraga dengan pendekatan yang menyenangkan dan aman untuk anak.',
        ]);

        // 5. Courses Data
        $courseData = [
            ['Technology', 'Junior Robotics Lab', 'junior-robotics-lab', 'Build, test, improve.', 'Anak merakit robot sederhana, mengenal sensor, logika, dan problem solving melalui proyek langsung.', 8, 10, 850000, 8, 90, 'SkillHub Kemang', 'Jakarta Selatan', '🤖', '#EAE5FF', $mentor2, true],
            ['Arts', 'Little Canvas Club', 'little-canvas-club', 'Create what you imagine.', 'Eksplorasi warna, bentuk, tekstur, dan craft untuk melatih kreativitas dan motorik halus.', 5, 7, 620000, 6, 75, 'Studio Warna Tebet', 'Jakarta Selatan', '🎨', '#FFE6D8', $mentor3, false],
            ['Self Improvement', 'Confident Kids Club', 'confident-kids-club', 'Speak, connect, grow.', 'Roleplay, permainan kelompok, dan aktivitas refleksi untuk membangun percaya diri serta kemampuan sosial.', 8, 10, 690000, 6, 90, 'GrowSpace Bintaro', 'Tangerang Selatan', '🌱', '#FFE3EC', $mentor4, true],
            ['Music', 'Piano Starter', 'piano-starter', 'Play your first song.', 'Belajar fondasi piano, ritme, koordinasi tangan, dan lagu sederhana secara bertahap.', 8, 10, 780000, 8, 60, 'Nada Studio Cipete', 'Jakarta Selatan', '🎹', '#FFF2B9', $mentor1, false],
            ['Languages', 'English Adventure', 'english-adventure', 'Speak through stories.', 'Speaking, storytelling, games, dan aktivitas kelompok untuk membangun keberanian berbahasa Inggris.', 11, 14, 720000, 8, 90, 'BrightTalk Depok', 'Depok', '💬', '#DDF2FF', $mentor5, false],
            ['Sports', 'Junior Badminton', 'junior-badminton', 'Move with confidence.', 'Teknik dasar badminton, koordinasi, kebugaran, kerja sama, dan sportivitas.', 11, 14, 760000, 8, 90, 'Arena Cilandak', 'Jakarta Selatan', '🏸', '#DFF4E5', $mentor6, false],
            ['Technology', 'Mini Coding Explorer', 'mini-coding-explorer', 'Logic before screens.', 'Pola, urutan, algoritma sederhana, dan logika komputasional melalui permainan serta aktivitas praktik.', 5, 7, 650000, 6, 75, 'Makerspace Bekasi', 'Bekasi', '🧩', '#EAE5FF', $mentor2, true],
            ['Self Improvement', 'Social Skills Playgroup', 'social-skills-playgroup', 'Practice friendship skills.', 'Kegiatan kelompok untuk melatih bergiliran, mendengar, menyampaikan kebutuhan, dan kerja sama.', 5, 7, 680000, 6, 75, 'KindSpace Pondok Indah', 'Jakarta Selatan', '🤝', '#FFE3EC', $mentor4, false],
            ['Arts', 'Young Illustrator', 'young-illustrator', 'Turn ideas into visual stories.', 'Ilustrasi karakter, komposisi, warna, dan proyek akhir berupa karya personal.', 11, 14, 740000, 8, 90, 'Creative Lab Rawamangun', 'Jakarta Timur', '✏️', '#FFE6D8', $mentor3, false],
            ['Music', 'Vocal Kids Ensemble', 'vocal-kids-ensemble', 'Sing with joy and harmony.', 'Latihan olah vokal anak, intonasi, pernapasan, dan harmoni bernyanyi bersama dalam kelompok.', 6, 12, 700000, 8, 60, 'Nada Studio Cipete', 'Jakarta Selatan', '🎤', '#FFF2B9', $mentor1, true],
        ];

        $coverImages = [
            'junior-robotics-lab' => 'tech-06.jpg',
            'little-canvas-club' => 'arts-01.jpg',
            'confident-kids-club' => 'Confidence Building.jpg',
            'piano-starter' => 'music-Piano.jpg',
            'english-adventure' => 'english convo.jpg',
            'junior-badminton' => 'Junior Sports Skills.jpg',
            'mini-coding-explorer' => 'tech-08.jpg',
            'social-skills-playgroup' => 'Effective Communication.jpg',
            'young-illustrator' => 'Young Artist Portfolio.jpg',
            'vocal-kids-ensemble' => 'music-vocal.jpg',
        ];

        $courses = [];
        foreach ($courseData as $x) {
            $course = Course::create([
                'category_id' => $categories[$x[0]]->id,
                'instructor_id' => $x[14]->id,
                'title' => $x[1],
                'slug' => $x[2],
                'subtitle' => $x[3],
                'description' => $x[4],
                'age_min' => $x[5],
                'age_max' => $x[6],
                'price' => $x[7],
                'sessions_count' => $x[8],
                'duration_minutes' => $x[9],
                'location_name' => $x[10],
                'city' => $x[11],
                'cover_emoji' => $x[12],
                'cover_image' => $coverImages[$x[2]] ?? null,
                'accent' => $x[13],
                'is_featured' => $x[15],
                'status' => 'active',
            ]);

            // Schedules (3 schedules per course)
            $scheduleConfigs = [
                [6, '10:00:00', '11:30:00', 'Studio A'],
                [6, '13:00:00', '14:30:00', 'Studio B'],
                [0, '10:00:00', '11:30:00', 'Studio Utama'],
            ];

            foreach ($scheduleConfigs as $j => $s) {
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

                // Generate full sessions for all schedules
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

            // Exam
            Exam::create([
                'course_id' => $course->id,
                'title' => 'Final Challenge: ' . $course->title,
                'passing_score' => 75,
                'max_attempts' => 2,
                'is_active' => true,
            ]);

            // Modules & Activities
            foreach ([['Pengenalan', 'Mengenal dasar dan tujuan course.'], ['Latihan Inti', 'Praktik langsung bersama mentor.'], ['Proyek Akhir', 'Menerapkan keterampilan pada proyek nyata.']] as $mi => $m) {
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
            $courses[$x[1]] = $course;
        }

        // 6. Children
        $alya = Child::create([
            'parent_id' => $parent1->id,
            'name' => 'Alya Putri',
            'nickname' => 'Alya',
            'birth_date' => now()->subYears(9)->subMonths(2)->toDateString(),
            'avatar' => '🧒🏻',
            'interests' => ['Technology', 'Arts', 'Self Improvement', 'Music'],
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
            'interests' => ['Self Improvement', 'Arts', 'Music'],
            'learning_preferences' => ['group', 'play'],
        ]);

        // Co-design
        CoDesignSession::create([
            'child_id' => $alya->id,
            'parent_id' => $parent1->id,
            'child_choices' => ['Technology', 'Arts', 'Self Improvement'],
            'parent_observations' => [
                'child_voice' => 'Aku suka membuat robot dan suka cerita saat bikin karya seni.',
                'discussed_with_child' => true,
            ],
            'agreed_interests' => ['Technology', 'Arts', 'Self Improvement'],
            'learning_preferences' => ['hands_on', 'group', 'step_by_step'],
            'completed_at' => now(),
        ]);

        // Wishlists
        Wishlist::create(['parent_id' => $parent1->id, 'course_id' => $courses['English Adventure']->id]);
        Wishlist::create(['parent_id' => $parent1->id, 'course_id' => $courses['Junior Badminton']->id]);

        // 7. Enrollments & Transactions for all Mentors
        $robot = $courses['Junior Robotics Lab'];
        $piano = $courses['Piano Starter'];
        $vocal = $courses['Vocal Kids Ensemble'];
        $canvas = $courses['Little Canvas Club'];
        $confident = $courses['Confident Kids Club'];
        $english = $courses['English Adventure'];
        $badminton = $courses['Junior Badminton'];

        // --- ENROLLMENT 1: Alya in Piano Starter (Mentor 1 - Naya)
        $pianoSched1 = $piano->schedules()->first();
        $pianoSched2 = $piano->schedules()->skip(1)->first();
        $enrollPiano = Enrollment::create([
            'parent_id' => $parent1->id,
            'child_id' => $alya->id,
            'course_id' => $piano->id,
            'schedule_id' => $pianoSched1->id,
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
        $pianoSession1 = $pianoSched1->sessions()->first();
        $pianoSession2 = $pianoSched1->sessions()->skip(1)->first();
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
        if ($pianoSession2) {
            Attendance::create([
                'enrollment_id' => $enrollPiano->id,
                'course_session_id' => $pianoSession2->id,
                'status' => 'present',
                'mentor_note' => 'Mengikuti tempo dengan baik.',
            ]);
        }
        $examPiano = $piano->exams()->first();
        ExamAttempt::create([
            'exam_id' => $examPiano->id,
            'enrollment_id' => $enrollPiano->id,
            'attempt_no' => 1,
            'score' => 78,
            'status' => 'passed',
            'mentor_feedback' => 'Bagus dalam membaca not balok dasar.',
            'taken_at' => now()->subDays(3),
        ]);

        // Reschedule request for Naya (Pending!)
        RescheduleRequest::create([
            'enrollment_id' => $enrollPiano->id,
            'parent_id' => $parent1->id,
            'mentor_id' => $mentor1->id,
            'current_schedule_id' => $pianoSched1->id,
            'requested_schedule_id' => $pianoSched2->id,
            'reason' => 'Alya ada acara keluarga pada hari Sabtu pagi, mohon pindah ke jadwal siang.',
            'status' => 'pending',
            'is_read' => false,
        ]);

        // --- ENROLLMENT 2: Maya in Vocal Ensemble (Mentor 1 - Naya)
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
            'instructor_id' => $mentor1->id,
            'mentor_rating' => 5,
            'mentor_review' => 'Kak Naya sangat sabar dan membuat anak nyaman berekspresi lewat musik.',
            'platform_rating' => 5,
            'platform_review' => 'Pengalaman belajar sangat memuaskan.',
        ]);

        // --- ENROLLMENT 3: Alya in Junior Robotics Lab (Mentor 2 - Dimas) (Completed)
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
        $robotAttempt = ExamAttempt::create([
            'exam_id' => $robotExam->id,
            'enrollment_id' => $enrollRobot->id,
            'attempt_no' => 1,
            'score' => 88,
            'status' => 'passed',
            'mentor_feedback' => 'Konsisten dan detail dalam merakit sensor.',
            'taken_at' => now()->subDays(5),
        ]);
        Certificate::create([
            'enrollment_id' => $enrollRobot->id,
            'exam_attempt_id' => $robotAttempt->id,
            'certificate_no' => 'CERT-SP-' . now()->format('Ym') . '-DEMO0001',
            'issued_at' => now()->subDays(5),
        ]);
        Review::create([
            'parent_id' => $parent1->id,
            'enrollment_id' => $enrollRobot->id,
            'course_id' => $robot->id,
            'instructor_id' => $robot->instructor_id,
            'mentor_rating' => 5,
            'mentor_review' => 'Kak Dimas mengajarkan problem solving dengan sangat menyenangkan!',
            'platform_rating' => 5,
            'platform_review' => 'Platformnya rapi dan sertifikat langsung terbit.',
        ]);

        // --- ENROLLMENT 4: Kenzo in Junior Robotics (Mentor 2 - Dimas) (Active)
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

        // --- ENROLLMENT 5: Raka in Little Canvas Club (Mentor 3 - Sari)
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
        Review::create([
            'parent_id' => $parent1->id,
            'enrollment_id' => $enrollCanvas->id,
            'course_id' => $canvas->id,
            'instructor_id' => $mentor3->id,
            'mentor_rating' => 5,
            'mentor_review' => 'Kak Sari hebat membuat anak-anak usia dini antusias melukis.',
            'platform_rating' => 5,
            'platform_review' => 'Tempat belajarnya bersih dan nyaman.',
        ]);

        // --- ENROLLMENT 6: Maya in Confident Kids Club (Mentor 4 - Bimo)
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
        Review::create([
            'parent_id' => $parent3->id,
            'enrollment_id' => $enrollConfident->id,
            'course_id' => $confident->id,
            'instructor_id' => $mentor4->id,
            'mentor_rating' => 5,
            'mentor_review' => 'Kak Bimo luar biasa membangun rasa percaya diri Maya saat berbicara.',
            'platform_rating' => 5,
            'platform_review' => 'Sangat recommended!',
        ]);

        // --- ENROLLMENT 7: Kenzo in English Adventure (Mentor 5 - Clara)
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

        // --- ENROLLMENT 8: Kenzo in Junior Badminton (Mentor 6 - Fajar)
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

        // 8. Learning Path for Alya
        $path = LearningPath::create([
            'child_id' => $alya->id,
            'title' => 'Creative Explorer Path',
            'rationale' => 'Gabungan Technology, Arts, dan Self Improvement dari hasil co-design.',
            'status' => 'active',
            'generated_at' => now(),
        ]);
        foreach ([$courses['Confident Kids Club'], $courses['Mini Coding Explorer'], $courses['Young Illustrator']] as $i => $c) {
            $path->items()->create([
                'course_id' => $c->id,
                'sequence' => $i + 1,
                'reason' => 'Sesuai minat dan usia Alya',
                'status' => $i === 0 ? 'recommended' : 'locked',
                'match_score' => 95 - $i * 8,
            ]);
        }
    }
}
