<?php

namespace App\Services;

use App\Models\ChildProfile;
use Illuminate\Support\Collection;

class StudentProgressService
{
    public function ensureLoaded(ChildProfile $child): ChildProfile
    {
        return $child->loadMissing([
            'user',
            'interests',
            'enrollments.learningPath.instructor',
            'enrollments.learningPath.modules.activities',
            'progress.activity.module.learningPath',
        ]);
    }

    public function summarize(ChildProfile $child, ?int $courseId = null): array
    {
        $this->ensureLoaded($child);

        $enrollments = $child->enrollments
            ->filter(fn ($enrollment) => $enrollment->status === 'active' && $enrollment->learningPath)
            ->when(
                $courseId,
                fn (Collection $items) => $items->where('learning_path_id', $courseId)
            )
            ->values();

        $activityIds = $enrollments
            ->flatMap(fn ($enrollment) => $enrollment->learningPath->modules
                ->flatMap(fn ($module) => $module->activities->pluck('id')))
            ->unique()
            ->values();

        $completedProgress = $child->progress
            ->where('status', 'completed')
            ->whereIn('activity_id', $activityIds)
            ->unique('activity_id')
            ->values();

        $totalActivities = $activityIds->count();
        $completedActivities = $completedProgress->count();
        $remainingActivities = max(0, $totalActivities - $completedActivities);

        $progressPercent = $totalActivities > 0
            ? (int) round(($completedActivities / $totalActivities) * 100)
            : 0;

        $scores = $completedProgress
            ->pluck('score')
            ->filter(fn ($score) => $score !== null);

        $lastActivityAt = $completedProgress
            ->pluck('completed_at')
            ->filter()
            ->sortDesc()
            ->first();

        $firstEnrollmentAt = $enrollments
            ->pluck('enrolled_at')
            ->filter()
            ->sort()
            ->first();

        $daysInactive = $lastActivityAt
            ? $lastActivityAt->diffInDays(now())
            : ($firstEnrollmentAt ? $firstEnrollmentAt->diffInDays(now()) : null);

        $status = $this->resolveStatus(
            enrollmentCount: $enrollments->count(),
            completedActivities: $completedActivities,
            progressPercent: $progressPercent,
            lastActivityAt: $lastActivityAt,
            firstEnrollmentAt: $firstEnrollmentAt,
        );

        return [
            'child' => $child,
            'enrollment_count' => $enrollments->count(),
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'remaining_activities' => $remainingActivities,
            'progress_percent' => $progressPercent,
            'points' => (int) $completedProgress->sum('points_awarded'),
            'average_score' => $scores->isNotEmpty() ? round((float) $scores->avg(), 1) : null,
            'last_activity_at' => $lastActivityAt,
            'first_enrollment_at' => $firstEnrollmentAt,
            'days_inactive' => $daysInactive,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'attention_reason' => $this->attentionReason(
                $status,
                $completedActivities,
                $lastActivityAt,
                $firstEnrollmentAt,
                $daysInactive,
            ),
        ];
    }

    public function courseBreakdown(ChildProfile $child): Collection
    {
        $this->ensureLoaded($child);

        return $child->enrollments
            ->filter(fn ($enrollment) => $enrollment->learningPath)
            ->map(function ($enrollment) use ($child) {
                $path = $enrollment->learningPath;

                $activityIds = $path->modules
                    ->flatMap(fn ($module) => $module->activities->pluck('id'))
                    ->unique()
                    ->values();

                $completed = $child->progress
                    ->where('status', 'completed')
                    ->whereIn('activity_id', $activityIds)
                    ->unique('activity_id')
                    ->values();

                $total = $activityIds->count();
                $done = $completed->count();
                $percent = $total > 0 ? (int) round(($done / $total) * 100) : 0;

                $scores = $completed
                    ->pluck('score')
                    ->filter(fn ($score) => $score !== null);

                $lastActivityAt = $completed
                    ->pluck('completed_at')
                    ->filter()
                    ->sortDesc()
                    ->first();

                $courseStatus = match (true) {
                    $percent >= 100 => 'completed',
                    $done === 0 => 'not_started',
                    $lastActivityAt && $lastActivityAt->lt(now()->subDays(14)) => 'needs_attention',
                    default => 'active',
                };

                return [
                    'enrollment' => $enrollment,
                    'path' => $path,
                    'total_activities' => $total,
                    'completed_activities' => $done,
                    'remaining_activities' => max(0, $total - $done),
                    'progress_percent' => $percent,
                    'points' => (int) $completed->sum('points_awarded'),
                    'average_score' => $scores->isNotEmpty() ? round((float) $scores->avg(), 1) : null,
                    'last_activity_at' => $lastActivityAt,
                    'status' => $courseStatus,
                    'status_label' => $this->statusLabel($courseStatus),
                ];
            })
            ->sortByDesc(fn ($item) => $item['enrollment']->enrolled_at)
            ->values();
    }

    private function resolveStatus(
        int $enrollmentCount,
        int $completedActivities,
        int $progressPercent,
        $lastActivityAt,
        $firstEnrollmentAt,
    ): string {
        if ($enrollmentCount === 0) {
            return 'no_course';
        }

        if ($progressPercent >= 100) {
            return 'completed';
        }

        if ($completedActivities === 0) {
            if ($firstEnrollmentAt && $firstEnrollmentAt->lt(now()->subDays(7))) {
                return 'needs_attention';
            }

            return 'not_started';
        }

        if ($lastActivityAt && $lastActivityAt->lt(now()->subDays(14))) {
            return 'needs_attention';
        }

        return 'active';
    }

    private function attentionReason(
        string $status,
        int $completedActivities,
        $lastActivityAt,
        $firstEnrollmentAt,
        ?int $daysInactive,
    ): ?string {
        if ($status !== 'needs_attention') {
            return null;
        }

        if ($completedActivities === 0 && $firstEnrollmentAt) {
            return 'Belum memulai aktivitas setelah '.$firstEnrollmentAt->diffInDays(now()).' hari sejak enrollment.';
        }

        if ($lastActivityAt && $daysInactive !== null) {
            return 'Tidak ada aktivitas baru selama '.$daysInactive.' hari.';
        }

        return 'Progres perlu ditinjau admin atau orang tua.';
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'needs_attention' => 'Perlu perhatian',
            'not_started' => 'Belum mulai',
            default => 'Belum ada course',
        };
    }
}
