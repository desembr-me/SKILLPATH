<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OnboardingController extends Controller
{
    public function edit(Request $request)
    {
        $interests = Interest::query()->orderBy('name')->get();
        $child = $request->user()->childProfile;
        $child?->load('interests');

        $learningNeeds = $this->learningNeeds();

        return view('onboarding.edit', compact('interests', 'child', 'learningNeeds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_name' => ['required', 'string', 'max:60'],
            'age' => ['required', 'integer', 'between:5,14'],
            'interests' => ['required', 'array', 'min:1', 'max:5'],
            'interests.*' => ['integer', 'distinct', 'exists:interests,id'],
            'favorite_interest_id' => ['required', 'integer', 'exists:interests,id'],
            'learning_need' => ['required', Rule::in(array_keys($this->learningNeeds()))],
            'child_voice' => ['required', 'string', 'min:10', 'max:500'],
            'co_design_confirmed' => ['accepted'],
        ]);

        $selectedInterestIds = array_map('intval', $data['interests']);
        if (! in_array((int) $data['favorite_interest_id'], $selectedInterestIds, true)) {
            throw ValidationException::withMessages([
                'favorite_interest_id' => 'Minat utama pilihan anak harus termasuk dalam minat yang dipilih.',
            ]);
        }

        DB::transaction(function () use ($request, $data, $selectedInterestIds) {
            $existingChild = $request->user()->childProfile;
            $recordedAt = now();

            $child = $request->user()->childProfile()->updateOrCreate(
                [],
                [
                    'name' => trim($data['child_name']),
                    'age' => $data['age'],
                    'avatar' => $existingChild?->avatar ?: 'spark',
                    'favorite_interest_id' => $data['favorite_interest_id'],
                    'learning_need' => $data['learning_need'],
                    'child_voice' => trim($data['child_voice']),
                    'co_design_completed_at' => $recordedAt,
                ]
            );

            $child->interests()->sync($selectedInterestIds);
            $child->coDesignSessions()->create([
                'selected_interest_ids' => array_values($selectedInterestIds),
                'favorite_interest_id' => $data['favorite_interest_id'],
                'learning_need' => $data['learning_need'],
                'child_voice' => trim($data['child_voice']),
                'recorded_at' => $recordedAt,
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil, suara anak, dan minat hasil co-design berhasil disimpan sebagai preferensi terbaru.');
    }

    private function learningNeeds(): array
    {
        return [
            'confidence' => 'Meningkatkan percaya diri',
            'communication' => 'Komunikasi dan kemampuan sosial',
            'creativity' => 'Kreativitas dan ekspresi diri',
            'independence' => 'Kemandirian dan kebiasaan positif',
            'problem_solving' => 'Problem solving dan berpikir kritis',
            'digital_literacy' => 'Literasi digital dan teknologi',
        ];
    }
}
