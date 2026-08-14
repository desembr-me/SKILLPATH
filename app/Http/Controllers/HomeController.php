<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LearningPath;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderedCore(
            Category::query()
                ->whereIn('slug', Category::coreSlugs())
                ->withCount(['learningPaths' => fn ($q) => $q->where('is_published', true)])
                ->get()
        );

        $featuredPaths = LearningPath::query()
            ->where('is_published', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('slug', Category::coreSlugs()))
            ->with(['skill', 'categories', 'instructor.instructorProfile', 'reviews'])
            ->withCount('enrollments')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $featuredInstructors = User::query()
            ->where('role', 'instructor')
            ->with('instructorProfile')
            ->withCount(['coursesTaught' => fn ($q) => $q->where('is_published', true)])
            ->orderByDesc('id')
            ->take(3)
            ->get();

        return view('home', compact('categories', 'featuredPaths', 'featuredInstructors'));
    }
}
