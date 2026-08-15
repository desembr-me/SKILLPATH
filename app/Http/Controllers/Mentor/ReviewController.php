<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['parent', 'course', 'enrollment.child'])
            ->where('instructor_id', $request->user()->id)
            ->latest()
            ->get();

        return view('mentor.reviews', [
            'reviews' => $reviews,
            'avgMentorRating' => round((float) $reviews->avg('mentor_rating'), 1),
            'avgPlatformRating' => round((float) $reviews->avg('platform_rating'), 1),
        ]);
    }
}
