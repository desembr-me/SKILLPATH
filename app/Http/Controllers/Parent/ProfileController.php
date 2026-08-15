<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('children');

        return view('parent.profile', [
            'user' => $user,
            'children' => $user->children,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/parents', 'public');
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return back()->with('success', 'Profil orang tua berhasil diperbarui.');
    }

    public function storeChild(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'interests' => ['nullable', 'array', 'max:3'],
            'interests.*' => ['string', 'max:50'],
            'learning_preferences' => ['nullable', 'array'],
            'learning_preferences.*' => ['string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['parent_id'] = $request->user()->id;
        $data['interests'] = $request->input('interests', []);
        $data['learning_preferences'] = $request->input('learning_preferences', []);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/children', 'public');
        }

        Child::create($data);

        return back()->with('success', 'Profil anak berhasil ditambahkan.');
    }

    public function updateChild(Request $request, Child $child)
    {
        abort_unless($child->parent_id === $request->user()->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'interests' => ['nullable', 'array', 'max:3'],
            'interests.*' => ['string', 'max:50'],
            'learning_preferences' => ['nullable', 'array'],
            'learning_preferences.*' => ['string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['interests'] = $request->input('interests', []);
        $data['learning_preferences'] = $request->input('learning_preferences', []);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars/children', 'public');
        } else {
            unset($data['avatar']);
        }

        $child->update($data);

        return back()->with('success', 'Profil anak berhasil diperbarui.');
    }
}
