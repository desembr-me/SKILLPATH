<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\LearningPath;
use Illuminate\Support\Collection;

class AdaptiveLearningService
{
    public function recommend(ChildProfile $child, int $limit = 4): Collection
    {
        $interestIds = $child->interests()->pluck('interests.id');
        $enrolledPathIds = $child->enrollments()
            ->where('status', 'active')
            ->pluck('learning_path_id');

        $paths = LearningPath::query()
            ->where('is_published', true)
            ->where('min_age', '<=', $child->age)
            ->where('max_age', '>=', $child->age)
            ->with([
                'skill',
                'interests',
                'classSessions' => fn ($query) => $query
                    ->where('status', 'scheduled')
                    ->where('starts_at', '>=', now())
                    ->orderBy('starts_at'),
            ])
            ->get();

        return $paths
            ->map(function (LearningPath $path) use ($interestIds, $enrolledPathIds) {
                $matchedInterests = $path->interests
                    ->pluck('id')
                    ->intersect($interestIds)
                    ->count();

                $isEnrolled = $enrolledPathIds->contains($path->id);
                $nextSession = $path->classSessions->first();

                $score = min(60, $matchedInterests * 30)
                    + ($nextSession ? 20 : 0)
                    + ($isEnrolled ? -35 : 10);

                return [
                    'path' => $path,
                    'score' => $score,
                    'matched_interests' => $matchedInterests,
                    'is_enrolled' => $isEnrolled,
                    'next_session' => $nextSession,
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }
}
