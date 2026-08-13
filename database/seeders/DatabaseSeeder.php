<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\InstructorProfile;
use App\Models\Interest;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\Order;
use App\Models\SessionBooking;
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
            ['name'=>'Teknologi','icon'=>'⌨','description'=>'Coding kreatif, perangkat digital, dan kreasi teknologi.'],
            ['name'=>'Seni','icon'=>'✎','description'=>'Menggambar, bercerita, desain, dan ekspresi kreatif.'],
            ['name'=>'Sains','icon'=>'⚗','description'=>'Eksperimen, observasi, dan rasa ingin tahu.'],
            ['name'=>'Komunikasi','icon'=>'☏','description'=>'Berbicara, mendengar, dan menyampaikan ide.'],
            ['name'=>'Kewirausahaan','icon'=>'◎','description'=>'Ide usaha, nilai, perencanaan, dan tanggung jawab.'],
            ['name'=>'Kehidupan Sehari-hari','icon'=>'⌂','description'=>'Kemandirian, kebiasaan baik, dan keterampilan praktis.'],
        ])->mapWithKeys(function($item){ $m=Interest::updateOrCreate(['slug'=>Str::slug($item['name'])],$item+['slug'=>Str::slug($item['name'])]); return[$m->slug=>$m]; });

        $skills = collect([
            ['name'=>'Kreativitas','icon'=>'✦','description'=>'Mengembangkan ide dan membuat karya nyata.'],
            ['name'=>'Literasi Digital','icon'=>'▣','description'=>'Menggunakan teknologi secara produktif dan bertanggung jawab.'],
            ['name'=>'Komunikasi','icon'=>'●','description'=>'Menyampaikan gagasan secara jelas dan percaya diri.'],
            ['name'=>'Problem Solving','icon'=>'◇','description'=>'Menganalisis masalah dan mencoba solusi.'],
            ['name'=>'Kemandirian','icon'=>'✓','description'=>'Mengatur tugas, kebiasaan, dan tanggung jawab sehari-hari.'],
        ])->mapWithKeys(function($item){ $m=Skill::updateOrCreate(['slug'=>Str::slug($item['name'])],$item+['slug'=>Str::slug($item['name'])]); return[$m->slug=>$m]; });

        $categories=collect([
            ['name'=>'Seni & Kreativitas','slug'=>'arts','icon'=>'✦','description'=>'Menggambar, cerita, desain, kerajinan, dan karya visual secara tatap muka.'],
            ['name'=>'Bahasa & Komunikasi','slug'=>'languages','icon'=>'Aa','description'=>'Percakapan, public speaking, storytelling, dan komunikasi melalui praktik langsung.'],
            ['name'=>'Musik','slug'=>'music','icon'=>'♫','description'=>'Ritme, bunyi, tempo, alat musik dasar, dan ekspresi musikal.'],
            ['name'=>'Olahraga','slug'=>'sports','icon'=>'●','description'=>'Koordinasi gerak, kebugaran, disiplin, kerja sama, dan sportivitas.'],
            ['name'=>'Teknologi Kreatif','slug'=>'technology','icon'=>'</>','description'=>'Coding kreatif, robotika sederhana, dan eksplorasi teknologi di ruang kelas.'],
        ])->mapWithKeys(function($item){ $m=Category::updateOrCreate(['slug'=>$item['slug']],$item); return[$m->slug=>$m]; });

        $admin=User::updateOrCreate(['email'=>'admin@skillpath.test'],['name'=>'Admin SKILLPATH','password'=>Hash::make('password'),'role'=>'admin']);

        $instructorData=[
            ['name'=>'Naila Prameswari','email'=>'naila@skillpath.test','headline'=>'Fasilitator seni visual dan creative storytelling anak','expertise'=>'Drawing, storytelling, visual creativity','years'=>6,'education'=>'S1 Pendidikan Seni','rating'=>4.9],
            ['name'=>'Arif Nugraha','email'=>'arif@skillpath.test','headline'=>'Mentor coding kreatif dan computational thinking','expertise'=>'Scratch, creative coding, problem solving','years'=>7,'education'=>'S1 Pendidikan Teknologi Informasi','rating'=>4.8],
            ['name'=>'Maya Lestari','email'=>'maya@skillpath.test','headline'=>'Coach komunikasi, bahasa, dan public speaking anak','expertise'=>'Public speaking, English conversation, communication','years'=>8,'education'=>'S1 Pendidikan Bahasa','rating'=>4.9],
            ['name'=>'Raka Aditya','email'=>'raka@skillpath.test','headline'=>'Coach aktivitas fisik dan sportivitas anak','expertise'=>'Motor skills, physical literacy, sportsmanship','years'=>6,'education'=>'S1 Pendidikan Jasmani','rating'=>4.8],
            ['name'=>'Dinda Maharani','email'=>'dinda@skillpath.test','headline'=>'Fasilitator musik dasar dan eksplorasi ritme','expertise'=>'Rhythm, basic music, creative sound','years'=>5,'education'=>'S1 Pendidikan Musik','rating'=>4.9],
        ];
        $instructors=collect($instructorData)->mapWithKeys(function($d){
            $u=User::updateOrCreate(['email'=>$d['email']],['name'=>$d['name'],'password'=>Hash::make('password'),'role'=>'instructor']);
            InstructorProfile::updateOrCreate(['user_id'=>$u->id],[
                'headline'=>$d['headline'],'bio'=>$d['headline'].'. Kelas berfokus pada praktik langsung, interaksi kelompok kecil, dan umpan balik positif sesuai usia anak.',
                'expertise'=>$d['expertise'],'years_experience'=>$d['years'],'education'=>$d['education'],'is_verified'=>true,'rating'=>$d['rating'],'students_count'=>120,
            ]);
            return[$u->email=>$u];
        });

        $paths=[
            ['title'=>'Studio Cerita Kreatif','category'=>'arts','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>5,'max'=>10,'duration'=>120,'icon'=>'✎','price'=>179000,'sale'=>129000,'type'=>'workshop','venue'=>'Panakkukang, Makassar','place'=>'Ruang Kreatif SKILLPATH Panakkukang','address'=>'Jl. Boulevard, Panakkukang, Makassar','interests'=>['seni','komunikasi'],'description'=>'Workshop tatap muka untuk membuat tokoh, ilustrasi, alur, dan cerita pendek bersama teman sebaya.','outcomes'=>'Anak berlatih menyusun cerita tiga bagian, membuat karakter, dan mempresentasikan karya sederhana.','materials'=>'Kertas gambar, lembar aktivitas, pensil warna dasar, dan perlengkapan craft bersama.','requirements'=>'Gunakan pakaian nyaman. Anak usia 5–7 tahun dianjurkan datang bersama pendamping saat check-in.'],
            ['title'=>'Penjelajah Coding Kreatif','category'=>'technology','skill'=>'literasi-digital','instructor'=>'arif@skillpath.test','min'=>8,'max'=>14,'duration'=>120,'icon'=>'⌨','price'=>249000,'sale'=>189000,'type'=>'regular','venue'=>'Tamalanrea, Makassar','place'=>'Lab Kreatif Tamalanrea','address'=>'Jl. Perintis Kemerdekaan, Tamalanrea, Makassar','interests'=>['teknologi'],'description'=>'Kelas offline coding visual berbasis proyek dengan pendampingan langsung dari mentor.','outcomes'=>'Anak memahami urutan, event, loop, dan dapat membuat proyek interaktif sederhana.','materials'=>'Komputer kelas, koneksi lokal, modul proyek, dan perangkat pendukung tersedia di lokasi.','requirements'=>'Tidak wajib membawa laptop. Bawa botol minum dan hadir 15 menit sebelum kelas.'],
            ['title'=>'Klub Eksperimen Sains','category'=>'technology','skill'=>'problem-solving','instructor'=>'arif@skillpath.test','min'=>7,'max'=>12,'duration'=>100,'icon'=>'⚗','price'=>149000,'sale'=>null,'type'=>'workshop','venue'=>'Panakkukang, Makassar','place'=>'Studio Eksperimen Anak','address'=>'Jl. Pengayoman, Panakkukang, Makassar','interests'=>['sains'],'description'=>'Eksperimen aman secara langsung untuk melatih observasi, prediksi, pencatatan, dan kesimpulan.','outcomes'=>'Anak berlatih membuat prediksi, mengamati perubahan, dan menjelaskan hasil eksperimen.','materials'=>'Bahan eksperimen, alat keselamatan dasar, lembar observasi, dan alat tulis eksperimen.','requirements'=>'Kenakan pakaian yang boleh terkena noda dan sepatu tertutup.'],
            ['title'=>'Berani Bicara','category'=>'languages','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>6,'max'=>14,'duration'=>90,'icon'=>'●','price'=>199000,'sale'=>159000,'type'=>'regular','venue'=>'Rappocini, Makassar','place'=>'Communication Studio Rappocini','address'=>'Jl. A.P. Pettarani, Rappocini, Makassar','interests'=>['komunikasi'],'description'=>'Kelas public speaking tatap muka dengan permainan kelompok, latihan suara, dan presentasi singkat.','outcomes'=>'Anak berlatih menyampaikan ide secara terstruktur, mendengar aktif, dan tampil lebih percaya diri.','materials'=>'Kartu permainan komunikasi, alat presentasi, dan lembar latihan disediakan.','requirements'=>'Datang dengan pakaian nyaman dan siapkan satu cerita pendek yang ingin dibagikan.'],
            ['title'=>'English Fun Conversation','category'=>'languages','skill'=>'komunikasi','instructor'=>'maya@skillpath.test','min'=>7,'max'=>12,'duration'=>90,'icon'=>'Aa','price'=>229000,'sale'=>179000,'type'=>'regular','venue'=>'Panakkukang, Makassar','place'=>'Language Corner Panakkukang','address'=>'Jl. Boulevard, Panakkukang, Makassar','interests'=>['komunikasi'],'description'=>'Kelas percakapan bahasa Inggris tatap muka berbasis permainan, role play, dan aktivitas berpasangan.','outcomes'=>'Anak berlatih memperkenalkan diri, bertanya, merespons, dan berbicara dalam situasi sehari-hari.','materials'=>'Flash card, role-play kit, lembar aktivitas, dan alat tulis kelas.','requirements'=>'Tidak ada syarat kemampuan awal. Datang 10 menit sebelum kelas.'],
            ['title'=>'Ritme dan Musik Dasar','category'=>'music','skill'=>'kreativitas','instructor'=>'dinda@skillpath.test','min'=>5,'max'=>11,'duration'=>90,'icon'=>'♫','price'=>169000,'sale'=>129000,'type'=>'workshop','venue'=>'Ujung Pandang, Makassar','place'=>'Studio Musik Anak Fort Area','address'=>'Jl. Ujung Pandang, Makassar','interests'=>['seni'],'description'=>'Workshop musik tatap muka untuk mengenal ritme, tempo, pola bunyi, dan ekspresi bersama.','outcomes'=>'Anak mengenali tempo, menirukan pola ketukan, dan membuat pola bunyi sederhana.','materials'=>'Instrumen perkusi kelas dan benda bunyi aman tersedia di lokasi.','requirements'=>'Tidak wajib memiliki atau membawa alat musik pribadi.'],
            ['title'=>'Gerak Aktif dan Sportivitas','category'=>'sports','skill'=>'kemandirian','instructor'=>'raka@skillpath.test','min'=>6,'max'=>14,'duration'=>90,'icon'=>'●','price'=>129000,'sale'=>99000,'type'=>'regular','venue'=>'Tamalate, Makassar','place'=>'Lapangan Aktivitas Tamalate','address'=>'Jl. Metro Tanjung Bunga, Tamalate, Makassar','interests'=>['kehidupan-sehari-hari'],'description'=>'Kelas aktivitas fisik kelompok untuk melatih koordinasi, kebiasaan aktif, disiplin, dan sportivitas.','outcomes'=>'Anak berlatih rangkaian gerak, kerja sama tim, dan sikap sportif.','materials'=>'Cone, bola latihan, marker, dan perlengkapan aktivitas kelompok tersedia.','requirements'=>'Gunakan pakaian olahraga, sepatu yang aman, topi, dan bawa botol minum.'],
            ['title'=>'Creative Starter Gratis','category'=>'arts','skill'=>'kreativitas','instructor'=>'naila@skillpath.test','min'=>5,'max'=>14,'duration'=>60,'icon'=>'✦','price'=>0,'sale'=>null,'type'=>'workshop','venue'=>'Panakkukang, Makassar','place'=>'Ruang Kreatif SKILLPATH Panakkukang','address'=>'Jl. Boulevard, Panakkukang, Makassar','interests'=>['seni'],'description'=>'Workshop pengenalan gratis untuk mencoba pengalaman kelas kreatif tatap muka SKILLPATH.','outcomes'=>'Anak menyelesaikan satu karya singkat dan mengenal suasana belajar kelompok di SKILLPATH.','materials'=>'Seluruh bahan proyek mini disediakan.','requirements'=>'Cukup datang 10 menit sebelum kelas dan gunakan pakaian nyaman.'],
        ];

        foreach($paths as $d){
            $path=LearningPath::updateOrCreate(['slug'=>Str::slug($d['title'])],[
                'skill_id'=>$skills[$d['skill']]->id,'instructor_id'=>$instructors[$d['instructor']]->id,'title'=>$d['title'],'slug'=>Str::slug($d['title']),
                'description'=>$d['description'],'price'=>$d['price'],'sale_price'=>$d['sale'],'is_free'=>$d['price']==0,'class_type'=>$d['type'],
                'min_age'=>$d['min'],'max_age'=>$d['max'],'level'=>'Pemula','duration_minutes'=>$d['duration'],'icon'=>$d['icon'],
                'certificate_enabled'=>true,'venue_summary'=>$d['venue'],'materials_included'=>$d['materials'],'learning_outcomes'=>$d['outcomes'],'requirements'=>$d['requirements'],
                'is_published'=>true,'published_at'=>now()->subDays(random_int(1,30)),
            ]);
            $path->categories()->sync([$categories[$d['category']]->id]);
            $path->interests()->sync(collect($d['interests'])->map(fn($slug)=>$interests[$slug]->id)->all());

            $moduleDefs=[
                ['title'=>'Pembukaan & Eksplorasi','summary'=>'Pemanasan, perkenalan alat atau konsep, dan eksplorasi terbimbing.','acts'=>[['Kenali alat dan aturan aman','practice'],['Tantangan pemanasan','game']]],
                ['title'=>'Praktik Utama','summary'=>'Kegiatan inti dilakukan bersama pengajar dan teman sekelas.','acts'=>[['Latihan bertahap','practice'],['Proyek atau permainan utama','project']]],
                ['title'=>'Karya & Refleksi','summary'=>'Menyelesaikan karya/performa lalu berbagi pengalaman.','acts'=>[['Karya atau performa akhir','project'],['Berbagi dan refleksi','reflection']]],
            ];
            foreach($moduleDefs as $mi=>$md){
                $slug=Str::slug($d['title'].'-'.$md['title']);
                $module=Module::updateOrCreate(['slug'=>$slug],[
                    'learning_path_id'=>$path->id,'title'=>$md['title'],'slug'=>$slug,'summary'=>$md['summary'],'order_index'=>$mi+1,'estimated_minutes'=>max(15,intdiv($d['duration'],3)),
                ]);
                foreach($md['acts'] as $ai=>$activity){
                    Activity::updateOrCreate(['module_id'=>$module->id,'title'=>$activity[0]],[
                        'type'=>$activity[1],'instructions'=>'Aktivitas dilakukan secara tatap muka di lokasi dan dipandu langsung oleh pengajar.','points'=>0,'order_index'=>$ai+1,
                    ]);
                }
            }

            if($path->slug==='creative-starter-gratis'){
                ClassSession::updateOrCreate(['learning_path_id'=>$path->id,'title'=>'Creative Starter - Sesi Demo'],[
                    'instructor_id'=>$path->instructor_id,'description'=>'Workshop kreatif singkat untuk mencoba suasana kelas SKILLPATH.','starts_at'=>now()->subDays(7)->setTime(10,0),'ends_at'=>now()->subDays(7)->setTime(11,0),
                    'venue_name'=>$d['place'],'address'=>$d['address'],'room'=>'Studio 1','capacity'=>16,'status'=>'completed','preparation_notes'=>'Datang 10 menit sebelum kelas.',
                ]);
            } else {
                for($n=1;$n<=2;$n++){
                    ClassSession::updateOrCreate(['learning_path_id'=>$path->id,'title'=>'Pertemuan '.$n.' - '.$path->title],[
                        'instructor_id'=>$path->instructor_id,'description'=>'Sesi tatap muka berisi praktik, interaksi kelompok, dan umpan balik langsung dari pengajar.',
                        'starts_at'=>now()->addDays(3+$n*5)->setTime($n===1?15:10,0),'ends_at'=>now()->addDays(3+$n*5)->setTime($n===1?16:11,30),
                        'venue_name'=>$d['place'],'address'=>$d['address'],'room'=>$d['type']==='regular'?'Kelas A':'Studio 1','capacity'=>$d['type']==='private'?4:20,'status'=>'scheduled',
                        'preparation_notes'=>$d['requirements'],
                    ]);
                }
            }
        }

        $parent=User::updateOrCreate(['email'=>'parent@skillpath.test'],['name'=>'Orang Tua Demo','password'=>Hash::make('password'),'role'=>'parent']);
        $child=ChildProfile::updateOrCreate(['user_id'=>$parent->id],['name'=>'Alya','age'=>10,'avatar'=>'spark']);
        $child->interests()->sync([$interests['teknologi']->id,$interests['seni']->id,$interests['komunikasi']->id]);

        $codingPath=LearningPath::where('slug','penjelajah-coding-kreatif')->with('classSessions')->first();
        $artsPath=LearningPath::where('slug','studio-cerita-kreatif')->with('classSessions')->first();
        foreach([$codingPath,$artsPath] as $path){ if(!$path) continue; Enrollment::updateOrCreate(['child_profile_id'=>$child->id,'learning_path_id'=>$path->id],['status'=>'active','enrolled_at'=>now()->subDays(8)]); $session=$path->classSessions->where('status','scheduled')->first(); if($session) SessionBooking::updateOrCreate(['class_session_id'=>$session->id,'child_profile_id'=>$child->id],['status'=>'booked','booked_at'=>now()->subDays(2)]); }

        $inactiveParent=User::updateOrCreate(['email'=>'parent.bima@skillpath.test'],['name'=>'Orang Tua Bima','password'=>Hash::make('password'),'role'=>'parent']);
        $bima=ChildProfile::updateOrCreate(['user_id'=>$inactiveParent->id],['name'=>'Bima','age'=>9,'avatar'=>'spark']);
        $bima->interests()->sync([$interests['teknologi']->id]);
        if($codingPath) Enrollment::updateOrCreate(['child_profile_id'=>$bima->id,'learning_path_id'=>$codingPath->id],['status'=>'active','enrolled_at'=>now()->subDays(10)]);

        $completeParent=User::updateOrCreate(['email'=>'parent.citra@skillpath.test'],['name'=>'Orang Tua Citra','password'=>Hash::make('password'),'role'=>'parent']);
        $citra=ChildProfile::updateOrCreate(['user_id'=>$completeParent->id],['name'=>'Citra','age'=>8,'avatar'=>'spark']);
        $citra->interests()->sync([$interests['seni']->id]);
        $starter=LearningPath::where('slug','creative-starter-gratis')->with('classSessions')->first();
        if($starter){
            Enrollment::updateOrCreate(['child_profile_id'=>$citra->id,'learning_path_id'=>$starter->id],['status'=>'active','enrolled_at'=>now()->subDays(10)]);
            $starterSession=$starter->classSessions->first();
            if($starterSession) SessionBooking::updateOrCreate(['class_session_id'=>$starterSession->id,'child_profile_id'=>$citra->id],['status'=>'attended','booked_at'=>now()->subDays(9),'checked_in_at'=>$starterSession->starts_at->copy()->addMinutes(5)]);
            Certificate::updateOrCreate(['child_profile_id'=>$citra->id,'learning_path_id'=>$starter->id],[
                'certificate_number'=>'CERT-SP-DEMO-CITRA','final_score'=>100,'issued_at'=>now()->subDays(6),'status'=>'active','issued_by'=>$admin->id,'revoked_at'=>null,'revoked_reason'=>null,
            ]);
        }

        if($codingPath){
            $history=ClassSession::updateOrCreate(['learning_path_id'=>$codingPath->id,'title'=>'Sesi Pengenalan Coding - Selesai'],[
                'instructor_id'=>$codingPath->instructor_id,'description'=>'Sesi historis untuk demo monitoring kehadiran.','starts_at'=>now()->subDays(5)->setTime(16,0),'ends_at'=>now()->subDays(5)->setTime(17,30),
                'venue_name'=>'Lab Kreatif Tamalanrea','address'=>'Jl. Perintis Kemerdekaan, Tamalanrea, Makassar','room'=>'Lab 1','capacity'=>20,'status'=>'completed','preparation_notes'=>'Tidak perlu membawa laptop.',
            ]);
            SessionBooking::updateOrCreate(['class_session_id'=>$history->id,'child_profile_id'=>$child->id],['status'=>'attended','booked_at'=>now()->subDays(8),'checked_in_at'=>$history->starts_at->copy()->addMinutes(4)]);
            SessionBooking::updateOrCreate(['class_session_id'=>$history->id,'child_profile_id'=>$bima->id],['status'=>'absent','booked_at'=>now()->subDays(8),'notes'=>'Tidak hadir tanpa check-in.']);
        }

        $createPaidDemoOrder=function(string $number,User $buyer,LearningPath $course,int $daysAgo,string $method){
            $normal=(float)$course->price; $final=$course->effectivePrice(); $discount=max(0,$normal-$final); $paidAt=now()->subDays($daysAgo)->setTime(10+($daysAgo%5),15);
            $order=Order::updateOrCreate(['order_number'=>$number],[
                'user_id'=>$buyer->id,'subtotal'=>$final,'discount'=>0,'total'=>$final,'payment_method'=>$method,'payment_status'=>'paid','status'=>'completed','paid_at'=>$paidAt,
                'created_at'=>$paidAt->copy()->subMinutes(10),'updated_at'=>$paidAt,
            ]);
            $order->items()->updateOrCreate(['learning_path_id'=>$course->id],['title_snapshot'=>$course->title,'price'=>$normal,'discount'=>$discount,'final_price'=>$final]);
        };
        if($codingPath){ $createPaidDemoOrder('SP-DEMO-001',$parent,$codingPath,3,'qris'); $createPaidDemoOrder('SP-DEMO-002',$inactiveParent,$codingPath,12,'virtual_account'); }
        if($artsPath) $createPaidDemoOrder('SP-DEMO-003',$parent,$artsPath,6,'ewallet');
        $english=LearningPath::where('slug','english-fun-conversation')->first(); if($english) $createPaidDemoOrder('SP-DEMO-004',$parent,$english,20,'qris');
    }
}
