<?php
namespace App\Http\Controllers;
use App\Models\CourseAnswer;
use App\Models\CourseQuestion;
use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Http\Request;
class CourseQuestionController extends Controller
{
    public function store(Request $request, LearningPath $learningPath){
        $child=$request->user()->childProfile; abort_unless($child,403);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$learningPath->id)->where('status','active')->exists(),403);
        $data=$request->validate(['question'=>['required','string','max:1500']]);
        CourseQuestion::create(['user_id'=>$request->user()->id,'child_profile_id'=>$child->id,'learning_path_id'=>$learningPath->id,'question'=>$data['question']]);
        return back()->with('success','Pertanyaan dikirim ke pengajar.');
    }
    public function answer(Request $request, CourseQuestion $courseQuestion){
        abort_unless($request->user()->role === 'instructor' && $courseQuestion->learningPath->instructor_id === $request->user()->id,403);
        $data=$request->validate(['answer'=>['required','string','max:2000']]);
        CourseAnswer::create(['course_question_id'=>$courseQuestion->id,'user_id'=>$request->user()->id,'answer'=>$data['answer'],'is_instructor'=>true]);
        $courseQuestion->update(['is_resolved'=>true]);
        return back()->with('success','Jawaban berhasil dikirim.');
    }
}
