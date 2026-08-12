<?php
namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Progress;
use Illuminate\Http\Request;
class CertificateController extends Controller
{
    public function show(Request $request, LearningPath $learningPath){
        $child=$request->user()->childProfile; abort_unless($child,403);
        abort_unless($learningPath->certificate_enabled,404);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$learningPath->id)->exists(),403);
        $learningPath->load('modules.activities','instructor.instructorProfile');
        $ids=$learningPath->modules->flatMap(fn($m)=>$m->activities->pluck('id'));
        $progress=Progress::where('child_profile_id',$child->id)->whereIn('activity_id',$ids)->where('status','completed')->get();
        abort_unless($ids->count()>0 && $progress->count()===$ids->count(),422,'Selesaikan semua aktivitas untuk memperoleh sertifikat.');
        $score=$progress->whereNotNull('score')->avg('score');
        $certificate=Certificate::firstOrCreate(
            ['child_profile_id'=>$child->id,'learning_path_id'=>$learningPath->id],
            ['certificate_number'=>'CERT-SP-'.now()->format('Ym').'-'.strtoupper(substr(md5($child->id.'-'.$learningPath->id),0,8)),'final_score'=>$score,'issued_at'=>now()]
        );
        return view('certificates.show',compact('certificate','child','learningPath'));
    }
}
