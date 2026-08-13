<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with(['childProfile', 'instructorProfile']);

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'Anda tidak dapat memindahkan akun admin yang sedang digunakan ke Recycle Bin.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'Minimal satu akun admin aktif harus tetap tersedia.']);
        }

        if ($user->role === 'instructor') {
            $user->coursesTaught()->update(['is_published' => false]);
        }

        $user->delete();

        return back()->with('success', 'Pengguna dipindahkan ke Recycle Bin.');
    }
}
