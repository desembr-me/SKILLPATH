<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Support\Collection;

class StudentProgressCalculator
{
    /**
     * Requires $enrollment->learningPath.modules.activities to be eager-loaded.
     */
    public static function forEnrollment(Enrollment $enrollment): array
    {
        $activityIds = $enrollment->learningPath->modules->flatMap(fn ($module) => $module->activities->pluck('id'));
        $total = $activityIds->count();
        $completed = $total > 0
            ? Progress::where('child_profile_id', $enrollment->child_profile_id)
                ->whereIn('activity_id', $activityIds)
                ->where('status', 'completed')
                ->count()
            : 0;

        return [
            'total_activities' => $total,
            'completed_activities' => $completed,
            'completion_rate' => $total > 0 ? (int) round($completed / $total * 100) : 0,
        ];
    }

    public static function averageCompletionRate(Collection $enrollments): int
    {
        $totalActivities = 0;
        $totalCompleted = 0;

        foreach ($enrollments as $enrollment) {
            $stats = self::forEnrollment($enrollment);
            $totalActivities += $stats['total_activities'];
            $totalCompleted += $stats['completed_activities'];
        }

        return $totalActivities > 0 ? (int) round($totalCompleted / $totalActivities * 100) : 0;
    }
}
