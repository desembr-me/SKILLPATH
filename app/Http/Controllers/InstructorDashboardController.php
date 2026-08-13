<?php
namespace App\Http\Controllers;
use App\Models\CourseQuestion;
use App\Models\Enrollment;
use App\Services\StudentProgressCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InstructorDashboardController extends Controller
{
    public function __invoke(Request $request){
        abort_unless($request->user()->role === 'instructor',403);
        $courses=$request->user()->coursesTaught()->withCount(['enrollments','reviews'])->with('categories')->orderBy('title')->get();
        $courseIds=$courses->pluck('id');
        $questions=CourseQuestion::whereIn('learning_path_id',$courseIds)->with(['user','learningPath','answers'])->latest()->take(8)->get();
        $studentCount=Enrollment::whereIn('learning_path_id',$courseIds)->where('status','active')->distinct('child_profile_id')->count('child_profile_id');

        $enrollments=Enrollment::whereIn('learning_path_id',$courseIds)->with('learningPath.modules.activities')->get();
        $avgCompletion=StudentProgressCalculator::averageCompletionRate($enrollments);

        $revenue=DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->whereIn('order_items.learning_path_id',$courseIds)
            ->where('orders.payment_status','paid')
            ->sum('order_items.final_price');

        return view('instructor.dashboard',compact('courses','questions','studentCount','avgCompletion','revenue'));
    }
}
