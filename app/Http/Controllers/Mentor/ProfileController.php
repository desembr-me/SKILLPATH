<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('category');
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $seededAvatars = ['naya.png', 'dimas.png', 'sari.png', 'bimo.png', 'clara.png', 'fajar.png'];
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && !in_array(basename($user->avatar), $seededAvatars) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar && !in_array(basename($user->avatar), $seededAvatars) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars/mentors', 'public');
        } else {
            unset($data['avatar']);
        }

        unset($data['remove_avatar']);
        $user->update($data);

        return back()->with('success', 'Profil dan foto pengajar berhasil diperbarui.');
    }
}
