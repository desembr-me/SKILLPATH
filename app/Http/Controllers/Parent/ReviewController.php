<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\PlatformReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store or update the single platform review for the authenticated parent.
     * Orang tua hanya dapat memberikan ulasan 1 kali untuk platform secara keseluruhan.
     */
    public function storePlatform(Request $request)
    {
        $data = $request->validate([
            'platform_rating' => 'required|integer|min:1|max:5',
            'platform_review' => 'nullable|string|max:1500',
        ]);

        $user = $request->user();

        $user->platformReview()->updateOrCreate(
            ['parent_id' => $user->id],
            [
                'rating' => $data['platform_rating'],
                'review' => $data['platform_review'] ?? null,
                'is_published' => true,
            ]
        );

        return back()->with('success', 'Ulasan platform berhasil disimpan.');
    }

    /**
     * Store or update mentor review for a specific course enrollment.
     * Ulasan mentor hanya dapat diberikan jika anak sudah mengikuti kelas mentor tersebut.
     */
    public function storeMentor(Request $request, Enrollment $enrollment)
    {
        $user = $request->user();

        // 1. Authorize parent ownership
        abort_unless($enrollment->parent_id === $user->id, 403, 'Akses ulasan tidak diizinkan.');

        // 2. Validate that the child has actually participated / enrolled in this class
        if (!in_array($enrollment->status, ['active', 'completed'])) {
            return back()->withErrors(['mentor_review' => 'Ulasan mentor hanya dapat diberikan jika anak sudah terdaftar dan mengikuti kelas mentor tersebut.']);
        }

        $data = $request->validate([
            'mentor_rating' => 'required|integer|min:1|max:5',
            'mentor_review' => 'nullable|string|max:1500',
        ]);

        $enrollment->review()->updateOrCreate(
            [],
            [
                'parent_id' => $user->id,
                'course_id' => $enrollment->course_id,
                'instructor_id' => $enrollment->course->instructor_id,
                'mentor_rating' => $data['mentor_rating'],
                'mentor_review' => $data['mentor_review'] ?? null,
                'is_published' => true,
            ]
        );

        return back()->with('success', 'Ulasan mentor untuk ' . $enrollment->course->title . ' berhasil disimpan.');
    }

    /**
     * Backward-compatible alias for existing review store route.
     */
    public function store(Request $request, Enrollment $enrollment)
    {
        return $this->storeMentor($request, $enrollment);
    }
}
