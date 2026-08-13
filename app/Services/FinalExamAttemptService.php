<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\FinalExam;
use Illuminate\Validation\ValidationException;

class FinalExamAttemptService
{
    public function version(FinalExam $exam): string
    {
        return hash('sha256', json_encode([
            'questions' => $exam->questions,
            'passing_score' => (int) $exam->passing_score,
            'max_attempts' => (int) $exam->max_attempts,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function presentation(FinalExam $exam, ChildProfile $child, int $attemptNumber): array
    {
        $questions = collect($exam->questions ?? [])
            ->values()
            ->map(function (array $question, int $sourceQuestionIndex) use ($exam, $child, $attemptNumber) {
                $options = collect($question['options'] ?? [])
                    ->values()
                    ->map(fn ($text, int $sourceOptionIndex) => [
                        'source_index' => $sourceOptionIndex,
                        'text' => (string) $text,
                    ])
                    ->sortBy(fn (array $option) => $this->shuffleKey(
                        $exam,
                        $child,
                        $attemptNumber,
                        'option:'.$sourceQuestionIndex.':'.$option['source_index']
                    ))
                    ->values()
                    ->all();

                return [
                    'source_index' => $sourceQuestionIndex,
                    'question' => (string) ($question['question'] ?? ''),
                    'options' => $options,
                ];
            })
            ->sortBy(fn (array $question) => $this->shuffleKey(
                $exam,
                $child,
                $attemptNumber,
                'question:'.$question['source_index']
            ))
            ->values()
            ->all();

        return $questions;
    }

    public function grade(
        FinalExam $exam,
        ChildProfile $child,
        int $attemptNumber,
        array $submittedAnswers
    ): array {
        $presentation = $this->presentation($exam, $child, $attemptNumber);

        if (count($presentation) === 0) {
            throw ValidationException::withMessages([
                'exam' => 'Ujian akhir belum memiliki soal yang dapat dinilai.',
            ]);
        }

        if (count($submittedAnswers) !== count($presentation)) {
            throw ValidationException::withMessages([
                'answers' => 'Seluruh pertanyaan ujian harus dijawab.',
            ]);
        }

        $sourceQuestions = collect($exam->questions ?? [])->values();
        $correct = 0;
        $normalizedAnswers = [];

        foreach ($presentation as $displayQuestionIndex => $displayQuestion) {
            if (! array_key_exists($displayQuestionIndex, $submittedAnswers)) {
                throw ValidationException::withMessages([
                    'answers' => 'Seluruh pertanyaan ujian harus dijawab.',
                ]);
            }

            $selectedDisplayOption = filter_var(
                $submittedAnswers[$displayQuestionIndex],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0]]
            );

            if ($selectedDisplayOption === false || ! isset($displayQuestion['options'][$selectedDisplayOption])) {
                throw ValidationException::withMessages([
                    'answers' => 'Jawaban ujian tidak valid. Muat ulang halaman dan coba kembali.',
                ]);
            }

            $sourceQuestionIndex = (int) $displayQuestion['source_index'];
            $sourceOptionIndex = (int) $displayQuestion['options'][$selectedDisplayOption]['source_index'];
            $sourceQuestion = $sourceQuestions->get($sourceQuestionIndex, []);

            if ($sourceOptionIndex === (int) ($sourceQuestion['correct'] ?? -1)) {
                $correct++;
            }

            $normalizedAnswers[] = [
                'question_index' => $sourceQuestionIndex,
                'selected_option_index' => $sourceOptionIndex,
            ];
        }

        $questionCount = count($presentation);
        $score = round(($correct / $questionCount) * 100, 2);

        return [
            'score' => $score,
            'passed' => $score >= (int) $exam->passing_score,
            'question_count' => $questionCount,
            'correct_answers' => $correct,
            'answers' => $normalizedAnswers,
        ];
    }

    private function shuffleKey(
        FinalExam $exam,
        ChildProfile $child,
        int $attemptNumber,
        string $item
    ): string {
        $secret = (string) config('app.key', 'skillpath-exam');
        $seed = $exam->id.'|'.$child->id.'|'.$attemptNumber;

        return hash_hmac('sha256', $seed.'|'.$item, $secret);
    }
}
