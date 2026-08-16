<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');

        $query = Course::with(['category', 'instructor'])->latest();

        if ($status && in_array($status, ['active', 'draft'])) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'currentStatus' => $status ?: 'all',
            'currentCategory' => $categoryId,
            'search' => $search,
            'totalCount' => Course::count(),
            'activeCount' => Course::where('status', 'active')->count(),
            'draftCount' => Course::where('status', 'draft')->count(),
        ]);
    }

    public function create()
    {
        return view('admin.courses.create', [
            'categories' => Category::all(),
            'instructors' => User::where('role', 'mentor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'instructor_id' => ['required', 'exists:users,id'],
            'age_min' => ['required', 'integer', 'min:3', 'max:18'],
            'age_max' => ['required', 'integer', 'gte:age_min', 'max:18'],
            'city' => ['required', 'string', 'max:100'],
            'location_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'sessions_count' => ['required', 'integer', 'min:1', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
            'status' => ['required', 'in:active,draft'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course baru berhasil dipublikasikan.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => Category::all(),
            'instructors' => User::where('role', 'mentor')->get(),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'instructor_id' => ['required', 'exists:users,id'],
            'age_min' => ['required', 'integer', 'min:3', 'max:18'],
            'age_max' => ['required', 'integer', 'gte:age_min', 'max:18'],
            'city' => ['required', 'string', 'max:100'],
            'location_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'sessions_count' => ['required', 'integer', 'min:1', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
            'status' => ['required', 'in:active,draft'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Data course berhasil diperbarui.');
    }

    public function toggleStatus(Course $course)
    {
        $newStatus = $course->status === 'active' ? 'draft' : 'active';
        $course->update(['status' => $newStatus]);

        return back()->with('success', "Status course diubah menjadi {$newStatus}.");
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Course berhasil dihapus.');
    }
}
