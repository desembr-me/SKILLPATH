<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LearningPath;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningPath::query()
            ->with(['instructor', 'skill', 'categories'])
            ->withCount('enrollments');

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($builder) => $builder->where('categories.slug', $request->category));
        }

        $courses = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    public function togglePublish(LearningPath $learningPath)
    {
        $learningPath->update([
            'is_published' => ! $learningPath->is_published,
            'published_at' => ! $learningPath->is_published ? now() : $learningPath->published_at,
        ]);

        return back()->with('success', 'Status publikasi kelas berhasil diperbarui.');
    }

    public function destroy(LearningPath $learningPath)
    {
        $learningPath->update(['is_published' => false]);
        $learningPath->delete();

        return back()->with('success', 'Kelas dipindahkan ke Recycle Bin.');
    }
}
