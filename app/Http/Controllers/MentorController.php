<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;

class MentorController extends Controller
{
    public function index()
    {
        $mentors = User::where('role', 'mentor')
            ->with(['category', 'courses'])
            ->get()
            ->map(function ($mentor) {
                $mentor->rating = round((float) Review::where('instructor_id', $mentor->id)->avg('mentor_rating'), 1);
                return $mentor;
            });

        return view('mentors.index', compact('mentors'));
    }

    public function show(User $mentor)
    {
        abort_unless($mentor->role === 'mentor', 404);

        $mentor->load(['category', 'courses.category']);
        $mentor->rating = round((float) Review::where('instructor_id', $mentor->id)->avg('mentor_rating'), 1);

        return view('mentors.show', compact('mentor'));
    }
}
