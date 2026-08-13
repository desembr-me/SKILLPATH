<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\LearningPath;
use App\Models\Skill;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable','string','max:80'],
            'category' => ['nullable','string','max:80'],
            'age' => ['nullable','integer','between:5,14'],
            'skill' => ['nullable','string','max:100'],
            'class_type' => ['nullable','in:regular,workshop,private'],
            'location' => ['nullable','string','max:180'],
            'sort' => ['nullable','in:newest,rating'],
        ]);

        $query = LearningPath::query()
            ->where('is_published', true)
            ->with(['skill','categories','interests','instructor.instructorProfile','reviews'])
            ->withCount(['enrollments','classSessions']);

        if (! empty($validated['q'])) {
            $keyword = trim($validated['q']);
            $query->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('venue_summary', 'like', "%{$keyword}%")
                ->orWhereHas('instructor', fn ($instructor) => $instructor->where('name', 'like', "%{$keyword}%"))
            );
        }

        if (! empty($validated['category'])) {
            $query->whereHas('categories', fn ($category) => $category->where('slug', $validated['category']));
        }

        if (! empty($validated['age'])) {
            $age = (int) $validated['age'];
            $query->where('min_age', '<=', $age)->where('max_age', '>=', $age);
        }

        if (! empty($validated['skill'])) {
            $query->whereHas('skill', fn ($skill) => $skill->where('slug', $validated['skill']));
        }

        if (! empty($validated['class_type'])) {
            $query->where('class_type', $validated['class_type']);
        }

        if (! empty($validated['location'])) {
            $query->where('venue_summary', $validated['location']);
        }

        $query->latest('published_at')->orderBy('title');
        $paths = $query->get();

        if (($validated['sort'] ?? 'newest') === 'rating') {
            $paths = $paths->sortByDesc(fn ($path) => $path->reviews->avg('rating') ?? 0)->values();
        }

        $categories = Category::orderBy('id')->get();
        $skills = Skill::orderBy('name')->get();
        $locations = LearningPath::query()
            ->where('is_published', true)
            ->whereNotNull('venue_summary')
            ->where('venue_summary', '!=', '')
            ->distinct()
            ->orderBy('venue_summary')
            ->pluck('venue_summary');

        return view('explore.index', compact('paths','categories','skills','locations'));
    }
}
