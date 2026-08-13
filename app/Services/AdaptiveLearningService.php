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
        $child->loadMissing('interests', 'favoriteInterest');
        $interestIds = $child->interests->pluck('id')->map(fn ($id) => (int) $id);
        $favoriteInterestId = $child->favorite_interest_id ? (int) $child->favorite_interest_id : null;
        $needSkillSlugs = $this->skillSlugsForNeed($child->learning_need);
        $history = $child->coDesignSessions()->latest('recorded_at')->take(3)->get();
        $stableInterestIds = $this->stableInterestIds($history);
        $voiceSignals = $this->voiceSignals($child->child_voice);

        $completedActivityIds = Progress::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'completed')
            ->pluck('activity_id');

        $paths = LearningPath::query()
            ->where('is_published', true)
            ->where('min_age', '<=', $child->age)
            ->where('max_age', '>=', $child->age)
            ->with(['skill', 'interests', 'categories', 'modules.activities'])
            ->get();

        $ranked = $paths
            ->map(function (LearningPath $path) use (
                $interestIds,
                $favoriteInterestId,
                $needSkillSlugs,
                $stableInterestIds,
                $completedActivityIds,
                $child,
                $voiceSignals
            ) {
                $pathInterestIds = $path->interests->pluck('id')->map(fn ($id) => (int) $id);
                $matchedInterests = $pathInterestIds->intersect($interestIds)->count();
                $stableMatches = $pathInterestIds->intersect($stableInterestIds)->count();
                $favoriteMatch = $favoriteInterestId
                    ? $pathInterestIds->contains($favoriteInterestId)
                    : false;

                $needMatch = in_array($path->skill?->slug, $needSkillSlugs, true);
                $selfImprovementMatch = in_array($child->learning_need, ['confidence', 'communication', 'independence'], true)
                    && $path->categories->contains('slug', 'self-improvement');
                $voiceSkillMatch = in_array($path->skill?->slug, $voiceSignals['skills'], true);
                $voiceCategoryMatch = $path->categories->pluck('slug')->intersect($voiceSignals['categories'])->isNotEmpty();
                $voiceMatch = $voiceSkillMatch || $voiceCategoryMatch;

                $activityIds = $path->modules
                    ->flatMap(fn ($module) => $module->activities->pluck('id'));

                $totalActivities = $activityIds->count();
                $completedCount = $activityIds->intersect($completedActivityIds)->count();
                $progressPercent = $totalActivities > 0
                    ? (int) round(($completedCount / $totalActivities) * 100)
                    : 0;
                $isOngoing = $completedCount > 0 && $completedCount < $totalActivities;
                $isCompleted = $totalActivities > 0 && $completedCount === $totalActivities;

                $breakdown = [
                    'interest' => min(45, $matchedInterests * 20),
                    'child_choice' => $favoriteMatch ? 30 : 0,
                    'stable_interest' => min(16, $stableMatches * 8),
                    'learning_need' => $needMatch ? 25 : 0,
                    'self_improvement' => $selfImprovementMatch ? 15 : 0,
                    'child_voice' => $voiceMatch ? 12 : 0,
                    'continuity' => $isOngoing ? 35 : 0,
                    'exploration' => $completedCount === 0 ? 5 : 0,
                    'completion_penalty' => $isCompleted ? -300 : 0,
                ];

                $score = array_sum($breakdown);

                $reason = match (true) {
                    $isOngoing => 'Lanjutkan progres',
                    $favoriteMatch && ($needMatch || $voiceMatch) => 'Pilihan anak & kebutuhan',
                    $favoriteMatch => 'Pilihan utama anak',
                    $stableMatches > 0 => 'Minat yang konsisten',
                    $voiceMatch => 'Sesuai suara anak',
                    $needMatch || $selfImprovementMatch => 'Sesuai kebutuhan',
                    $matchedInterests > 0 => 'Sesuai minat',
                    default => 'Eksplorasi baru',
                };

                return [
                    'path' => $path,
                    'score' => $score,
                    'score_breakdown' => $breakdown,
                    'matched_interests' => $matchedInterests,
                    'stable_interest_matches' => $stableMatches,
                    'favorite_interest_match' => $favoriteMatch,
                    'need_match' => $needMatch || $selfImprovementMatch,
                    'voice_match' => $voiceMatch,
                    'match_reason' => $reason,
                    'progress_percent' => $progressPercent,
                    'completed_activities' => $completedCount,
                    'total_activities' => $totalActivities,
                    'is_ongoing' => $isOngoing,
                    'is_completed' => $isCompleted,
                    'primary_category' => $path->categories->first()?->slug ?: 'uncategorized',
                ];
            })
            ->sortByDesc('score')
            ->values();

        return $this->diversify($ranked, $limit);
    }

    private function diversify(Collection $ranked, int $limit): Collection
    {
        $selected = collect();
        $deferred = collect();
        $categoryCounts = [];

        foreach ($ranked as $item) {
            if ($selected->count() >= $limit) {
                break;
            }

            $category = $item['primary_category'];
            $count = $categoryCounts[$category] ?? 0;

            if (! $item['is_ongoing'] && $count >= 2) {
                $deferred->push($item);
                continue;
            }

            $selected->push($item);
            $categoryCounts[$category] = $count + 1;
        }

        if ($selected->count() < $limit) {
            foreach ($deferred as $item) {
                $selected->push($item);
                if ($selected->count() >= $limit) {
                    break;
                }
            }
        }

        return $selected->take($limit)->values();
    }

    private function stableInterestIds(Collection $history): Collection
    {
        if ($history->count() < 2) {
            return collect();
        }

        $frequency = [];
        foreach ($history as $session) {
            foreach (array_unique(array_map('intval', $session->selected_interest_ids ?? [])) as $interestId) {
                $frequency[$interestId] = ($frequency[$interestId] ?? 0) + 1;
            }
        }

        $threshold = 2;

        return collect($frequency)
            ->filter(fn ($count) => $count >= $threshold)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->values();
    }

    private function voiceSignals(?string $voice): array
    {
        $text = strtolower((string) $voice);
        $skills = [];
        $categories = [];

        $rules = [
            [['bicara', 'ngobrol', 'presentasi', 'komunikasi', 'bahasa'], ['komunikasi'], ['languages']],
            [['teman', 'bersosialisasi', 'bergaul'], ['komunikasi'], ['self-improvement']],
            [['gambar', 'menggambar', 'warna', 'cerita', 'kreatif', 'seni'], ['kreativitas'], ['arts']],
            [['musik', 'ritme', 'lagu', 'nyanyi'], ['kreativitas'], ['music']],
            [['coding', 'komputer', 'teknologi', 'digital'], ['literasi-digital'], ['technology']],
            [['logika', 'teka-teki', 'masalah', 'eksperimen', 'sains'], ['problem-solving'], ['technology']],
            [['mandiri', 'sendiri', 'kebiasaan', 'disiplin'], ['kemandirian'], ['self-improvement']],
            [['percaya diri', 'berani', 'emosi', 'perasaan', 'malu'], ['komunikasi', 'kemandirian'], ['self-improvement']],
            [['olahraga', 'gerak', 'bola', 'aktif'], ['kemandirian'], ['sports']],
        ];

        foreach ($rules as [$keywords, $ruleSkills, $ruleCategories]) {
            if ($this->containsAny($text, $keywords)) {
                $skills = array_merge($skills, $ruleSkills);
                $categories = array_merge($categories, $ruleCategories);
            }
        }

        return [
            'skills' => array_values(array_unique($skills)),
            'categories' => array_values(array_unique($categories)),
        ];
    }

    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function skillSlugsForNeed(?string $need): array
    {
        return match ($need) {
            'confidence' => ['komunikasi', 'kemandirian'],
            'communication' => ['komunikasi'],
            'creativity' => ['kreativitas'],
            'independence' => ['kemandirian'],
            'problem_solving' => ['problem-solving'],
            'digital_literacy' => ['literasi-digital'],
            default => [],
        };
    }
}
