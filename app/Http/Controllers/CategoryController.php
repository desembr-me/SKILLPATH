<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->withCount([
                'learningPaths' => fn ($query) => $query->where('is_published', true),
            ])
            ->orderBy('id')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $category->load([
            'learningPaths' => fn ($query) => $query
                ->where('is_published', true)
                ->with(['skill', 'instructor.instructorProfile', 'reviews'])
                ->withCount('enrollments')
                ->orderBy('title'),
        ]);

        return view('categories.show', compact('category'));
    }
}
