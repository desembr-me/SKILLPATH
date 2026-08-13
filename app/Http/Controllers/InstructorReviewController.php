<?php

namespace App\Http\Controllers;

use App\Models\CourseReview;
use Illuminate\Http\Request;

class InstructorReviewController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);

        $query = CourseReview::query()
            ->whereHas('learningPath', fn ($q) => $q->where('instructor_id', $request->user()->id))
            ->with(['user', 'learningPath']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('instructor.reviews.index', compact('reviews'));
    }

    public function toggleApprove(Request $request, CourseReview $courseReview)
    {
        abort_unless(
            $request->user()->role === 'instructor' && $courseReview->learningPath->instructor_id === $request->user()->id,
            403,
        );

        $courseReview->update(['is_approved' => ! $courseReview->is_approved]);

        return back()->with('success', 'Status ulasan kursus berhasil diperbarui.');
    }
}
