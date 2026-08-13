<?php

namespace Tests\Unit;

use App\Models\ChildProfile;
use App\Models\FinalExam;
use App\Services\FinalExamAttemptService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FinalExamAttemptServiceTest extends TestCase
{
    public function test_exam_presentation_is_deterministic_and_can_be_graded_after_shuffle(): void
    {
        config(['app.key' => 'testing-secret']);
        $service = new FinalExamAttemptService();
        $exam = $this->exam();
        $child = $this->child();

        $first = $service->presentation($exam, $child, 1);
        $second = $service->presentation($exam, $child, 1);

        $this->assertSame($first, $second);

        $answers = [];
        foreach ($first as $questionIndex => $question) {
            $sourceQuestion = $exam->questions[$question['source_index']];
            foreach ($question['options'] as $displayOptionIndex => $option) {
                if ($option['source_index'] === $sourceQuestion['correct']) {
                    $answers[$questionIndex] = $displayOptionIndex;
                    break;
                }
            }
        }

        $grade = $service->grade($exam, $child, 1, $answers);

        $this->assertSame(100.0, $grade['score']);
        $this->assertTrue($grade['passed']);
        $this->assertSame(3, $grade['correct_answers']);
    }

    public function test_invalid_display_option_is_rejected(): void
    {
        config(['app.key' => 'testing-secret']);
        $service = new FinalExamAttemptService();

        $this->expectException(ValidationException::class);
        $service->grade($this->exam(), $this->child(), 1, [99, 99, 99]);
    }

    private function exam(): FinalExam
    {
        $exam = new FinalExam([
            'passing_score' => 75,
            'max_attempts' => 3,
            'questions' => [
                ['question' => 'Q1', 'options' => ['A', 'B', 'C', 'D'], 'correct' => 0],
                ['question' => 'Q2', 'options' => ['A', 'B', 'C', 'D'], 'correct' => 1],
                ['question' => 'Q3', 'options' => ['A', 'B', 'C', 'D'], 'correct' => 2],
            ],
        ]);
        $exam->id = 10;

        return $exam;
    }

    private function child(): ChildProfile
    {
        $child = new ChildProfile(['name' => 'Test', 'age' => 10]);
        $child->id = 20;

        return $child;
    }
}
