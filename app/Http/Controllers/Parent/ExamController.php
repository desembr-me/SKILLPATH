<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Enrollment; use Illuminate\Http\Request;
class ExamController extends Controller { public function index(Request $r){$enrollments=Enrollment::with(['course.exams','examAttempts.exam','certificate'])->where('parent_id',$r->user()->id)->get();return view('parent.exams',compact('enrollments'));} }
