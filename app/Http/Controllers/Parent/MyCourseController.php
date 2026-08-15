<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use Illuminate\Http\Request;
class MyCourseController extends Controller { public function index(Request $r){$enrollments=$r->user()->enrollments()->with(['course.category','child','certificate'])->whereIn('status',['active','completed'])->latest()->get();return view('parent.my-courses',compact('enrollments'));} }
