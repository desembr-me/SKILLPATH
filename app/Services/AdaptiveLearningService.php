<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\LearningPath;
use App\Models\Progress;
use Illuminate\Support\Collection;

class AdaptiveLearningService
{
    public function recommend(ChildProfile $child, int $limit = 4): Collection
    {
        $interestIds = $child->interests()->pluck('interests.id');

        $completedActivityIds = Progress::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'completed')
            ->pluck('activity_id');

        $paths = LearningPath::query()
            ->where('is_published', true)
            ->where('min_age', '<=', $child->age)
            ->where('max_age', '>=', $child->age)
            ->with(['skill', 'interests', 'modules.activities'])
            ->get();

        return $paths
            ->map(function (LearningPath $path) use ($interestIds, $completedActivityIds) {
                $matchedInterests = $path->interests
                    ->pluck('id')
                    ->intersect($interestIds)
                    ->count();

                $activityIds = $path->modules
                    ->flatMap(fn ($module) => $module->activities->pluck('id'));

                $totalActivities = $activityIds->count();

                $completedCount = $activityIds
                    ->intersect($completedActivityIds)
                    ->count();

                $progressPercent = $totalActivities > 0
                    ? (int) round(($completedCount / $totalActivities) * 100)
                    : 0;

                $interestScore = min(60, $matchedInterests * 30);

                $continuityBonus = $completedCount > 0 && $completedCount < $totalActivities
                    ? 25
                    : 0;

                $newPathBonus = $completedCount === 0
                    ? 10
                    : 0;

                $completionPenalty = $totalActivities > 0 && $completedCount === $totalActivities
                    ? 40
                    : 0;

                $score = $interestScore
                    + $continuityBonus
                    + $newPathBonus
                    - $completionPenalty;

                return [
                    'path' => $path,
                    'score' => $score,
                    'matched_interests' => $matchedInterests,
                    'progress_percent' => $progressPercent,
                    'completed_activities' => $completedCount,
                    'total_activities' => $totalActivities,
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }
}
