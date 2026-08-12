<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Progress;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function __invoke(Request $request)
    {
        $child = null;
        $stats = [
            'completed_activities' => 0,
            'total_points' => 0,
            'interests' => 0,
            'active_courses' => 0,
            'orders' => 0,
            'upcoming_live' => 0,
        ];

        if ($request->user()) {
            $child = $request->user()->childProfile;

            if ($child) {
                $child->load('interests');
                $courseIds = Enrollment::query()
                    ->where('child_profile_id', $child->id)
                    ->where('status', 'active')
                    ->pluck('learning_path_id');

                $stats = [
                    'completed_activities' => Progress::query()
                        ->where('child_profile_id', $child->id)
                        ->where('status', 'completed')
                        ->count(),
                    'total_points' => Progress::query()
                        ->where('child_profile_id', $child->id)
                        ->sum('points_awarded'),
                    'interests' => $child->interests->count(),
                    'active_courses' => $courseIds->count(),
                    'orders' => Order::query()->where('user_id', $request->user()->id)->count(),
                    'upcoming_live' => LiveSession::query()
                        ->whereIn('learning_path_id', $courseIds)
                        ->where('starts_at', '>=', now())
                        ->count(),
                ];
            }
        }

        return view('pages.parents', compact('child', 'stats'));
    }
}
