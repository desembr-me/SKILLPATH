<?php

namespace App\Http\Controllers;

use App\Models\CourseReview;
use App\Models\LearningPath;
use Illuminate\Http\Request;

class ReviewerDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'reviewer', 403);

        $stats = [
            'total_reviews' => CourseReview::count(),
            'approved_reviews' => CourseReview::where('is_approved', true)->count(),
            'pending_reviews' => CourseReview::where('is_approved', false)->count(),
            'courses' => LearningPath::count(),
        ];

        $reviews = CourseReview::query()
            ->with(['user', 'learningPath'])
            ->latest()
            ->take(8)
            ->get();

        return view('reviewer.dashboard', compact('stats', 'reviews'));
    }
}
