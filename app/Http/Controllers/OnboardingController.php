<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function edit(Request $request)
    {
        $interests = Interest::query()->orderBy('name')->get();
        $child = $request->user()->childProfile;

        return view('onboarding.edit', compact('interests', 'child'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_name' => ['required', 'string', 'max:60'],
            'age' => ['required', 'integer', 'between:5,14'],
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['integer', 'exists:interests,id'],
        ]);

        $child = $request->user()->childProfile()->updateOrCreate(
            [],
            [
                'name' => $data['child_name'],
                'age' => $data['age'],
                'avatar' => 'spark',
            ]
        );

        $child->interests()->sync($data['interests']);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil dan minat berhasil disimpan.');
    }
}
