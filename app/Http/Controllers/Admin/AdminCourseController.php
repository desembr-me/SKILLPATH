<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LearningPath;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        if ($request->filled('level') && in_array($request->level, LearningPath::LEVELS, true)) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderedCore(Category::whereIn('slug', Category::coreSlugs())->get());
        $levels = LearningPath::LEVELS;

        return view('admin.courses.index', compact('courses', 'categories', 'levels'));
    }

    public function create()
    {
        return view('admin.courses.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $categoryId = (int) $data['category_id'];
        unset($data['category_id']);

        $data['slug'] = $this->uniqueSlug($data['title']);
        $data = $this->normalizeCourseData($request, $data);

        $course = LearningPath::create($data);
        $course->categories()->sync([$categoryId]);

        return redirect()->route('admin.courses.index')->with('success', 'Course offline berhasil ditambahkan.');
    }

    public function edit(LearningPath $learningPath)
    {
        $learningPath->load('categories');

        return view('admin.courses.edit', array_merge(
            $this->formOptions(),
            ['course' => $learningPath]
        ));
    }

    public function update(Request $request, LearningPath $learningPath)
    {
        $data = $this->validatedData($request, $learningPath);
        $categoryId = (int) $data['category_id'];
        unset($data['category_id']);

        if ($learningPath->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $learningPath->id);
        }

        $data = $this->normalizeCourseData($request, $data, $learningPath);
        $learningPath->update($data);
        $learningPath->categories()->sync([$categoryId]);

        return redirect()->route('admin.courses.index')->with('success', 'Course berhasil diperbarui.');
    }

    public function editImage(LearningPath $learningPath)
    {
        return view('admin.courses.image', ['course' => $learningPath]);
    }

    public function updateImage(Request $request, LearningPath $learningPath)
    {
        $request->validate([
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'thumbnail.required' => 'Pilih gambar course yang ingin digunakan.',
            'thumbnail.image' => 'File yang dipilih harus berupa gambar.',
            'thumbnail.mimes' => 'Gambar harus berformat JPG, JPEG, PNG, atau WebP.',
            'thumbnail.max' => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $learningPath->update([
            'thumbnail_url' => $this->storeThumbnail($request, $learningPath),
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Gambar course berhasil diganti.');
    }

    public function togglePublish(LearningPath $learningPath)
    {
        $nextState = ! $learningPath->is_published;
        $learningPath->update([
            'is_published' => $nextState,
            'published_at' => $nextState ? now() : $learningPath->published_at,
        ]);

        return back()->with('success', 'Status publikasi course berhasil diperbarui.');
    }

    public function destroy(LearningPath $learningPath)
    {
        $learningPath->update(['is_published' => false]);
        $learningPath->delete();

        return back()->with('success', 'Course dipindahkan ke Recycle Bin.');
    }

    private function formOptions(): array
    {
        return [
            'categories' => Category::orderedCore(Category::whereIn('slug', Category::coreSlugs())->get()),
            'levels' => LearningPath::LEVELS,
            'skills' => Skill::orderBy('name')->get(),
            'instructors' => User::where('role', 'instructor')->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request, ?LearningPath $course = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:3000'],
            'category_id' => [
                'required', 'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->whereIn('slug', Category::coreSlugs())->whereNull('deleted_at')),
            ],
            'level' => ['required', Rule::in(LearningPath::LEVELS)],
            'skill_id' => ['required', 'integer', 'exists:skills,id'],
            'instructor_id' => [
                'required', 'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'instructor')->whereNull('deleted_at')),
            ],
            'min_age' => ['required', 'integer', 'between:5,14'],
            'max_age' => ['required', 'integer', 'between:5,14', 'gte:min_age'],
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:10080'],
            'icon' => ['nullable', 'string', 'max:20'],
            'thumbnail' => [$course ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'learning_outcomes' => ['nullable', 'string', 'max:3000'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'is_free' => ['nullable', 'boolean'],
            'certificate_enabled' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    private function normalizeCourseData(Request $request, array $data, ?LearningPath $course = null): array
    {
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = $this->storeThumbnail($request, $course);
        } elseif ($course) {
            $data['thumbnail_url'] = $course->thumbnail_url;
        }

        unset($data['thumbnail']);

        $isFree = $request->boolean('is_free');
        $isPublished = $request->boolean('is_published');

        $data['icon'] = $data['icon'] ?: '✦';
        $data['is_free'] = $isFree;
        $data['price'] = $isFree ? 0 : (float) ($data['price'] ?? 0);
        $data['sale_price'] = $isFree ? null : ($data['sale_price'] ?? null);
        $data['course_type'] = 'offline';
        $data['live_class_enabled'] = true;
        $data['certificate_enabled'] = $request->boolean('certificate_enabled');
        $data['is_published'] = $isPublished;
        $data['published_at'] = $isPublished
            ? ($course?->published_at ?? now())
            : $course?->published_at;
        $data['access_days'] = null;
        $data['promo_video_url'] = null;

        return $data;
    }

    private function storeThumbnail(Request $request, ?LearningPath $course = null): string
    {
        if ($course?->thumbnail_url && str_starts_with($course->thumbnail_url, 'uploads/course-thumbnails/')) {
            $oldFile = public_path($course->thumbnail_url);
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $uploadDirectory = public_path('uploads/course-thumbnails');
        if (! is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $file = $request->file('thumbnail');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::uuid().'.'.$extension;
        $file->move($uploadDirectory, $fileName);

        return 'uploads/course-thumbnails/'.$fileName;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $counter = 2;

        while (LearningPath::withTrashed()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
