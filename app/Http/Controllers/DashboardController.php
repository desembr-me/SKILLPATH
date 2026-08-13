<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\SessionBooking;
use App\Services\AdaptiveLearningService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, AdaptiveLearningService $adaptive)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        $child->load('interests');
        $recommendations = $adaptive->recommend($child, 4);

        $registeredClasses = Enrollment::where('child_profile_id', $child->id)
            ->where('status', 'active')
            ->count();

        $upcomingBookings = SessionBooking::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'booked')
            ->whereHas('classSession', fn ($query) => $query
                ->where('status', 'scheduled')
                ->where('starts_at', '>=', now()))
            ->count();

        $attendedSessions = SessionBooking::where('child_profile_id', $child->id)
            ->where('status', 'attended')
            ->count();

        return view('dashboard', compact(
            'child', 'recommendations', 'registeredClasses', 'upcomingBookings', 'attendedSessions'
        ));
    }
}
