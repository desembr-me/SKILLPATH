<?php

namespace App\Http\Controllers;

use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show(Request $request, LearningPath $learningPath)
    {
        abort_unless($learningPath->is_published, 404);

        $learningPath->load([
            'skill','categories','interests','instructor.instructorProfile','modules.activities',
            'reviews.user','liveSessions.instructor','questions.user','questions.answers.user',
        ]);

        $averageRating = round((float) $learningPath->reviews->avg(fn ($review) => $review->mentor_rating ?? $review->rating), 1);
        $platformRating = round((float) $learningPath->reviews->avg(fn ($review) => $review->platform_rating ?? $review->rating), 1);
        $reviewCount = $learningPath->reviews->count();
        $studentCount = $learningPath->enrollments()->where('status', 'active')->count();
        $isEnrolled = false;
        $canReview = false;
        $isWishlisted = false;
        $myReview = null;

        if ($request->user()) {
            $child = $request->user()->childProfile;
            if ($child) {
                $isEnrolled = Enrollment::query()
                    ->where('child_profile_id', $child->id)
                    ->where('learning_path_id', $learningPath->id)
                    ->where('status', 'active')
                    ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->exists();

                // Alumni tetap boleh memberi ulasan walau masa akses course berakhir.
                $canReview = Enrollment::query()
                    ->where('child_profile_id', $child->id)
                    ->where('learning_path_id', $learningPath->id)
                    ->exists();
            }

            $isWishlisted = Wishlist::where('user_id', $request->user()->id)
                ->where('learning_path_id', $learningPath->id)
                ->exists();

            $myReview = CourseReview::query()
                ->where('user_id', $request->user()->id)
                ->where('learning_path_id', $learningPath->id)
                ->first();
        }

        return view('courses.show', compact(
            'learningPath','averageRating','platformRating','reviewCount','studentCount',
            'isEnrolled','canReview','isWishlisted','myReview'
        ));
    }

    public function enrollFree(Request $request, LearningPath $learningPath)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        abort_unless($learningPath->is_published && ($learningPath->is_free || $learningPath->effectivePrice() <= 0), 403);
        abort_unless($child->age >= $learningPath->min_age && $child->age <= $learningPath->max_age, 422, 'Usia anak belum sesuai dengan course ini.');

        Enrollment::updateOrCreate(
            ['child_profile_id'=>$child->id,'learning_path_id'=>$learningPath->id],
            ['status'=>'active','enrolled_at'=>now(),'expires_at'=>$learningPath->access_days ? now()->addDays($learningPath->access_days) : null]
        );

        return redirect()->route('learning.path', $learningPath)->with('success', 'Course gratis berhasil diaktifkan.');
    }
}
