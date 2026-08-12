<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\Progress;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function showPath(Request $request, LearningPath $learningPath)
    {
        $child = $request->user()->childProfile;

        if (! $child) {
            return redirect()->route('onboarding.edit');
        }

        abort_unless(
            $learningPath->is_published
            && $child->age >= $learningPath->min_age
            && $child->age <= $learningPath->max_age,
            404
        );

        $learningPath->load(['skill', 'interests', 'modules.activities']);

        $activityIds = $learningPath->modules
            ->flatMap(fn ($module) => $module->activities->pluck('id'));

        $completedIds = Progress::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'completed')
            ->whereIn('activity_id', $activityIds)
            ->pluck('activity_id');

        $total = $activityIds->count();
        $completed = $completedIds->count();
        $progressPercent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $nextActivity = $learningPath->modules
            ->flatMap(fn ($module) => $module->activities)
            ->first(fn ($activity) => ! $completedIds->contains($activity->id));

        return view('learning.path', compact(
            'learningPath',
            'completedIds',
            'progressPercent',
            'nextActivity'
        ));
    }

    public function showModule(Request $request, Module $module)
    {
        $child = $request->user()->childProfile;

        if (! $child) {
            return redirect()->route('onboarding.edit');
        }

        $module->load(['learningPath.skill', 'activities']);

        abort_unless(
            $module->learningPath->is_published
            && $child->age >= $module->learningPath->min_age
            && $child->age <= $module->learningPath->max_age,
            404
        );

        $completedIds = Progress::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'completed')
            ->whereIn('activity_id', $module->activities->pluck('id'))
            ->pluck('activity_id');

        return view('learning.module', compact('module', 'completedIds'));
    }

    public function completeActivity(Request $request, Activity $activity)
    {
        $child = $request->user()->childProfile;

        abort_unless($child, 403);

        $data = $request->validate([
            'score' => ['nullable', 'integer', 'between:0,100'],
        ]);

        Progress::updateOrCreate(
            [
                'child_profile_id' => $child->id,
                'activity_id' => $activity->id,
            ],
            [
                'status' => 'completed',
                'score' => $data['score'] ?? null,
                'points_awarded' => $activity->points,
                'completed_at' => now(),
            ]
        );

        return back()->with('success', 'Aktivitas selesai. Poinmu bertambah!');
    }
}
