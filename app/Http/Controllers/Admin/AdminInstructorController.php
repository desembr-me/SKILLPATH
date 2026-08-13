<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminInstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'instructor')
            ->with('instructorProfile')
            ->withCount('coursesTaught');

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $instructors = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function toggleVerify(User $instructor)
    {
        abort_unless($instructor->role === 'instructor', 404);

        $profile = $instructor->instructorProfile;
        abort_unless($profile, 404);

        $profile->update(['is_verified' => ! $profile->is_verified]);

        return back()->with('success', 'Status verifikasi pengajar berhasil diperbarui.');
    }

    public function destroy(User $instructor)
    {
        abort_unless($instructor->role === 'instructor', 404);

        $instructor->coursesTaught()->update(['is_published' => false]);
        $instructor->delete();

        return back()->with('success', 'Akun pengajar dipindahkan ke Recycle Bin. Kelas miliknya otomatis dibuat draft.');
    }
}
