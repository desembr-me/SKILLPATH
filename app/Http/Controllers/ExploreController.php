<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', Rule::in(Category::coreSlugs())],
            'age' => ['nullable', 'integer', 'between:5,14'],
            'level' => ['nullable', Rule::in(LearningPath::LEVELS)],
            'sort' => ['nullable', 'in:newest,rating'],
        ]);

        $query = LearningPath::query()
            ->where('is_published', true)
            ->whereHas('categories', fn ($q) => $q->whereIn('slug', Category::coreSlugs()))
            ->with(['skill', 'categories', 'interests', 'instructor.instructorProfile', 'reviews'])
            ->withCount(['modules', 'enrollments']);

        if (! empty($validated['q'])) {
            $keyword = trim($validated['q']);
            $query->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhereHas('instructor', fn ($i) => $i->where('name', 'like', "%{$keyword}%")));
        }

        if (! empty($validated['category'])) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $validated['category']));
        }

        if (! empty($validated['age'])) {
            $age = (int) $validated['age'];
            $query->where('min_age', '<=', $age)->where('max_age', '>=', $age);
        }

        if (! empty($validated['level'])) {
            $query->where('level', $validated['level']);
        }

        $query->latest('published_at')->orderBy('title');
        $paths = $query->get();

        if (($validated['sort'] ?? 'newest') === 'rating') {
            $paths = $paths->sortByDesc(fn ($path) => $path->reviews->avg('rating') ?? 0)->values();
        }

        $categories = Category::orderedCore(
            Category::whereIn('slug', Category::coreSlugs())->get()
        );
        $levels = LearningPath::LEVELS;

        return view('explore.index', compact('paths', 'categories', 'levels'));
    }
}
