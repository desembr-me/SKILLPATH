<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Enrollment; use Illuminate\Http\Request;
class ReviewController extends Controller { public function store(Request $r,Enrollment $enrollment){abort_unless($enrollment->parent_id===$r->user()->id,403);$d=$r->validate(['mentor_rating'=>'required|integer|min:1|max:5','mentor_review'=>'nullable|max:1500','platform_rating'=>'required|integer|min:1|max:5','platform_review'=>'nullable|max:1500']);$enrollment->review()->updateOrCreate([],array_merge($d,['parent_id'=>$r->user()->id,'course_id'=>$enrollment->course_id,'instructor_id'=>$enrollment->course->instructor_id]));return back()->with('success','Ulasan mentor dan platform berhasil disimpan.');} }
