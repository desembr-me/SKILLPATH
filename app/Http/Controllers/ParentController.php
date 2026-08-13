<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Order;
use App\Models\SessionBooking;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function __invoke(Request $request)
    {
        $child = null;
        $stats = [
            'interests' => 0,
            'registered_classes' => 0,
            'upcoming_sessions' => 0,
            'attended_sessions' => 0,
            'orders' => 0,
        ];

        if ($request->user()) {
            $child = $request->user()->childProfile;

            if ($child) {
                $child->load('interests');

                $classIds = Enrollment::query()
                    ->where('child_profile_id', $child->id)
                    ->where('status', 'active')
                    ->pluck('learning_path_id');

                $stats = [
                    'interests' => $child->interests->count(),
                    'registered_classes' => $classIds->count(),
                    'upcoming_sessions' => SessionBooking::query()
                        ->where('child_profile_id', $child->id)
                        ->where('status', 'booked')
                        ->whereHas('classSession', fn ($query) => $query->where('starts_at', '>=', now()))
                        ->count(),
                    'attended_sessions' => SessionBooking::query()
                        ->where('child_profile_id', $child->id)
                        ->where('status', 'attended')
                        ->count(),
                    'orders' => Order::query()->where('user_id', $request->user()->id)->count(),
                ];
            }
        }

        return view('pages.parents', compact('child', 'stats'));
    }
}
