<?php
namespace App\Http\Controllers\Mentor;
use App\Http\Controllers\Controller; use App\Models\Enrollment; use App\Models\Review; use Illuminate\Http\Request;
class DashboardController extends Controller {public function __invoke(Request $r){$courses=$r->user()->courses()->with(['schedules.sessions','exams','category'])->get();$ids=$courses->pluck('id');$enrollments=Enrollment::with(['child','course.exams','schedule.sessions','examAttempts'])->whereIn('course_id',$ids)->whereIn('status',['active','completed'])->get();return view('mentor.dashboard',['courses'=>$courses,'enrollments'=>$enrollments,'students'=>$enrollments->where('status','active')->count(),'rating'=>round((float)Review::where('instructor_id',$r->user()->id)->avg('mentor_rating'),1)]);} }
