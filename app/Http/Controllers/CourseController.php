<?php

namespace App\Http\Controllers;

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
            'reviews.user','classSessions.instructor','classSessions.bookings','questions.user','questions.answers.user',
        ]);

        $averageRating = round((float) $learningPath->reviews->avg('rating'), 1);
        $reviewCount = $learningPath->reviews->count();
        $studentCount = $learningPath->enrollments()->where('status', 'active')->count();
        $isEnrolled = false;
        $isWishlisted = false;

        if ($request->user()) {
            $child = $request->user()->childProfile;
            $isEnrolled = $child
                ? Enrollment::where('child_profile_id', $child->id)
                    ->where('learning_path_id', $learningPath->id)
                    ->where('status', 'active')
                    ->exists()
                : false;
            $isWishlisted = Wishlist::where('user_id', $request->user()->id)
                ->where('learning_path_id', $learningPath->id)
                ->exists();
        }

        return view('courses.show', compact(
            'learningPath','averageRating','reviewCount','studentCount','isEnrolled','isWishlisted'
        ));
    }

    public function enrollFree(Request $request, LearningPath $learningPath)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        abort_unless($learningPath->is_published && ($learningPath->is_free || $learningPath->effectivePrice() <= 0), 403);
        abort_unless(
            $child->age >= $learningPath->min_age && $child->age <= $learningPath->max_age,
            422,
            'Usia anak belum sesuai dengan kelas ini.'
        );

        Enrollment::updateOrCreate(
            ['child_profile_id'=>$child->id,'learning_path_id'=>$learningPath->id],
            ['status'=>'active','enrolled_at'=>now(),'expires_at'=>null]
        );

        return redirect()
            ->route('live.index', ['course' => $learningPath->id])
            ->with('success', 'Pendaftaran kelas gratis berhasil. Silakan pilih jadwal tatap muka.');
    }
}
