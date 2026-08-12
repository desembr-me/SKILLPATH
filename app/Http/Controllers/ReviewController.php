<?php
namespace App\Http\Controllers;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Http\Request;
class ReviewController extends Controller
{
    public function store(Request $request, LearningPath $learningPath){
        $child=$request->user()->childProfile; abort_unless($child,403);
        abort_unless(Enrollment::where('child_profile_id',$child->id)->where('learning_path_id',$learningPath->id)->exists(),403);
        $data=$request->validate(['rating'=>['required','integer','between:1,5'],'review'=>['nullable','string','max:1000']]);
        CourseReview::updateOrCreate(['user_id'=>$request->user()->id,'learning_path_id'=>$learningPath->id],$data+['is_approved'=>true]);
        return back()->with('success','Ulasan berhasil disimpan.');
    }
}
