<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function show(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->course->instructor_id === $request->user()->id, 403);

        $enrollment->load(['child', 'course.exams', 'schedule', 'attendance.courseSession', 'examAttempts.exam', 'certificate']);

        return view('mentor.student', [
            'enrollment' => $enrollment,
            'child' => $enrollment->child,
        ]);
    }
}
