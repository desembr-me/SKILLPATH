<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseReview;
use App\Services\ReviewRatingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(['approved', 'pending'])],
            'source' => ['nullable', Rule::in(['mentor', 'platform', 'both', 'none'])],
        ]);

        $query = CourseReview::query()->with(['user', 'learningPath']);

        if (! empty($data['status'])) {
            $query->where('is_approved', $data['status'] === 'approved');
        }

        if (! empty($data['source'])) {
            match ($data['source']) {
                'mentor' => $query->where('mentor_rating', '<=', 2)->where('platform_rating', '>', 2),
                'platform' => $query->where('platform_rating', '<=', 2)->where('mentor_rating', '>', 2),
                'both' => $query->where('mentor_rating', '<=', 2)->where('platform_rating', '<=', 2),
                'none' => $query->where('mentor_rating', '>', 2)->where('platform_rating', '>', 2),
            };
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        // Statistik diagnostik menggunakan seluruh review, termasuk yang belum
        // dipublikasikan. Moderasi publik tidak boleh menutupi sumber masalah.
        $allReviews = CourseReview::query();
        $stats = [
            'mentor_average' => round((float) (clone $allReviews)->avg('mentor_rating'), 2),
            'platform_average' => round((float) (clone $allReviews)->avg('platform_rating'), 2),
            'mentor_issues' => (clone $allReviews)->where('mentor_rating', '<=', 2)->count(),
            'platform_issues' => (clone $allReviews)->where('platform_rating', '<=', 2)->count(),
            'pending' => (clone $allReviews)->where('is_approved', false)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function toggleApprove(CourseReview $courseReview, ReviewRatingService $ratingService)
    {
        $instructorId = $courseReview->learningPath?->instructor_id;
        $courseReview->update(['is_approved' => ! $courseReview->is_approved]);

        if ($instructorId) {
            $ratingService->recalculateForInstructor((int) $instructorId);
        }

        return back()->with('success', 'Status review dan rating mentor berhasil diperbarui.');
    }

    public function destroy(CourseReview $courseReview, ReviewRatingService $ratingService)
    {
        $instructorId = $courseReview->learningPath?->instructor_id;
        $courseReview->delete();

        if ($instructorId) {
            $ratingService->recalculateForInstructor((int) $instructorId);
        }

        return back()->with('success', 'Review dipindahkan ke Recycle Bin dan rating mentor dihitung ulang.');
    }
}
