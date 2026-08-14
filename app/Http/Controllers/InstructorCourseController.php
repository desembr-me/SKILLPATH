<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinalExam;
use App\Models\Interest;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstructorCourseController extends Controller
{
    public function edit(Request $request, LearningPath $learningPath)
    {
        abort_unless($request->user()->role === 'instructor' && $learningPath->instructor_id === $request->user()->id, 403);
        $learningPath->load('modules.activities', 'finalExam', 'categories', 'interests');
        $categories = Category::orderedCore(Category::whereIn('slug', Category::coreSlugs())->get());
        $interests = Interest::query()->orderBy('name')->get();

        return view('instructor.courses.edit', compact('learningPath', 'categories', 'interests'));
    }

    public function update(Request $request, LearningPath $learningPath)
    {
        abort_unless($request->user()->role === 'instructor' && $learningPath->instructor_id === $request->user()->id, 403);
        $learningPath->load('finalExam');

        $rules = [
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'learning_outcomes' => ['nullable', 'string', 'max:3000'],
            'requirements' => ['nullable', 'string', 'max:3000'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'distinct', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'interest_ids' => ['required', 'array', 'min:1', 'max:8'],
            'interest_ids.*' => ['integer', 'distinct', 'exists:interests,id'],
        ];

        if ($learningPath->finalExam) {
            $rules += [
                'exam_title' => ['required', 'string', 'max:160'],
                'exam_passing_score' => ['required', 'integer', 'between:50,100'],
                'exam_max_attempts' => ['required', 'integer', 'between:1,10'],
                'exam_questions' => ['required', 'array', 'min:3', 'max:20'],
                'exam_questions.*.question' => ['required', 'string', 'max:500'],
                'exam_questions.*.options' => ['required', 'array', 'min:2', 'max:6'],
                'exam_questions.*.options.*' => ['required', 'string', 'max:300'],
                'exam_questions.*.correct' => ['required', 'integer', 'min:0', 'max:5'],
            ];
        }

        $data = $request->validate($rules);

        if (isset($data['sale_price']) && $data['sale_price'] !== null && (float) $data['sale_price'] > (float) $data['price']) {
            throw ValidationException::withMessages([
                'sale_price' => 'Harga promo tidak boleh lebih tinggi dari harga normal.',
            ]);
        }

        if ($learningPath->finalExam) {
            foreach ($data['exam_questions'] as $index => $question) {
                $options = array_values($question['options']);
                $correct = (int) $question['correct'];

                if (! array_key_exists($correct, $options)) {
                    throw ValidationException::withMessages([
                        "exam_questions.$index.correct" => 'Pilihan jawaban benar tidak valid.',
                    ]);
                }

                if (count(array_unique(array_map(fn ($option) => strtolower(trim($option)), $options))) !== count($options)) {
                    throw ValidationException::withMessages([
                        "exam_questions.$index.options" => 'Pilihan jawaban dalam satu soal tidak boleh duplikat.',
                    ]);
                }
            }

            $highestAttempt = (int) $learningPath->finalExam->attempts()->max('attempt_number');
            if ((int) $data['exam_max_attempts'] < $highestAttempt) {
                throw ValidationException::withMessages([
                    'exam_max_attempts' => 'Batas percobaan tidak boleh lebih kecil dari percobaan yang sudah digunakan siswa ('.$highestAttempt.').',
                ]);
            }
        }

        DB::transaction(function () use ($learningPath, $data) {
            $courseData = collect($data)->only([
                'price', 'sale_price', 'learning_outcomes', 'requirements',
            ])->all();
            $courseData['is_free'] = (float) $courseData['price'] === 0.0;
            $courseData['course_type'] = 'offline';
            $courseData['live_class_enabled'] = true;
            $learningPath->update($courseData);
            $learningPath->categories()->sync(array_map('intval', $data['category_ids']));
            $learningPath->interests()->sync(array_map('intval', $data['interest_ids']));

            if ($learningPath->finalExam) {
                $exam = FinalExam::query()
                    ->whereKey($learningPath->finalExam->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $highestAttempt = (int) $exam->attempts()->max('attempt_number');
                if ((int) $data['exam_max_attempts'] < $highestAttempt) {
                    throw ValidationException::withMessages([
                        'exam_max_attempts' => 'Batas percobaan tidak boleh lebih kecil dari percobaan yang sudah digunakan siswa ('.$highestAttempt.').',
                    ]);
                }

                $questions = collect($data['exam_questions'])
                    ->map(fn (array $question) => [
                        'question' => trim($question['question']),
                        'options' => array_values(array_map('trim', $question['options'])),
                        'correct' => (int) $question['correct'],
                    ])
                    ->values()
                    ->all();

                $exam->update([
                    'title' => trim($data['exam_title']),
                    'passing_score' => $data['exam_passing_score'],
                    'max_attempts' => $data['exam_max_attempts'],
                    'questions' => $questions,
                ]);
            }
        });

        return back()->with('success', 'Informasi course, soal, dan aturan ujian berhasil diperbarui. Perubahan soal berlaku untuk percobaan berikutnya.');
    }
}
