<?php
namespace App\Http\Controllers;
use App\Models\LearningPath;
use Illuminate\Http\Request;
class InstructorCourseController extends Controller
{
    public function edit(Request $request, LearningPath $learningPath){
        abort_unless($request->user()->role==='instructor' && $learningPath->instructor_id===$request->user()->id,403);
        $learningPath->load('modules.activities','liveSessions');
        return view('instructor.courses.edit',compact('learningPath'));
    }
    public function update(Request $request, LearningPath $learningPath){
        abort_unless($request->user()->role==='instructor' && $learningPath->instructor_id===$request->user()->id,403);
        $data=$request->validate([
            'price'=>['required','numeric','min:0'],'sale_price'=>['nullable','numeric','min:0'],
            'course_type'=>['required','in:self_paced,live,hybrid'],'live_class_enabled'=>['nullable','boolean'],
            'learning_outcomes'=>['nullable','string','max:3000'],'requirements'=>['nullable','string','max:3000'],
        ]);
        $data['is_free']=(float)$data['price']===0.0;
        $data['live_class_enabled']=$request->boolean('live_class_enabled');
        $learningPath->update($data);
        return back()->with('success','Informasi course berhasil diperbarui.');
    }
}
