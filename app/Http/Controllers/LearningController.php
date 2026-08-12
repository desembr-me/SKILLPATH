<?php
namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\Progress;
use Illuminate\Http\Request;
class LearningController extends Controller
{
    private function ensureAccess(Request $request, LearningPath $course): mixed
    {
        $child=$request->user()->childProfile;
        if(!$child) return redirect()->route('onboarding.edit');
        abort_unless($course->is_published && $child->age >= $course->min_age && $child->age <= $course->max_age,404);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$course->id)->where('status','active')->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->exists(),403,'Course belum dibeli atau akses sudah berakhir.');
        return $child;
    }
    public function showPath(Request $request, LearningPath $learningPath){
        $child=$this->ensureAccess($request,$learningPath); if($child instanceof \Illuminate\Http\RedirectResponse) return $child;
        $learningPath->load(['skill','interests','modules.activities','instructor.instructorProfile']);
        $ids=$learningPath->modules->flatMap(fn($m)=>$m->activities->pluck('id'));
        $completedIds=Progress::where('child_profile_id',$child->id)->where('status','completed')->whereIn('activity_id',$ids)->pluck('activity_id');
        $total=$ids->count(); $completed=$completedIds->count(); $progressPercent=$total?(int)round($completed/$total*100):0;
        $nextActivity=$learningPath->modules->flatMap(fn($m)=>$m->activities)->first(fn($a)=>!$completedIds->contains($a->id));
        return view('learning.path',compact('learningPath','completedIds','progressPercent','nextActivity'));
    }
    public function showModule(Request $request, Module $module){
        $module->load(['learningPath.skill','activities']);
        $child=$this->ensureAccess($request,$module->learningPath); if($child instanceof \Illuminate\Http\RedirectResponse) return $child;
        $completedIds=Progress::where('child_profile_id',$child->id)->where('status','completed')->whereIn('activity_id',$module->activities->pluck('id'))->pluck('activity_id');
        return view('learning.module',compact('module','completedIds'));
    }
    public function completeActivity(Request $request, Activity $activity){
        $activity->load('module.learningPath');
        $child=$this->ensureAccess($request,$activity->module->learningPath); if($child instanceof \Illuminate\Http\RedirectResponse) return $child;
        $data=$request->validate(['score'=>['nullable','integer','between:0,100']]);
        Progress::updateOrCreate(['child_profile_id'=>$child->id,'activity_id'=>$activity->id],[
            'status'=>'completed','score'=>$data['score']??null,'points_awarded'=>$activity->points,'completed_at'=>now(),
        ]);
        return back()->with('success','Aktivitas selesai. Poinmu bertambah!');
    }
}
