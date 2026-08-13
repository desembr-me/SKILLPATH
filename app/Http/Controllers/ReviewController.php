<?php

namespace App\Http\Controllers;

use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Services\ReviewRatingService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, LearningPath $learningPath, ReviewRatingService $ratingService)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        abort_unless(
            Enrollment::where('child_profile_id', $child->id)
                ->where('learning_path_id', $learningPath->id)
                ->exists(),
            403
        );

        $data = $request->validate([
            'mentor_rating' => ['required', 'integer', 'between:1,5'],
            'platform_rating' => ['required', 'integer', 'between:1,5'],
            'mentor_review' => ['nullable', 'string', 'max:1000'],
            'platform_review' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = CourseReview::withTrashed()
            ->firstOrNew([
                'user_id' => $request->user()->id,
                'learning_path_id' => $learningPath->id,
            ]);

        if ($review->trashed()) {
            $review->restore();
        }

        $review->fill($data + [
            'rating' => $data['mentor_rating'],
            'review' => $data['mentor_review'] ?? null,
            // Review baru maupun yang diedit masuk moderasi ulang supaya
            // perubahan isi tidak langsung tampil ke publik.
            'is_approved' => false,
        ])->save();

        $ratingService->recalculateForCourse($learningPath);

        return back()->with('success', 'Ulasan mentor dan platform berhasil disimpan dan menunggu moderasi Admin.');
    }
}
