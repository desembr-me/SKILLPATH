<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\ActivityCompletion; use App\Models\Enrollment; use App\Models\ModuleActivity; use Illuminate\Http\Request;
class LearnController extends Controller {
 public function show(Request $r,Enrollment $enrollment){
  abort_unless($enrollment->parent_id===$r->user()->id,403);
  $enrollment->load(['course.modules.activities','child','activityCompletions']);
  $completedIds=$enrollment->activityCompletions->pluck('module_activity_id')->all();
  return view('parent.learn',compact('enrollment','completedIds'));
 }
 public function toggleActivity(Request $r,Enrollment $enrollment,ModuleActivity $activity){
  abort_unless($enrollment->parent_id===$r->user()->id,403);
  $existing=ActivityCompletion::where('enrollment_id',$enrollment->id)->where('module_activity_id',$activity->id)->first();
  if($existing){$existing->delete();}else{ActivityCompletion::create(['enrollment_id'=>$enrollment->id,'module_activity_id'=>$activity->id,'completed_at'=>now()]);}
  $total=$enrollment->course->modules->sum(fn($m)=>$m->activities->count());
  $completed=$enrollment->activityCompletions()->count();
  if($total>0)$enrollment->update(['progress'=>(int)round($completed/$total*100)]);
  return back();
 }
}
