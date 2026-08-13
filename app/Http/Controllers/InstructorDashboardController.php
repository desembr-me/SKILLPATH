<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\CourseQuestion;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class InstructorDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);

        $courses = $request->user()->coursesTaught()
            ->withCount(['enrollments','reviews'])
            ->with('categories')
            ->orderBy('title')
            ->get();
        $courseIds = $courses->pluck('id');

        $upcoming = ClassSession::where('instructor_id', $request->user()->id)
            ->where('status', 'scheduled')
            ->where('starts_at', '>=', now())
            ->with(['learningPath','bookings'])
            ->orderBy('starts_at')
            ->take(6)
            ->get();

        $questions = CourseQuestion::whereIn('learning_path_id', $courseIds)
            ->with(['user','learningPath','answers'])
            ->latest()
            ->take(8)
            ->get();

        $studentCount = Enrollment::whereIn('learning_path_id', $courseIds)
            ->where('status','active')
            ->distinct('child_profile_id')
            ->count('child_profile_id');

        return view('instructor.dashboard', compact('courses','upcoming','questions','studentCount'));
    }
}
