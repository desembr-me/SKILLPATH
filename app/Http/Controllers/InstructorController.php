<?php
namespace App\Http\Controllers;
use App\Models\User;
class InstructorController extends Controller
{
    public function index(){
        $instructors = User::query()->where('role','instructor')->with('instructorProfile')->withCount(['coursesTaught'=>fn($q)=>$q->where('is_published',true)])->orderBy('name')->get();
        return view('instructors.index', compact('instructors'));
    }
    public function show(User $instructor){
        abort_unless($instructor->role === 'instructor', 404);
        $instructor->load(['instructorProfile','coursesTaught'=>fn($q)=>$q->where('is_published',true)->with(['skill','categories','reviews'])]);
        return view('instructors.show', compact('instructor'));
    }
}
