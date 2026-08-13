<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseReview;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseReview::query()->with(['user', 'learningPath']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApprove(CourseReview $courseReview)
    {
        $courseReview->update(['is_approved' => ! $courseReview->is_approved]);

        return back()->with('success', 'Status review berhasil diperbarui.');
    }

    public function destroy(CourseReview $courseReview)
    {
        $courseReview->delete();

        return back()->with('success', 'Review dipindahkan ke Recycle Bin.');
    }
}
