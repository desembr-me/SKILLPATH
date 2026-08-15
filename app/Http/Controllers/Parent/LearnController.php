<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Enrollment; use Illuminate\Http\Request;
class LearnController extends Controller {
 public function show(Request $r,Enrollment $enrollment){
  abort_unless($enrollment->parent_id===$r->user()->id,403);
  $enrollment->load(['course.modules.activities','child','activityCompletions']);
  $completedIds=$enrollment->activityCompletions->pluck('module_activity_id')->all();
  return view('parent.learn',compact('enrollment','completedIds'));
 }
}
