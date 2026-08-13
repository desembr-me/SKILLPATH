<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Progress;
use App\Services\StudentProgressCalculator;
use Illuminate\Http\Request;

class InstructorProgressController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);
        $courseIds = $request->user()->coursesTaught()->pluck('id');

        $enrollments = Enrollment::whereIn('learning_path_id', $courseIds)
            ->with(['childProfile.user', 'learningPath.modules.activities'])
            ->latest('enrolled_at')
            ->get()
            ->each(function (Enrollment $enrollment) {
                $stats = StudentProgressCalculator::forEnrollment($enrollment);
                $enrollment->completion_rate = $stats['completion_rate'];
                $enrollment->completed_activities = $stats['completed_activities'];
                $enrollment->total_activities = $stats['total_activities'];
            });

        return view('instructor.progress.index', compact('enrollments'));
    }

    public function show(Request $request, Enrollment $enrollment)
    {
        abort_unless($request->user()->role === 'instructor', 403);
        $enrollment->load('childProfile.user', 'learningPath.modules.activities');
        abort_unless($enrollment->learningPath->instructor_id === $request->user()->id, 403);

        $activityIds = $enrollment->learningPath->modules->flatMap(fn ($module) => $module->activities->pluck('id'));
        $progress = Progress::where('child_profile_id', $enrollment->child_profile_id)
            ->whereIn('activity_id', $activityIds)
            ->get()
            ->keyBy('activity_id');

        $totalActivities = $activityIds->count();
        $completedActivities = $progress->where('status', 'completed')->count();
        $completionRate = $totalActivities > 0 ? (int) round($completedActivities / $totalActivities * 100) : 0;
        $scores = $progress->pluck('score')->filter(fn ($score) => ! is_null($score));

        return view('instructor.progress.show', compact(
            'enrollment', 'progress', 'completionRate', 'totalActivities', 'completedActivities', 'scores'
        ));
    }
}
