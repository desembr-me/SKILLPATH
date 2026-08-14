<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\FinalExam;
use App\Models\ExamAttempt;
use App\Models\InstructorProfile;
use App\Models\Interest;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\Module;
use App\Models\Order;
use App\Models\Progress;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $interests = collect([
            ['name'=>'Teknologi','icon'=>'⌨','description'=>'Coding, perangkat digital, dan kreasi teknologi.'],
            ['name'=>'Seni','icon'=>'✎','description'=>'Menggambar, bercerita, desain, dan ekspresi kreatif.'],
            ['name'=>'Sains','icon'=>'⚗','description'=>'Eksperimen, observasi, dan rasa ingin tahu.'],
            ['name'=>'Komunikasi','icon'=>'☏','description'=>'Berbicara, mendengar, dan menyampaikan ide.'],
            ['name'=>'Kewirausahaan','icon'=>'◎','description'=>'Ide usaha, nilai, perencanaan, dan tanggung jawab.'],
            ['name'=>'Kehidupan Sehari-hari','icon'=>'⌂','description'=>'Kemandirian, kebiasaan baik, dan keterampilan praktis.'],
            ['name'=>'Pengembangan Diri','icon'=>'✧','description'=>'Percaya diri, mengenali emosi, kemampuan sosial, dan kebiasaan positif.'],
        ])->mapWithKeys(function($item){$m=Interest::updateOrCreate(['slug'=>Str::slug($item['name'])],$item+['slug'=>Str::slug($item['name'])]);return[$m->slug=>$m];});

        $skills = collect([
            ['name'=>'Kreativitas','icon'=>'✦','description'=>'Mengembangkan ide dan membuat karya.'],
            ['name'=>'Literasi Digital','icon'=>'▣','description'=>'Menggunakan teknologi secara produktif dan bertanggung jawab.'],
            ['name'=>'Komunikasi','icon'=>'●','description'=>'Menyampaikan gagasan secara jelas dan percaya diri.'],
            ['name'=>'Problem Solving','icon'=>'◇','description'=>'Menganalisis masalah dan memilih solusi.'],
            ['name'=>'Kemandirian','icon'=>'✓','description'=>'Mengatur tugas dan kebiasaan sehari-hari.'],
        ])->mapWithKeys(function($item){$m=Skill::updateOrCreate(['slug'=>Str::slug($item['name'])],$item+['slug'=>Str::slug($item['name'])]);return[$m->slug=>$m];});

        $categories=collect([
            ['name'=>'Arts','slug'=>'arts','icon'=>'✦','description'=>'Mengembangkan kreativitas melalui gambar, cerita, desain, dan karya visual.'],
            ['name'=>'Languages','slug'=>'languages','icon'=>'Aa','description'=>'Melatih kemampuan berbicara, mendengar, kosakata, dan menyampaikan gagasan.'],
            ['name'=>'Music','slug'=>'music','icon'=>'♫','description'=>'Mengenal ritme, bunyi, tempo, dan ekspresi melalui aktivitas musik sederhana.'],
            ['name'=>'Sports','slug'=>'sports','icon'=>'●','description'=>'Mengembangkan koordinasi gerak, kebugaran, disiplin, dan sportivitas.'],
            ['name'=>'Technology','slug'=>'technology','icon'=>'</>','description'=>'Mengenal logika digital, coding, teknologi, dan pemecahan masalah secara bertahap.'],
            ['name'=>'Self-Improvement','slug'=>'self-improvement','icon'=>'✧','description'=>'Pengembangan diri anak melalui kemampuan sosial, emosional, percaya diri, kemandirian, dan kebiasaan positif.'],
        ])->mapWithKeys(function($item){$m=Category::updateOrCreate(['slug'=>$item['slug']],$item);return[$m->slug=>$m];});

        $admin=User::updateOrCreate(['email'=>'admin@skillpath.test'],['name'=>'Admin SKILLPATH','password'=>Hash::make('password'),'role'=>'admin']);

        $instructorData=[
            ['name'=>'Naila Prameswari','email'=>'naila@skillpath.test','headline'=>'Pengajar seni visual dan creative storytelling anak','expertise'=>'Drawing, storytelling, visual creativity','years'=>6,'education'=>'S1 Pendidikan Seni','rating'=>4.9],
            ['name'=>'Arif Nugraha','email'=>'arif@skillpath.test','headline'=>'Mentor coding visual dan computational thinking','expertise'=>'Scratch, coding visual, problem solving','years'=>7,'education'=>'S1 Pendidikan Teknologi Informasi','rating'=>4.8],
            ['name'=>'Maya Lestari','email'=>'maya@skillpath.test','headline'=>'Coach komunikasi, bahasa, dan public speaking anak','expertise'=>'Public speaking, English conversation, communication','years'=>8,'education'=>'S1 Pendidikan Bahasa','rating'=>4.9],
            ['name'=>'Raka Aditya','email'=>'raka@skillpath.test','headline'=>'Coach aktivitas fisik dan sportivitas anak','expertise'=>'Motor skills, physical literacy, sportsmanship','years'=>6,'education'=>'S1 Pendidikan Jasmani','rating'=>4.8],
            ['name'=>'Dinda Maharani','email'=>'dinda@skillpath.test','headline'=>'Pengajar musik dasar dan eksplorasi ritme','expertise'=>'Rhythm, basic music, creative sound','years'=>5,'education'=>'S1 Pendidikan Musik','rating'=>4.9],
        ];
        $instructors=collect($instructorData)->mapWithKeys(function($d){
            $u=User::updateOrCreate(['email'=>$d['email']],['name'=>$d['name'],'password'=>Hash::make('password'),'role'=>'instructor']);
            InstructorProfile::updateOrCreate(['user_id'=>$u->id],[
                'headline'=>$d['headline'],'bio'=>$d['headline'].'. Pembelajaran menekankan praktik bertahap, instruksi sederhana, dan umpan balik positif yang sesuai usia anak.',
                'expertise'=>$d['expertise'],'years_experience'=>$d['years'],'education'=>$d['education'],'is_verified'=>true,'rating'=>$d['rating'],'students_count'=>120,
            ]);
            return[$u->email=>$u];
        });

        $paths=[
            // ARTS
            ['title'=>'Studio Cerita Kreatif','category'=>'arts','level'=>'Beginner','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>5,'max'=>10,'duration'=>180,'icon'=>'✎','thumbnail'=>'foto/arts-01.jpg','price'=>179000,'sale'=>129000,'interests'=>['seni','komunikasi'],'description'=>'Membuat tokoh, alur, ilustrasi, dan cerita pendek melalui project kreatif.','outcomes'=>'Anak mampu membuat tokoh, menyusun cerita tiga bagian, dan mempresentasikan karya sederhana.','requirements'=>'Kertas gambar, pensil warna, dan pendampingan orang tua untuk anak usia 5–7 tahun.'],
            ['title'=>'Creative Starter Gratis','category'=>'arts','level'=>'Beginner','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>5,'max'=>14,'duration'=>45,'icon'=>'✦','thumbnail'=>'foto/arts-07.jpg','price'=>0,'sale'=>null,'interests'=>['seni'],'description'=>'Kelas pengenalan gratis untuk mencoba pengalaman belajar tatap muka SKILLPATH.','outcomes'=>'Anak menyelesaikan satu project kreatif singkat dan mengenal alur belajar SKILLPATH.','requirements'=>'Kertas dan alat tulis.'],
            ['title'=>'Painting & Mixed Media Lab','category'=>'arts','level'=>'Intermediate','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>8,'max'=>14,'duration'=>240,'icon'=>'✎','thumbnail'=>'foto/arts-02.jpg','price'=>229000,'sale'=>189000,'interests'=>['seni'],'description'=>'Eksplorasi warna, komposisi, tekstur, dan mixed media melalui karya bertema.','outcomes'=>'Anak mampu merencanakan komposisi dan menghasilkan karya mixed media dengan teknik yang lebih terarah.','requirements'=>'Gunakan pakaian yang nyaman untuk aktivitas seni.'],
            ['title'=>'Young Artist Portfolio','category'=>'arts','level'=>'Expert','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>10,'max'=>14,'duration'=>360,'icon'=>'✦','thumbnail'=>'foto/young-artist-portfolio.jpg','price'=>349000,'sale'=>299000,'interests'=>['seni'],'description'=>'Program proyek untuk menyusun seri karya dan mini portfolio sesuai gaya visual anak.','outcomes'=>'Anak menghasilkan seri karya tematik dan mampu menjelaskan ide serta proses kreatifnya.','requirements'=>'Membawa beberapa karya sebelumnya sebagai bahan diskusi awal.'],

            // MUSIC
            ['title'=>'Ritme dan Musik Dasar','category'=>'music','level'=>'Beginner','skill'=>'kreativitas','instructor'=>'dinda@skillpath.test','min'=>5,'max'=>11,'duration'=>180,'icon'=>'♫','thumbnail'=>'foto/music-09.jpg','price'=>169000,'sale'=>129000,'interests'=>['seni'],'description'=>'Mengenal ritme, tempo, pola bunyi, dan ekspresi musik melalui permainan sederhana.','outcomes'=>'Anak mengenali tempo, menirukan pola ketukan, dan membuat pola bunyi sendiri.','requirements'=>'Tidak wajib membawa alat musik pada pertemuan pertama.'],
            ['title'=>'Melody & Ensemble Club','category'=>'music','level'=>'Intermediate','skill'=>'kreativitas','instructor'=>'dinda@skillpath.test','min'=>8,'max'=>14,'duration'=>240,'icon'=>'♫','thumbnail'=>'foto/music-09.jpg','price'=>229000,'sale'=>189000,'interests'=>['seni','komunikasi'],'description'=>'Berlatih melodi, dinamika, dan bermain bersama dalam kelompok kecil.','outcomes'=>'Anak mampu mengikuti pola melodi sederhana dan menjaga tempo saat bermain bersama.','requirements'=>'Alat musik dasar sesuai arahan pengajar.'],
            ['title'=>'Junior Performance Project','category'=>'music','level'=>'Expert','skill'=>'komunikasi','instructor'=>'dinda@skillpath.test','min'=>10,'max'=>14,'duration'=>360,'icon'=>'♫','thumbnail'=>'foto/junior-performance-project.jpg','price'=>349000,'sale'=>299000,'interests'=>['seni','komunikasi'],'description'=>'Menyiapkan satu penampilan musik lengkap dari latihan, aransemen sederhana, sampai tampil.','outcomes'=>'Anak mampu menyiapkan dan membawakan satu karya musik dengan ekspresi dan kerja tim yang baik.','requirements'=>'Sudah memiliki pengalaman dasar memainkan instrumen atau vokal.'],

            // SELF IMPROVEMENT
            ['title'=>'Kenali Emosi dan Percaya Diri','category'=>'self-improvement','level'=>'Beginner','skill'=>'kemandirian','instructor'=>'maya@skillpath.test','min'=>6,'max'=>12,'duration'=>180,'icon'=>'✧','thumbnail'=>'foto/emotional-intelligence.jpg','price'=>179000,'sale'=>139000,'interests'=>['pengembangan-diri','komunikasi'],'description'=>'Membantu anak mengenali emosi, menyampaikan kebutuhan, dan membangun percaya diri.','outcomes'=>'Anak mampu mengenali emosi dasar dan menggunakan kalimat sederhana untuk menyampaikan kebutuhan.','requirements'=>'Pendampingan orang tua pada aktivitas refleksi untuk anak usia lebih kecil.'],
            ['title'=>'Social Skills Adventure','category'=>'self-improvement','level'=>'Intermediate','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>8,'max'=>14,'duration'=>240,'icon'=>'✧','thumbnail'=>'foto/effective-communication.jpg','price'=>219000,'sale'=>179000,'interests'=>['pengembangan-diri','komunikasi'],'description'=>'Latihan komunikasi, teamwork, empati, menyelesaikan konflik kecil, dan mengambil keputusan melalui simulasi.','outcomes'=>'Anak mampu berkomunikasi lebih asertif dan bekerja sama dalam aktivitas kelompok.','requirements'=>'Bersedia mengikuti permainan kelompok dan role play.'],
            ['title'=>'Young Leader Project','category'=>'self-improvement','level'=>'Expert','skill'=>'kemandirian','instructor'=>'maya@skillpath.test','min'=>10,'max'=>14,'duration'=>360,'icon'=>'✧','thumbnail'=>'foto/confidence-building.jpg','price'=>329000,'sale'=>279000,'interests'=>['pengembangan-diri','komunikasi','kewirausahaan'],'description'=>'Program kepemimpinan anak melalui perencanaan proyek, pembagian peran, presentasi, dan refleksi.','outcomes'=>'Anak mampu memimpin proyek kecil, membagi tugas, dan mempresentasikan hasil bersama tim.','requirements'=>'Sudah nyaman bekerja dalam kelompok dan mengikuti diskusi.'],

            // LANGUAGES
            ['title'=>'English Fun Conversation','category'=>'languages','level'=>'Beginner','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>7,'max'=>12,'duration'=>220,'icon'=>'Aa','thumbnail'=>'foto/english-convo.jpg','price'=>229000,'sale'=>179000,'interests'=>['komunikasi'],'description'=>'Kelas percakapan bahasa Inggris berbasis permainan, topik sehari-hari, dan role play.','outcomes'=>'Anak mampu menggunakan ungkapan dasar untuk memperkenalkan diri, bertanya, dan merespons.','requirements'=>'Bawa alat tulis dan hadir 10 menit sebelum kelas dimulai.'],
            ['title'=>'Berani Bicara','category'=>'languages','level'=>'Intermediate','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>8,'max'=>14,'duration'=>200,'icon'=>'Aa','thumbnail'=>'foto/berani-bicara.jpg','price'=>199000,'sale'=>159000,'interests'=>['komunikasi'],'description'=>'Melatih struktur bicara, volume suara, mendengar aktif, storytelling, dan presentasi singkat.','outcomes'=>'Anak mampu menyampaikan ide singkat secara terstruktur dan percaya diri.','requirements'=>'Bawa alat tulis dan botol minum.'],
            ['title'=>'Junior English Presenter','category'=>'languages','level'=>'Expert','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>10,'max'=>14,'duration'=>320,'icon'=>'Aa','thumbnail'=>'foto/english-convo.jpg','price'=>319000,'sale'=>269000,'interests'=>['komunikasi'],'description'=>'Kelas lanjutan untuk presentasi, storytelling, improvisasi, dan diskusi bahasa Inggris.','outcomes'=>'Anak mampu menyusun dan membawakan presentasi bahasa Inggris dengan struktur yang jelas.','requirements'=>'Memiliki kemampuan percakapan dasar bahasa Inggris.'],

            // SPORTS
            ['title'=>'Gerak Aktif dan Sportivitas','category'=>'sports','level'=>'Beginner','skill'=>'kemandirian','instructor'=>'raka@skillpath.test','min'=>6,'max'=>14,'duration'=>150,'icon'=>'●','thumbnail'=>'foto/sports-05-senam.jpg','price'=>129000,'sale'=>99000,'interests'=>['kehidupan-sehari-hari'],'description'=>'Melatih koordinasi gerak, kebiasaan aktif, disiplin, dan sportivitas.','outcomes'=>'Anak mampu mengikuti rangkaian gerak sederhana dan menunjukkan sikap sportif.','requirements'=>'Pakaian olahraga, sepatu yang sesuai, dan botol minum.'],
            ['title'=>'Junior Sports Skills','category'=>'sports','level'=>'Intermediate','skill'=>'problem-solving','instructor'=>'raka@skillpath.test','min'=>8,'max'=>14,'duration'=>240,'icon'=>'●','thumbnail'=>'foto/junior-sports-skills.jpg','price'=>199000,'sale'=>159000,'interests'=>['kehidupan-sehari-hari'],'description'=>'Latihan koordinasi, agility, strategi sederhana, teamwork, dan pengambilan keputusan saat bermain.','outcomes'=>'Anak meningkatkan kontrol gerak dan mampu menerapkan strategi sederhana dalam permainan.','requirements'=>'Pakaian olahraga, sepatu, dan kondisi fisik yang sehat untuk aktivitas.'],
            ['title'=>'Youth Performance Camp','category'=>'sports','level'=>'Expert','skill'=>'kemandirian','instructor'=>'raka@skillpath.test','min'=>10,'max'=>14,'duration'=>360,'icon'=>'●','thumbnail'=>'foto/youth-camp.jpg','price'=>329000,'sale'=>279000,'interests'=>['kehidupan-sehari-hari'],'description'=>'Program lanjutan untuk ketahanan, koordinasi, strategi, disiplin latihan, dan evaluasi performa.','outcomes'=>'Anak mampu mengikuti program latihan terstruktur dan mengevaluasi perkembangan performanya.','requirements'=>'Sudah terbiasa mengikuti latihan olahraga dasar.'],

            // TECHNOLOGY
            ['title'=>'Penjelajah Coding Visual','category'=>'technology','level'=>'Beginner','skill'=>'literasi-digital','instructor'=>'arif@skillpath.test','min'=>8,'max'=>14,'duration'=>240,'icon'=>'⌨','thumbnail'=>'foto/tech-04.jpg','price'=>249000,'sale'=>189000,'interests'=>['teknologi'],'description'=>'Belajar logika urutan, event, loop, dan project coding visual tanpa sintaks rumit.','outcomes'=>'Anak memahami algoritma sederhana, pola, loop, dan dapat membuat project interaktif dasar.','requirements'=>'Laptop atau komputer dengan browser modern.'],
            ['title'=>'Eksperimen Sains Mini','category'=>'technology','level'=>'Intermediate','skill'=>'problem-solving','instructor'=>'arif@skillpath.test','min'=>7,'max'=>12,'duration'=>160,'icon'=>'⚗','thumbnail'=>'foto/eksperimen-sains-mini.jpg','price'=>149000,'sale'=>null,'interests'=>['sains','teknologi'],'description'=>'Melatih observasi, prediksi, pencatatan data, dan kesimpulan melalui eksperimen sederhana.','outcomes'=>'Anak dapat membuat prediksi, mencatat hasil, dan menjelaskan kesimpulan sederhana.','requirements'=>'Pendampingan orang dewasa untuk kegiatan eksperimen tertentu.'],
            ['title'=>'Junior Game Creator','category'=>'technology','level'=>'Expert','skill'=>'literasi-digital','instructor'=>'arif@skillpath.test','min'=>10,'max'=>14,'duration'=>420,'icon'=>'⌨','thumbnail'=>'foto/tech-08.jpg','price'=>389000,'sale'=>329000,'interests'=>['teknologi','seni'],'description'=>'Membuat game interaktif dengan perencanaan mekanik, level, visual, testing, dan presentasi proyek.','outcomes'=>'Anak menghasilkan satu game sederhana dan mampu menjelaskan logika serta proses pengembangannya.','requirements'=>'Sudah memahami dasar coding visual dan membawa laptop.'],
        ];

        foreach($paths as $d){
            $path=LearningPath::updateOrCreate(['slug'=>Str::slug($d['title'])],[
                'skill_id'=>$skills[$d['skill']]->id,'instructor_id'=>$instructors[$d['instructor']]->id,'title'=>$d['title'],'slug'=>Str::slug($d['title']),
                'description'=>$d['description'],'price'=>$d['price'],'sale_price'=>$d['sale'],'is_free'=>$d['price']==0,'course_type'=>'offline',
                'min_age'=>$d['min'],'max_age'=>$d['max'],'level'=>$d['level'],'duration_minutes'=>$d['duration'],'icon'=>$d['icon'],
                'certificate_enabled'=>true,'live_class_enabled'=>true,'access_days'=>null,'learning_outcomes'=>$d['outcomes'],'requirements'=>$d['requirements'],
                'is_published'=>true,'published_at'=>now()->subDays(rand(1,30)),
            ]+(isset($d['thumbnail'])?['thumbnail_url'=>$d['thumbnail']]:[]));
            $path->categories()->sync([$categories[$d['category']]->id]);
            $path->interests()->sync(collect($d['interests'])->map(fn($slug)=>$interests[$slug]->id)->all());

            $moduleDefs=[
                ['title'=>'Mulai dan Kenali Dasar','summary'=>'Mengenal konsep utama melalui contoh sederhana.','acts'=>[['Kenali konsep utama','reflection',10],['Coba tantangan pertama','project',20]]],
                ['title'=>'Praktik Terarah','summary'=>'Menerapkan konsep melalui aktivitas yang lebih menantang.','acts'=>[['Latihan bertahap','quiz',15],['Project mini','project',25]]],
                ['title'=>'Project dan Refleksi','summary'=>'Membuat karya atau performa akhir lalu melakukan refleksi.','acts'=>[['Project akhir','project',30],['Refleksi belajar','reflection',15]]],
            ];
            foreach($moduleDefs as $mi=>$md){
                $slug=Str::slug($d['title'].'-'.$md['title']);
                $m=Module::updateOrCreate(['slug'=>$slug],[
                    'learning_path_id'=>$path->id,'title'=>$md['title'],'slug'=>$slug,'summary'=>$md['summary'],'order_index'=>$mi+1,'estimated_minutes'=>max(15,intdiv($d['duration'],3)),
                ]);
                foreach($md['acts'] as $ai=>$a){
                    Activity::updateOrCreate(['module_id'=>$m->id,'title'=>$a[0]],[
                        'type'=>$a[1],'instructions'=>'Ikuti instruksi pengajar pada materi, selesaikan tugas dengan aman, lalu tandai aktivitas sebagai selesai.','points'=>$a[2],'order_index'=>$ai+1,
                    ]);
                }
            }

            $examQuestions = [
                ['question'=>'Apa hasil belajar utama yang diharapkan dari course '.$path->title.'?','options'=>[$d['outcomes'],'Menghafal seluruh halaman tanpa praktik','Menyelesaikan course secepat mungkin tanpa memahami','Menghindari semua aktivitas yang menantang'],'correct'=>0],
                ['question'=>'Saat menemui bagian yang sulit, langkah belajar yang paling tepat adalah...','options'=>['Mencoba kembali secara bertahap dan meminta bantuan bila diperlukan','Melewati semua latihan','Menyalin jawaban tanpa memahami','Berhenti belajar selamanya'],'correct'=>0],
                ['question'=>'Mengapa latihan dan project penting dalam course ini?','options'=>['Agar keterampilan dapat diterapkan, bukan hanya diketahui','Agar halaman terlihat lebih panjang','Agar tidak perlu memahami konsep','Agar nilai selalu otomatis sempurna'],'correct'=>0],
                ['question'=>'Apa cara yang baik untuk menilai perkembangan diri setelah belajar?','options'=>['Membandingkan kemampuan sekarang dengan kemampuan sebelum berlatih','Hanya membandingkan diri dengan teman','Mengabaikan semua umpan balik','Mengulang tanpa refleksi'],'correct'=>0],
                ['question'=>'Jika aktivitas belum berhasil pada percobaan pertama, sikap yang paling tepat adalah...','options'=>['Mengevaluasi kesalahan, mencoba strategi lain, lalu berlatih lagi','Menganggap diri tidak mampu','Menghapus semua progres','Memilih jawaban secara acak terus-menerus'],'correct'=>0],
            ];
            FinalExam::updateOrCreate(
                ['learning_path_id'=>$path->id],
                ['title'=>'Ujian Akhir - '.$path->title,'passing_score'=>75,'max_attempts'=>3,'questions'=>$examQuestions,'is_active'=>true]
            );

            if($path->live_class_enabled){
                for($n=1;$n<=2;$n++){
                    LiveSession::updateOrCreate(
                        ['learning_path_id'=>$path->id,'title'=>'Kelas Tatap Muka '.$n.' - '.$path->title],
                        ['instructor_id'=>$path->instructor_id,'description'=>'Sesi tatap muka bersama pengajar untuk praktik, tanya jawab, dan umpan balik.','starts_at'=>now()->addDays(3+$n*4)->setTime(16,0),'ends_at'=>now()->addDays(3+$n*4)->setTime(17,0),'location'=>'SKILLPATH Learning Hub, pusat kota','meeting_url'=>'https://maps.google.com/','capacity'=>20,'status'=>'scheduled']
                    );
                }
            }
        }

        // Katalog lengkap 6 kategori x 3 level sesuai daftar course terbaru.
        $this->call(CourseCatalogSeeder::class);

        $parent=User::updateOrCreate(
            ['email'=>'parent@skillpath.test'],
            ['name'=>'Orang Tua Demo','password'=>Hash::make('password'),'role'=>'parent']
        );
        $child=ChildProfile::updateOrCreate(
            ['user_id'=>$parent->id],
            ['name'=>'Alya','age'=>10,'avatar'=>'spark','favorite_interest_id'=>$interests['teknologi']->id,'learning_need'=>'digital_literacy','child_voice'=>'Aku suka membuat sesuatu dengan komputer dan ingin bisa membuat project sendiri.','co_design_completed_at'=>now()]
        );
        $child->interests()->sync([
            $interests['teknologi']->id,
            $interests['seni']->id,
            $interests['komunikasi']->id,
        ]);

        $codingPath = LearningPath::where('slug', 'penjelajah-coding-visual')
            ->with('modules.activities')
            ->first();

        $artsPath = LearningPath::where('slug', 'studio-cerita-kreatif')
            ->with('modules.activities')
            ->first();

        if ($codingPath) {
            Enrollment::updateOrCreate(
                ['child_profile_id'=>$child->id,'learning_path_id'=>$codingPath->id],
                ['status'=>'active','enrolled_at'=>now()->subDays(18)]
            );

            foreach ($codingPath->modules->flatMap->activities->take(4)->values() as $index => $activity) {
                Progress::updateOrCreate(
                    ['child_profile_id'=>$child->id,'activity_id'=>$activity->id],
                    [
                        'status'=>'completed',
                        'score'=>82 + ($index * 3),
                        'points_awarded'=>$activity->points,
                        'completed_at'=>now()->subDays(8 - ($index * 2)),
                    ]
                );
            }
        }

        if ($artsPath) {
            Enrollment::updateOrCreate(
                ['child_profile_id'=>$child->id,'learning_path_id'=>$artsPath->id],
                ['status'=>'active','enrolled_at'=>now()->subDays(9)]
            );

            foreach ($artsPath->modules->flatMap->activities->take(2)->values() as $index => $activity) {
                Progress::updateOrCreate(
                    ['child_profile_id'=>$child->id,'activity_id'=>$activity->id],
                    [
                        'status'=>'completed',
                        'score'=>88 + ($index * 2),
                        'points_awarded'=>$activity->points,
                        'completed_at'=>now()->subDays(3 - $index),
                    ]
                );
            }
        }

        $inactiveParent=User::updateOrCreate(
            ['email'=>'parent.bima@skillpath.test'],
            ['name'=>'Orang Tua Bima','password'=>Hash::make('password'),'role'=>'parent']
        );
        $bima=ChildProfile::updateOrCreate(
            ['user_id'=>$inactiveParent->id],
            ['name'=>'Bima','age'=>9,'avatar'=>'spark','favorite_interest_id'=>$interests['teknologi']->id,'learning_need'=>'problem_solving','child_voice'=>'Aku suka tantangan yang membuatku mencari cara sampai berhasil.','co_design_completed_at'=>now()]
        );
        $bima->interests()->sync([$interests['teknologi']->id]);

        if ($codingPath) {
            Enrollment::updateOrCreate(
                ['child_profile_id'=>$bima->id,'learning_path_id'=>$codingPath->id],
                ['status'=>'active','enrolled_at'=>now()->subDays(20)]
            );
        }

        $completeParent=User::updateOrCreate(
            ['email'=>'parent.citra@skillpath.test'],
            ['name'=>'Orang Tua Citra','password'=>Hash::make('password'),'role'=>'parent']
        );
        $citra=ChildProfile::updateOrCreate(
            ['user_id'=>$completeParent->id],
            ['name'=>'Citra','age'=>8,'avatar'=>'spark','favorite_interest_id'=>$interests['seni']->id,'learning_need'=>'creativity','child_voice'=>'Aku suka menggambar dan membuat cerita dari ideku sendiri.','co_design_completed_at'=>now()]
        );
        $citra->interests()->sync([$interests['seni']->id]);

        if ($artsPath) {
            Enrollment::updateOrCreate(
                ['child_profile_id'=>$citra->id,'learning_path_id'=>$artsPath->id],
                ['status'=>'active','enrolled_at'=>now()->subDays(25)]
            );

            foreach ($artsPath->modules->flatMap->activities->values() as $index => $activity) {
                Progress::updateOrCreate(
                    ['child_profile_id'=>$citra->id,'activity_id'=>$activity->id],
                    [
                        'status'=>'completed',
                        'score'=>90,
                        'points_awarded'=>$activity->points,
                        'completed_at'=>now()->subDays(max(1, 7 - $index)),
                    ]
                );
            }


            $artsExam = FinalExam::where('learning_path_id', $artsPath->id)->first();
            if ($artsExam) {
                ExamAttempt::updateOrCreate(
                    ['final_exam_id'=>$artsExam->id,'child_profile_id'=>$citra->id,'attempt_number'=>1],
                    ['score'=>90,'passed'=>true,'answers'=>[0,0,0,0,0],'completed_at'=>now()->subDay()]
                );
            }
        }


        // Data demo untuk fitur Manajemen Sertifikat Admin.
        if ($artsPath) {
            Certificate::updateOrCreate(
                [
                    'child_profile_id' => $citra->id,
                    'learning_path_id' => $artsPath->id,
                ],
                [
                    'certificate_number' => 'CERT-SP-DEMO-CITRA',
                    'final_score' => 90,
                    'issued_at' => now()->subDay(),
                    'status' => 'active',
                    'issued_by' => $admin->id,
                    'revoked_at' => null,
                    'revoked_reason' => null,
                ]
            );
        }


        // Data demo untuk fitur Jadwal Pengajaran Admin.
        if ($codingPath) {
            LiveSession::updateOrCreate(
                ['learning_path_id'=>$codingPath->id,'title'=>'Sesi Review Coding - Selesai'],
                [
                    'instructor_id'=>$codingPath->instructor_id,
                    'description'=>'Sesi demo historis untuk monitoring jadwal admin.',
                    'starts_at'=>now()->subDays(5)->setTime(16,0),
                    'ends_at'=>now()->subDays(5)->setTime(17,0),
                    'location'=>'SKILLPATH Learning Hub, Ruang Teknologi',
                    'meeting_url'=>'https://maps.google.com/',
                    'capacity'=>20,
                    'status'=>'completed',
                ]
            );
        }

        if ($artsPath) {
            LiveSession::updateOrCreate(
                ['learning_path_id'=>$artsPath->id,'title'=>'Studio Kreatif Hari Ini'],
                [
                    'instructor_id'=>$artsPath->instructor_id,
                    'description'=>'Sesi demo hari ini untuk monitoring jadwal admin.',
                    'starts_at'=>now()->setTime(14,0),
                    'ends_at'=>now()->setTime(15,0),
                    'location'=>'SKILLPATH Creative Studio, Ruang Seni',
                    'meeting_url'=>'https://maps.google.com/',
                    'capacity'=>18,
                    'status'=>'scheduled',
                ]
            );
        }

        // Data transaksi PAID demo untuk menampilkan Laporan Pendapatan Admin.
        $createPaidDemoOrder = function (string $number, User $buyer, LearningPath $course, int $daysAgo, string $method) {
            $normalPrice = (float) $course->price;
            $finalPrice = $course->effectivePrice();
            $discount = max(0, $normalPrice - $finalPrice);
            $paidAt = now()->subDays($daysAgo)->setTime(10 + ($daysAgo % 5), 15);

            $order = Order::updateOrCreate(
                ['order_number'=>$number],
                [
                    'user_id'=>$buyer->id,
                    'subtotal'=>$finalPrice,
                    'discount'=>0,
                    'total'=>$finalPrice,
                    'payment_method'=>$method,
                    'payment_status'=>'paid',
                    'status'=>'completed',
                    'paid_at'=>$paidAt,
                    'created_at'=>$paidAt->copy()->subMinutes(10),
                    'updated_at'=>$paidAt,
                ]
            );

            $order->items()->updateOrCreate(
                ['learning_path_id'=>$course->id],
                [
                    'title_snapshot'=>$course->title,
                    'price'=>$normalPrice,
                    'discount'=>$discount,
                    'final_price'=>$finalPrice,
                ]
            );
        };

        if ($codingPath) {
            $createPaidDemoOrder('SP-DEMO-001', $parent, $codingPath, 3, 'qris');
            $createPaidDemoOrder('SP-DEMO-002', $inactiveParent, $codingPath, 12, 'virtual_account');
        }

        if ($artsPath) {
            $createPaidDemoOrder('SP-DEMO-003', $parent, $artsPath, 6, 'ewallet');
            $createPaidDemoOrder('SP-DEMO-004', $completeParent, $artsPath, 25, 'bank_transfer');
        }

        $englishPath = LearningPath::where('slug', 'english-fun-conversation')->first();
        if ($englishPath) {
            $createPaidDemoOrder('SP-DEMO-005', $parent, $englishPath, 35, 'qris');
        }
    }
}
