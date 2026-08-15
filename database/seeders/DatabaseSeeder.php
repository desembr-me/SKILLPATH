<?php
namespace Database\Seeders;

use App\Models\{ActivityCompletion,Attendance,Category,Certificate,Child,CoDesignSession,Course,CourseModule,CourseSchedule,CourseSession,Enrollment,Exam,ExamAttempt,LearningPath,Review,SessionCredit,Transaction,User,Wishlist};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $parent = User::create(['name'=>'Nadia Putri','email'=>'parent@skillpath.test','phone'=>'081234567890','password'=>Hash::make('password'),'role'=>'parent']);
        User::create(['name'=>'Admin SkillPath','email'=>'admin@skillpath.test','password'=>Hash::make('password'),'role'=>'admin']);

        $categories = collect([
            ['Arts','arts','Kreativitas visual, craft, drawing, dan ekspresi artistik.','#FFE6D8'],
            ['Music','music','Piano, ritme, vokal, dan eksplorasi musikal.','#FFF2B9'],
            ['Languages','languages','Komunikasi, speaking, storytelling, dan bahasa asing.','#DDF2FF'],
            ['Sports','sports','Gerak aktif, koordinasi, kebugaran, dan sportivitas.','#DFF4E5'],
            ['Self Improvement','self-improvement','Percaya diri, kemampuan sosial, emosi, dan komunikasi.','#FFE3EC'],
            ['Technology','technology','Coding, robotik, logika, dan kreasi digital.','#EAE5FF'],
        ])->mapWithKeys(function($x){$c=Category::create(['name'=>$x[0],'slug'=>$x[1],'description'=>$x[2],'accent'=>$x[3]]);return [$x[0]=>$c];});

        // Setiap mentor hanya mengajar 1 kategori.
        $mentor = User::create(['name'=>'Naya Rahma','email'=>'mentor@skillpath.test','phone'=>'081298765432','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Music']->id,'avatar'=>'avatars/mentors/naya.png','headline'=>'Mentor Piano & Musik Anak','bio'=>'Mengajar piano untuk anak sejak 7 tahun, fokus pada fondasi ritme dan kepercayaan diri tampil.']);
        $mentor2 = User::create(['name'=>'Dimas Pratama','email'=>'dimas@skillpath.test','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Technology']->id,'avatar'=>'avatars/mentors/dimas.png','headline'=>'Mentor Robotika & Coding Anak','bio'=>'Membimbing anak merakit robot dan belajar logika pemrograman lewat proyek langsung.']);
        $mentor3 = User::create(['name'=>'Sari Wulandari','email'=>'sari@skillpath.test','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Arts']->id,'avatar'=>'avatars/mentors/sari.png','headline'=>'Mentor Seni & Ilustrasi Anak','bio'=>'Mendampingi anak bereksplorasi warna, bentuk, dan bercerita lewat karya visual.']);
        $mentor4 = User::create(['name'=>'Bimo Aditya','email'=>'bimo@skillpath.test','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Self Improvement']->id,'avatar'=>'avatars/mentors/bimo.png','headline'=>'Mentor Pengembangan Diri Anak','bio'=>'Fokus membangun kepercayaan diri, kemampuan sosial, dan pengelolaan emosi anak lewat aktivitas kelompok.']);
        $mentor5 = User::create(['name'=>'Clara Simanjuntak','email'=>'clara@skillpath.test','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Languages']->id,'avatar'=>'avatars/mentors/clara.png','headline'=>'Mentor Bahasa Inggris Anak','bio'=>'Mengajak anak berani berbahasa Inggris lewat storytelling dan permainan kelompok.']);
        $mentor6 = User::create(['name'=>'Fajar Nugroho','email'=>'fajar@skillpath.test','password'=>Hash::make('password'),'role'=>'mentor','category_id'=>$categories['Sports']->id,'avatar'=>'avatars/mentors/fajar.png','headline'=>'Mentor Olahraga Anak','bio'=>'Melatih teknik dasar olahraga dengan pendekatan yang menyenangkan dan aman untuk anak.']);

        $courseData = [
            ['Technology','Junior Robotics Lab','junior-robotics-lab','Build, test, improve.','Anak merakit robot sederhana, mengenal sensor, logika, dan problem solving melalui proyek langsung.',8,10,850000,8,90,'SkillHub Kemang','Jakarta Selatan','🤖','#EAE5FF',$mentor2,true],
            ['Arts','Little Canvas Club','little-canvas-club','Create what you imagine.','Eksplorasi warna, bentuk, tekstur, dan craft untuk melatih kreativitas dan motorik halus.',5,7,620000,6,75,'Studio Warna Tebet','Jakarta Selatan','🎨','#FFE6D8',$mentor3,false],
            ['Self Improvement','Confident Kids Club','confident-kids-club','Speak, connect, grow.','Roleplay, permainan kelompok, dan aktivitas refleksi untuk membangun percaya diri serta kemampuan sosial.',8,10,690000,6,90,'GrowSpace Bintaro','Tangerang Selatan','🌱','#FFE3EC',$mentor4,true],
            ['Music','Piano Starter','piano-starter','Play your first song.','Belajar fondasi piano, ritme, koordinasi tangan, dan lagu sederhana secara bertahap.',8,10,780000,8,60,'Nada Studio Cipete','Jakarta Selatan','🎹','#FFF2B9',$mentor,false],
            ['Languages','English Adventure','english-adventure','Speak through stories.','Speaking, storytelling, games, dan aktivitas kelompok untuk membangun keberanian berbahasa Inggris.',11,14,720000,8,90,'BrightTalk Depok','Depok','💬','#DDF2FF',$mentor5,false],
            ['Sports','Junior Badminton','junior-badminton','Move with confidence.','Teknik dasar badminton, koordinasi, kebugaran, kerja sama, dan sportivitas.',11,14,760000,8,90,'Arena Cilandak','Jakarta Selatan','🏸','#DFF4E5',$mentor6,false],
            ['Technology','Mini Coding Explorer','mini-coding-explorer','Logic before screens.','Pola, urutan, algoritma sederhana, dan logika komputasional melalui permainan serta aktivitas praktik.',5,7,650000,6,75,'Makerspace Bekasi','Bekasi','🧩','#EAE5FF',$mentor2,true],
            ['Self Improvement','Social Skills Playgroup','social-skills-playgroup','Practice friendship skills.','Kegiatan kelompok untuk melatih bergiliran, mendengar, menyampaikan kebutuhan, dan kerja sama.',5,7,680000,6,75,'KindSpace Pondok Indah','Jakarta Selatan','🤝','#FFE3EC',$mentor4,false],
            ['Arts','Young Illustrator','young-illustrator','Turn ideas into visual stories.','Ilustrasi karakter, komposisi, warna, dan proyek akhir berupa karya personal.',11,14,740000,8,90,'Creative Lab Rawamangun','Jakarta Timur','✏️','#FFE6D8',$mentor3,false],
        ];

        $coverImages = [
            'junior-robotics-lab'=>'tech-06.jpg',
            'little-canvas-club'=>'arts-01.jpg',
            'confident-kids-club'=>'Confidence Building.jpg',
            'piano-starter'=>'music-Piano.jpg',
            'english-adventure'=>'english convo.jpg',
            'junior-badminton'=>'Junior Sports Skills.jpg',
            'mini-coding-explorer'=>'tech-08.jpg',
            'social-skills-playgroup'=>'Effective Communication.jpg',
            'young-illustrator'=>'Young Artist Portfolio.jpg',
        ];

        $courses=[];
        foreach($courseData as $i=>$x){
            $course=Course::create(['category_id'=>$categories[$x[0]]->id,'instructor_id'=>$x[14]->id,'title'=>$x[1],'slug'=>$x[2],'subtitle'=>$x[3],'description'=>$x[4],'age_min'=>$x[5],'age_max'=>$x[6],'price'=>$x[7],'sessions_count'=>$x[8],'duration_minutes'=>$x[9],'location_name'=>$x[10],'city'=>$x[11],'cover_emoji'=>$x[12],'cover_image'=>$coverImages[$x[2]]??null,'accent'=>$x[13],'is_featured'=>$x[15],'status'=>'active']);
            foreach([[6,'10:00:00','11:30:00'],[6,'13:00:00','14:30:00'],[0,'10:00:00','11:30:00']] as $j=>$s){
                CourseSchedule::create(['course_id'=>$course->id,'instructor_id'=>$course->instructor_id,'day_of_week'=>$s[0],'start_time'=>$s[1],'end_time'=>$s[2],'start_date'=>now()->addDays(7+$j)->toDateString(),'end_date'=>now()->addMonths(3)->toDateString(),'capacity'=>10,'room'=>'Room '.($j+1),'status'=>'open']);
            }
            Exam::create(['course_id'=>$course->id,'title'=>'Final Challenge: '.$course->title,'passing_score'=>75,'max_attempts'=>2,'is_active'=>true]);
            foreach([['Pengenalan','Mengenal dasar dan tujuan course.'],['Latihan Inti','Praktik langsung bersama mentor.'],['Proyek Akhir','Menerapkan keterampilan pada proyek nyata.']] as $mi=>$m){
                $module=$course->modules()->create(['title'=>'Modul '.($mi+1).': '.$m[0],'description'=>$m[1],'sequence'=>$mi+1]);
                foreach([['Materi: '.$m[0],'materi'],['Latihan: '.$m[0],'latihan'],['Refleksi: '.$m[0],'refleksi']] as $ai=>$a){
                    $module->activities()->create(['title'=>$a[0],'type'=>$a[1],'sequence'=>$ai+1]);
                }
            }
            $courses[$x[1]]=$course;
        }

        $alya=Child::create(['parent_id'=>$parent->id,'name'=>'Alya Putri','nickname'=>'Alya','birth_date'=>now()->subYears(9)->subMonths(2)->toDateString(),'avatar'=>'🧒🏻','interests'=>['Technology','Arts','Self Improvement'],'learning_preferences'=>['hands_on','group','step_by_step']]);
        Child::create(['parent_id'=>$parent->id,'name'=>'Raka Putra','nickname'=>'Raka','birth_date'=>now()->subYears(6)->subMonths(1)->toDateString(),'avatar'=>'👦🏻','interests'=>['Arts','Sports'],'learning_preferences'=>['play','hands_on']]);
        CoDesignSession::create(['child_id'=>$alya->id,'parent_id'=>$parent->id,'child_choices'=>['Technology','Arts','Self Improvement'],'parent_observations'=>['child_voice'=>'Aku suka membuat robot dan suka cerita saat bikin karya seni.','discussed_with_child'=>true],'agreed_interests'=>['Technology','Arts','Self Improvement'],'learning_preferences'=>['hands_on','group','step_by_step'],'completed_at'=>now()]);
        Wishlist::create(['parent_id'=>$parent->id,'course_id'=>$courses['English Adventure']->id]);
        Wishlist::create(['parent_id'=>$parent->id,'course_id'=>$courses['Junior Badminton']->id]);

        $robot=$courses['Junior Robotics Lab']; $piano=$courses['Piano Starter'];
        $robotSchedule=$robot->schedules()->first(); $pianoSchedule=$piano->schedules()->first();
        $enroll=Enrollment::create(['parent_id'=>$parent->id,'child_id'=>$alya->id,'course_id'=>$piano->id,'schedule_id'=>$pianoSchedule->id,'status'=>'active','progress'=>0,'enrolled_at'=>now()->subMonth()]);
        Transaction::create(['parent_id'=>$parent->id,'enrollment_id'=>$enroll->id,'invoice_code'=>'SP-DEMO-001','subtotal'=>$piano->price,'platform_fee'=>15000,'total'=>$piano->price+15000,'payment_method'=>'virtual_account','status'=>'paid','paid_at'=>now()->subMonth()]);
        for($i=1;$i<=2;$i++) CourseSession::create(['course_id'=>$piano->id,'schedule_id'=>$pianoSchedule->id,'session_no'=>$i,'session_date'=>now()->addDays($i*7)->toDateString(),'start_time'=>$pianoSchedule->start_time,'end_time'=>$pianoSchedule->end_time,'topic'=>'Piano Foundation '.$i]);
        $s1=CourseSession::where('schedule_id',$pianoSchedule->id)->first();
        $att=Attendance::create(['enrollment_id'=>$enroll->id,'course_session_id'=>$s1->id,'status'=>'excused','absence_reason'=>'Sakit','credit_eligible'=>true,'mentor_note'=>'Dapat menggunakan sesi pengganti.']);
        SessionCredit::create(['child_id'=>$alya->id,'enrollment_id'=>$enroll->id,'source_attendance_id'=>$att->id,'credit_code'=>'CR-DEMO01','reason'=>'Sakit','status'=>'available','expires_at'=>now()->addDays(30)]);
        $exam=$piano->exams()->first();
        ExamAttempt::create(['exam_id'=>$exam->id,'enrollment_id'=>$enroll->id,'attempt_no'=>1,'score'=>68,'status'=>'failed','mentor_feedback'=>'Perlu menguatkan tempo dan koordinasi.','taken_at'=>now()->subDays(3)]);

        $pianoActivities=$piano->modules()->with('activities')->get()->flatMap->activities;
        foreach($pianoActivities->take(4) as $activity)ActivityCompletion::create(['enrollment_id'=>$enroll->id,'module_activity_id'=>$activity->id,'completed_at'=>now()->subDays(rand(1,25))]);
        $enroll->update(['progress'=>(int)round(4/$pianoActivities->count()*100)]);

        $robotEnroll=Enrollment::create(['parent_id'=>$parent->id,'child_id'=>$alya->id,'course_id'=>$robot->id,'schedule_id'=>$robotSchedule->id,'status'=>'completed','progress'=>100,'enrolled_at'=>now()->subMonths(2),'completed_at'=>now()->subDays(5),'final_status'=>'passed']);
        Transaction::create(['parent_id'=>$parent->id,'enrollment_id'=>$robotEnroll->id,'invoice_code'=>'SP-DEMO-002','subtotal'=>$robot->price,'platform_fee'=>15000,'total'=>$robot->price+15000,'payment_method'=>'virtual_account','status'=>'paid','paid_at'=>now()->subMonths(2)]);
        $robotActivities=$robot->modules()->with('activities')->get()->flatMap->activities;
        foreach($robotActivities as $activity)ActivityCompletion::create(['enrollment_id'=>$robotEnroll->id,'module_activity_id'=>$activity->id,'completed_at'=>now()->subDays(rand(6,50))]);
        $robotExam=$robot->exams()->first();
        $robotAttempt=ExamAttempt::create(['exam_id'=>$robotExam->id,'enrollment_id'=>$robotEnroll->id,'attempt_no'=>1,'score'=>88,'status'=>'passed','mentor_feedback'=>'Konsisten dan detail dalam merakit sensor.','taken_at'=>now()->subDays(5)]);
        Certificate::create(['enrollment_id'=>$robotEnroll->id,'exam_attempt_id'=>$robotAttempt->id,'certificate_no'=>'CERT-SP-'.now()->format('Ym').'-DEMO0001','issued_at'=>now()->subDays(5)]);
        Review::create(['parent_id'=>$parent->id,'enrollment_id'=>$robotEnroll->id,'course_id'=>$robot->id,'instructor_id'=>$robot->instructor_id,'mentor_rating'=>5,'mentor_review'=>'Anak jadi lebih percaya diri membongkar dan merakit ulang.','platform_rating'=>5,'platform_review'=>'Jadwal dan sertifikat langsung muncul di dashboard.']);

        $path=LearningPath::create(['child_id'=>$alya->id,'title'=>'Creative Explorer Path','rationale'=>'Gabungan Technology, Arts, dan Self Improvement dari hasil co-design.','status'=>'active','generated_at'=>now()]);
        foreach([$courses['Confident Kids Club'],$courses['Mini Coding Explorer'],$courses['Young Illustrator']] as $i=>$c)$path->items()->create(['course_id'=>$c->id,'sequence'=>$i+1,'reason'=>'Sesuai minat dan usia Alya','status'=>$i===0?'recommended':'locked','match_score'=>95-$i*8]);
    }
}
