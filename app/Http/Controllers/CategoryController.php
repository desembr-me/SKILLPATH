<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LearningPath;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderedCore(
            Category::query()
                ->whereIn('slug', Category::coreSlugs())
                ->withCount(['learningPaths' => fn ($query) => $query->where('is_published', true)])
                ->get()
        );

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        abort_unless(in_array($category->slug, Category::coreSlugs(), true), 404);

        $category->load([
            'learningPaths' => fn ($query) => $query
                ->where('is_published', true)
                ->with(['skill', 'instructor.instructorProfile', 'reviews'])
                ->withCount('enrollments')
                ->orderBy('level')
                ->orderBy('title'),
        ]);

        $levels = collect(LearningPath::LEVELS)->mapWithKeys(fn ($level) => [
            $level => $category->learningPaths->where('level', $level)->values(),
        ]);

        return view('categories.show', compact('category', 'levels'));
    }
}
