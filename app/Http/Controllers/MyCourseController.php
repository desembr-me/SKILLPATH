<?php
namespace App\Http\Controllers;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Http\Request;
class MyCourseController extends Controller
{
    public function index(Request $request){
        $child=$request->user()->childProfile;
        if(!$child) return redirect()->route('onboarding.edit');
        $enrollments=Enrollment::where('child_profile_id',$child->id)
            ->where('status','active')
            ->whereHas('learningPath', fn($query) => $query->where('is_published', true))
            ->with('learningPath.skill','learningPath.instructor.instructorProfile','learningPath.modules.activities')
            ->latest('enrolled_at')
            ->get();
        $completedIds=Progress::where('child_profile_id',$child->id)->where('status','completed')->pluck('activity_id');
        $courses=$enrollments->map(function($e) use($completedIds){
            $ids=$e->learningPath->modules->flatMap(fn($m)=>$m->activities->pluck('id'));
            $done=$ids->intersect($completedIds)->count();
            $pct=$ids->count()? (int)round($done/$ids->count()*100):0;
            return ['enrollment'=>$e,'course'=>$e->learningPath,'progress'=>$pct];
        });
        return view('my-courses.index',compact('child','courses'));
    }
}
