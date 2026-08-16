<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\RescheduleRequest;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $courses = $user->courses()->with(['schedules.sessions', 'exams', 'category'])->get();
        $courseIds = $courses->pluck('id');

        $enrollments = Enrollment::with([
            'child',
            'course.exams',
            'schedule.sessions',
            'examAttempts',
            'attendance.courseSession',
        ])
        ->whereIn('course_id', $courseIds)
        ->whereIn('status', ['active', 'completed'])
        ->get();

        // Calculate total earnings
        $totalEarnings = Transaction::whereHas('enrollment', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('status', 'paid')->sum('subtotal');

        // Pending reschedule requests
        $pendingReschedules = RescheduleRequest::where('mentor_id', $user->id)
            ->where('status', 'pending')
            ->with(['enrollment.child', 'enrollment.course', 'currentSchedule', 'requestedSchedule', 'parent'])
            ->latest()
            ->get();

        $rating = round((float) Review::where('instructor_id', $user->id)->avg('mentor_rating'), 1);

        return view('mentor.dashboard', [
            'courses' => $courses,
            'enrollments' => $enrollments,
            'students' => $enrollments->where('status', 'active')->count(),
            'rating' => $rating,
            'totalEarnings' => $totalEarnings,
            'pendingReschedules' => $pendingReschedules,
            'days' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ]);
    }
}
