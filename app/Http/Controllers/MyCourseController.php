<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\SessionBooking;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{
    public function index(Request $request)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        $enrollments = Enrollment::where('child_profile_id', $child->id)
            ->where('status', 'active')
            ->whereHas('learningPath', fn ($query) => $query->where('is_published', true))
            ->with([
                'learningPath.skill',
                'learningPath.instructor.instructorProfile',
                'learningPath.classSessions' => fn ($query) => $query->orderBy('starts_at'),
            ])
            ->latest('enrolled_at')
            ->get();

        $bookings = SessionBooking::where('child_profile_id', $child->id)
            ->with('classSession')
            ->get()
            ->keyBy('class_session_id');

        $courses = $enrollments->map(function ($enrollment) use ($bookings) {
            $course = $enrollment->learningPath;
            $nextSession = $course->classSessions
                ->where('status', 'scheduled')
                ->where('starts_at', '>=', now())
                ->first();

            $courseSessionIds = $course->classSessions->pluck('id');
            $courseBookings = $bookings->whereIn('class_session_id', $courseSessionIds);

            return [
                'enrollment' => $enrollment,
                'course' => $course,
                'next_session' => $nextSession,
                'next_booking' => $nextSession ? $bookings->get($nextSession->id) : null,
                'attended_count' => $courseBookings->where('status', 'attended')->count(),
                'booked_count' => $courseBookings->where('status', 'booked')->count(),
                'certificate' => $course->certificates()
                    ->where('child_profile_id', $enrollment->child_profile_id)
                    ->where('status', 'active')
                    ->first(),
            ];
        });

        return view('my-courses.index', compact('child','courses'));
    }
}
