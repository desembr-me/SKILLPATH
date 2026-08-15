<?php
namespace App\Http\Controllers;
use App\Models\Category; use App\Models\Course; use Illuminate\Http\Request;
class CourseController extends Controller {
 public function index(Request $r){$q=Course::with(['category','instructor'])->where('status','active'); if($r->filled('category'))$q->whereHas('category',fn($x)=>$x->where('slug',$r->category)); if($r->filled('age'))$q->where('age_min','<=',$r->age)->where('age_max','>=',$r->age); return view('courses.index',['courses'=>$q->paginate(12)->withQueryString(),'categories'=>Category::all()]);}
 public function show(Course $course){$course->load(['category','instructor','schedules']); return view('courses.show',compact('course'));}
}
