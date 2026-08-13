<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CourseReview;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminRecycleBinController extends Controller
{
    private const TYPES = ['course', 'category', 'user', 'review'];

    public function index(Request $request)
    {
        $data = $request->validate([
            'type' => ['nullable', Rule::in(['all', ...self::TYPES])],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $type = $data['type'] ?? 'all';
        $q = trim((string) ($data['q'] ?? ''));

        $counts = [
            'course' => LearningPath::onlyTrashed()->count(),
            'category' => Category::onlyTrashed()->count(),
            'user' => User::onlyTrashed()->count(),
            'review' => CourseReview::onlyTrashed()->count(),
        ];

        $items = collect();

        foreach (self::TYPES as $itemType) {
            if ($type !== 'all' && $type !== $itemType) {
                continue;
            }

            $items = $items->concat($this->itemsFor($itemType, $q));
        }

        $items = $items
            ->sortByDesc(fn (array $item) => $item['deleted_at']?->getTimestamp() ?? 0)
            ->values();

        return view('admin.recycle-bin.index', compact('items', 'counts', 'type', 'q'));
    }

    public function restore(string $type, int $id)
    {
        $model = $this->findTrashed($type, $id);
        $model->restore();

        return back()->with('success', $this->label($type).' berhasil dipulihkan dari Recycle Bin.');
    }

    public function restoreAll()
    {
        LearningPath::onlyTrashed()->restore();
        Category::onlyTrashed()->restore();
        User::onlyTrashed()->restore();
        CourseReview::onlyTrashed()->restore();

        return back()->with('success', 'Semua data di Recycle Bin berhasil dipulihkan. Kelas yang dipulihkan tetap berstatus draft sampai dipublikasikan kembali oleh admin.');
    }

    public function forceDelete(string $type, int $id)
    {
        $model = $this->findTrashed($type, $id);

        $blockedReason = $this->permanentDeleteBlockReason($type, $id);
        if ($blockedReason) {
            return back()->withErrors(['recycle_bin' => $blockedReason]);
        }

        $model->forceDelete();

        return back()->with('success', $this->label($type).' dihapus permanen.');
    }

    private function itemsFor(string $type, string $q): Collection
    {
        return match ($type) {
            'course' => LearningPath::onlyTrashed()
                ->when($q !== '', fn ($query) => $query->where(function ($builder) use ($q) {
                    $builder->where('title', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                }))
                ->latest('deleted_at')
                ->get()
                ->map(fn (LearningPath $course) => [
                    'type' => 'course',
                    'id' => $course->id,
                    'name' => $course->title,
                    'detail' => 'Kelas · usia '.$course->min_age.'–'.$course->max_age.' tahun',
                    'deleted_at' => $course->deleted_at,
                ]),

            'category' => Category::onlyTrashed()
                ->when($q !== '', fn ($query) => $query->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                }))
                ->latest('deleted_at')
                ->get()
                ->map(fn (Category $category) => [
                    'type' => 'category',
                    'id' => $category->id,
                    'name' => $category->name,
                    'detail' => 'Kategori · '.$category->slug,
                    'deleted_at' => $category->deleted_at,
                ]),

            'user' => User::onlyTrashed()
                ->when($q !== '', fn ($query) => $query->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                }))
                ->latest('deleted_at')
                ->get()
                ->map(fn (User $user) => [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'detail' => strtoupper($user->role).' · '.$user->email,
                    'deleted_at' => $user->deleted_at,
                ]),

            'review' => CourseReview::onlyTrashed()
                ->with(['user', 'learningPath'])
                ->when($q !== '', fn ($query) => $query->where('review', 'like', "%{$q}%"))
                ->latest('deleted_at')
                ->get()
                ->map(fn (CourseReview $review) => [
                    'type' => 'review',
                    'id' => $review->id,
                    'name' => 'Review '.$review->rating.' bintang',
                    'detail' => ($review->user?->name ?? 'Pengguna tidak aktif').' · '.($review->learningPath?->title ?? 'Kelas tidak aktif'),
                    'deleted_at' => $review->deleted_at,
                ]),
        };
    }

    private function findTrashed(string $type, int $id): Model
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        return match ($type) {
            'course' => LearningPath::onlyTrashed()->findOrFail($id),
            'category' => Category::onlyTrashed()->findOrFail($id),
            'user' => User::onlyTrashed()->findOrFail($id),
            'review' => CourseReview::onlyTrashed()->findOrFail($id),
        };
    }

    private function label(string $type): string
    {
        return match ($type) {
            'course' => 'Kelas',
            'category' => 'Kategori',
            'user' => 'Pengguna',
            'review' => 'Review',
            default => 'Data',
        };
    }

    private function permanentDeleteBlockReason(string $type, int $id): ?string
    {
        if ($type === 'course') {
            $hasTransactions = DB::table('order_items')->where('learning_path_id', $id)->exists();
            $hasEnrollments = DB::table('enrollments')->where('learning_path_id', $id)->exists();

            if ($hasTransactions || $hasEnrollments) {
                return 'Kelas tidak dapat dihapus permanen karena memiliki riwayat transaksi atau pendaftaran. Pulihkan kelas jika masih diperlukan.';
            }
        }

        if ($type === 'user') {
            $hasOrders = DB::table('orders')->where('user_id', $id)->exists();
            $hasCourses = DB::table('learning_paths')->where('instructor_id', $id)->exists();
            $childId = DB::table('child_profiles')->where('user_id', $id)->value('id');
            $hasEnrollments = $childId
                ? DB::table('enrollments')->where('child_profile_id', $childId)->exists()
                : false;

            if ($hasOrders || $hasCourses || $hasEnrollments) {
                return 'Pengguna tidak dapat dihapus permanen karena memiliki riwayat kelas, transaksi, atau pendaftaran.';
            }
        }

        return null;
    }
}
