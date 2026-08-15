<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $courses = $user->courses()->with('category')->get();

        return view('mentor.profile', [
            'user' => $user,
            'courses' => $courses,
            'students' => $user->courses()->withCount(['enrollments' => fn($q) => $q->where('status', 'active')])->get()->sum('enrollments_count'),
            'rating' => round((float) Review::where('instructor_id', $user->id)->avg('mentor_rating'), 1),
            'reviewCount' => Review::where('instructor_id', $user->id)->count(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'headline' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/mentors', 'public');
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return back()->with('success', 'Profil pengajar berhasil diperbarui.');
    }
}
