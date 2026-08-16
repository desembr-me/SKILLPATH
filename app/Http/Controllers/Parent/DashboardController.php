<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $children = $user->children()
            ->with(['enrollments.course', 'enrollments.certificate'])
            ->get();

        $transactions = $user->transactions()
            ->with(['enrollments.course', 'enrollments.child', 'enrollment.course', 'enrollment.child'])
            ->latest()
            ->limit(5)
            ->get();

        // 1. Platform Review (1x per parent account)
        $platformReview = $user->platformReview;

        // 2. Mentor Enrollments (Only courses child has followed with active/completed status)
        $mentorEnrollments = $user->enrollments()
            ->whereIn('status', ['active', 'completed'])
            ->with(['course.instructor', 'course.category', 'child', 'review'])
            ->latest()
            ->get();

        // Backward compatibility
        $reviewable = $mentorEnrollments->filter(fn($e) => !$e->review);

        $mentors = User::where('role', 'mentor')
            ->with('category')
            ->limit(3)
            ->get();

        // 3. Notifikasi & Pengingat Jadwal Hari Ini
        $now = Carbon::now();
        $todayDayOfWeek = (int) $now->dayOfWeek; // 0=Minggu, 1=Senin, ..., 6=Sabtu
        $todayDateString = Carbon::today()->toDateString();
        $todayDateFormatted = $now->locale('id')->translatedFormat('l, d F Y');

        $todaySchedules = $user->enrollments()
            ->where('status', 'active')
            ->whereHas('schedule', function ($q) use ($todayDayOfWeek, $todayDateString) {
                $q->where('day_of_week', $todayDayOfWeek)
                  ->orWhereHas('sessions', function ($sq) use ($todayDateString) {
                      $sq->whereDate('session_date', $todayDateString);
                  });
            })
            ->with([
                'course.instructor',
                'course.category',
                'schedule.sessions' => function ($sq) use ($todayDateString) {
                    $sq->whereDate('session_date', $todayDateString);
                },
                'child'
            ])
            ->get()
            ->map(function ($enrollment) use ($now, $todayDateString) {
                $schedule = $enrollment->schedule;
                $startTime = Carbon::parse($schedule->start_time);
                $endTime = Carbon::parse($schedule->end_time);

                $isOngoing = $now->between($startTime, $endTime);
                $isUpcoming = $now->lt($startTime);
                $isFinished = $now->gt($endTime);

                $todaySession = $schedule->sessions ? $schedule->sessions->first() : null;

                return [
                    'enrollment' => $enrollment,
                    'course' => $enrollment->course,
                    'instructor' => $enrollment->course->instructor,
                    'child' => $enrollment->child,
                    'schedule' => $schedule,
                    'session' => $todaySession,
                    'is_ongoing' => $isOngoing,
                    'is_upcoming' => $isUpcoming,
                    'is_finished' => $isFinished,
                    'time_label' => substr($schedule->start_time, 0, 5) . ' - ' . substr($schedule->end_time, 0, 5) . ' WIB',
                    'room' => $schedule->room ?: ($enrollment->course->location_name ?: 'Ruang Kelas'),
                ];
            });

        // If no schedules today, find the nearest upcoming schedule in the week
        $upcomingNextSchedule = null;
        if ($todaySchedules->isEmpty()) {
            $upcomingNextSchedule = $user->enrollments()
                ->where('status', 'active')
                ->with(['course.instructor', 'schedule', 'child'])
                ->get()
                ->sortBy(function ($enrollment) use ($todayDayOfWeek) {
                    $schedDay = (int) $enrollment->schedule->day_of_week;
                    $diff = ($schedDay - $todayDayOfWeek + 7) % 7;
                    return $diff === 0 ? 7 : $diff;
                })
                ->first();
        }

        return view('parent.dashboard', [
            'children' => $children,
            'transactions' => $transactions,
            'platformReview' => $platformReview,
            'mentorEnrollments' => $mentorEnrollments,
            'reviewable' => $reviewable,
            'mentors' => $mentors,
            'todaySchedules' => $todaySchedules,
            'upcomingNextSchedule' => $upcomingNextSchedule,
            'todayDateFormatted' => $todayDateFormatted,
        ]);
    }
}
