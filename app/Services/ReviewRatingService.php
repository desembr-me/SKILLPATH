<?php

namespace App\Services;

use App\Models\CourseReview;
use App\Models\LearningPath;
use App\Models\User;

class ReviewRatingService
{
    public function recalculateForCourse(LearningPath $course): void
    {
        if ($course->instructor_id) {
            $this->recalculateForInstructor((int) $course->instructor_id);
        }
    }

    public function recalculateForInstructor(int $instructorId): void
    {
        $instructor = User::query()
            ->whereKey($instructorId)
            ->where('role', 'instructor')
            ->with('instructorProfile')
            ->first();

        if (! $instructor?->instructorProfile) {
            return;
        }

        $courseIds = LearningPath::query()
            ->where('instructor_id', $instructorId)
            ->pluck('id');

        $reviews = CourseReview::query()
            ->where('is_approved', true)
            ->whereIn('learning_path_id', $courseIds)
            ->get(['mentor_rating', 'rating']);

        $rating = $reviews->isNotEmpty()
            ? round((float) $reviews->avg(fn (CourseReview $review) => (int) ($review->mentor_rating ?? $review->rating)), 2)
            : 0;

        $instructor->instructorProfile->update(['rating' => $rating]);
    }

    public function recalculateAll(): void
    {
        User::query()
            ->where('role', 'instructor')
            ->pluck('id')
            ->each(fn ($id) => $this->recalculateForInstructor((int) $id));
    }
}
