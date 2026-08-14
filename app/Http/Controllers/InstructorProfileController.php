<?php

namespace App\Http\Controllers;

use App\Models\InstructorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstructorProfileController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);

        $profile = $request->user()->instructorProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['is_verified' => false, 'rating' => 0, 'students_count' => 0]
        );

        return view('instructor.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);

        $profile = $request->user()->instructorProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['is_verified' => false, 'rating' => 0, 'students_count' => 0]
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'headline' => ['nullable', 'string', 'max:140'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'expertise' => ['nullable', 'string', 'max:180'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'education' => ['nullable', 'string', 'max:180'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $request->user()->update(['name' => $data['name']]);

        $profileData = collect($data)->only([
            'headline', 'bio', 'expertise', 'years_experience', 'education',
        ])->all();

        if ($request->boolean('remove_photo')) {
            $this->deleteLocalPhoto($profile->photo_url);
            $profileData['photo_url'] = null;
        }

        if ($request->hasFile('photo')) {
            $this->deleteLocalPhoto($profile->photo_url);

            $directory = public_path('uploads/instructors');
            File::ensureDirectoryExists($directory);

            $extension = strtolower($request->file('photo')->getClientOriginalExtension() ?: 'jpg');
            $filename = 'instructor-'.Str::uuid().'.'.$extension;
            $request->file('photo')->move($directory, $filename);
            $profileData['photo_url'] = 'uploads/instructors/'.$filename;
        }

        $profile->update($profileData);

        return back()->with('success', 'Profil pengajar berhasil diperbarui.');
    }

    private function deleteLocalPhoto(?string $photoUrl): void
    {
        if (! $photoUrl || Str::startsWith($photoUrl, ['http://', 'https://'])) {
            return;
        }

        $relative = ltrim($photoUrl, '/');
        if (! Str::startsWith($relative, 'uploads/instructors/')) {
            return;
        }

        File::delete(public_path($relative));
    }
}
