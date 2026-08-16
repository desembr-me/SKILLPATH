<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $rating = $request->query('rating');

        $query = Review::with(['parent', 'enrollment.course.instructor', 'enrollment.child'])->latest();

        if ($rating && in_array($rating, [5, 4, 3, 2, 1])) {
            $query->where('mentor_rating', $rating);
        }

        $reviews = $query->paginate(10)->withQueryString();

        $avgMentor = Review::avg('mentor_rating');
        $avgPlatform = Review::avg('platform_rating');

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'currentRating' => $rating ?: 'all',
            'totalCount' => Review::count(),
            'avgMentor' => $avgMentor ? number_format($avgMentor, 2) : '5.00',
            'avgPlatform' => $avgPlatform ? number_format($avgPlatform, 2) : '5.00',
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
