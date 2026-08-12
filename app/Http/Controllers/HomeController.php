<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\LearningPath;
use App\Models\User;
class HomeController extends Controller
{
    public function index(){
        $categories=Category::withCount(['learningPaths'=>fn($q)=>$q->where('is_published',true)])->orderBy('id')->get();
        $featuredPaths=LearningPath::where('is_published',true)->with(['skill','categories','instructor.instructorProfile','reviews'])->withCount('enrollments')->orderByDesc('published_at')->take(6)->get();
        $featuredInstructors=User::where('role','instructor')->with('instructorProfile')->withCount('coursesTaught')->take(3)->get();
        return view('home',compact('categories','featuredPaths','featuredInstructors'));
    }
}
