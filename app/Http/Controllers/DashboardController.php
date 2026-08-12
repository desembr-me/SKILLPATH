<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Services\AdaptiveLearningService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, AdaptiveLearningService $adaptive)
    {
        $child = $request->user()->childProfile;

        if (! $child) {
            return redirect()->route('onboarding.edit');
        }

        $child->load('interests');

        $recommendations = $adaptive->recommend($child, 4);

        $completedActivities = Progress::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'completed')
            ->count();

        $totalPoints = Progress::query()
            ->where('child_profile_id', $child->id)
            ->sum('points_awarded');

        return view('dashboard', compact(
            'child',
            'recommendations',
            'completedActivities',
            'totalPoints'
        ));
    }
}
