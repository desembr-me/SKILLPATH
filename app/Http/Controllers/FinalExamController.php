<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\ExamAttempt;
use App\Models\FinalExam;
use App\Models\LearningPath;
use App\Services\CertificateService;
use App\Services\FinalExamAttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinalExamController extends Controller
{
    public function show(
        Request $request,
        LearningPath $learningPath,
        CertificateService $certificateService,
        FinalExamAttemptService $examService
    ) {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);

        $this->ensureEnrollment($child->id, $learningPath->id);
        abort_unless($learningPath->certificate_enabled, 404);

        $learningPath->load('finalExam');
        abort_unless($learningPath->finalExam?->is_active, 404, 'Ujian akhir belum dikonfigurasi untuk course ini.');

        $evaluation = $certificateService->evaluate($child, $learningPath);
        if (! $evaluation['learning_complete']) {
            return redirect()->route('learning.path', $learningPath)->withErrors([
                'exam' => 'Selesaikan seluruh aktivitas course sebelum mengikuti ujian akhir.',
            ]);
        }

        $attempts = $learningPath->finalExam->attempts()
            ->where('child_profile_id', $child->id)
            ->orderByDesc('attempt_number')
            ->get();
        $certificate = Certificate::where('child_profile_id', $child->id)
            ->where('learning_path_id', $learningPath->id)
            ->first();

        $bestAttempt = $attempts->sortByDesc(fn ($attempt) => (float) $attempt->score)->first();
        $nextAttemptNumber = ((int) $attempts->max('attempt_number')) + 1;
        $examVersion = $examService->version($learningPath->finalExam);
        $examPresentation = (! $evaluation['exam_passed'] && $evaluation['attempts_remaining'] > 0)
            ? $examService->presentation($learningPath->finalExam, $child, $nextAttemptNumber)
            : [];

        return view('exams.show', compact(
            'learningPath',
            'child',
            'evaluation',
            'attempts',
            'bestAttempt',
            'certificate',
            'nextAttemptNumber',
            'examVersion',
            'examPresentation'
        ));
    }

    public function submit(
        Request $request,
        LearningPath $learningPath,
        CertificateService $certificateService,
        FinalExamAttemptService $examService
    ) {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        $this->ensureEnrollment($child->id, $learningPath->id);

        $data = $request->validate([
            'exam_version' => ['required', 'string', 'size:64'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'integer', 'min:0'],
        ]);

        $result = DB::transaction(function () use (
            $child,
            $learningPath,
            $data,
            $certificateService,
            $examService
        ) {
            $lockedChild = ChildProfile::query()->whereKey($child->id)->lockForUpdate()->firstOrFail();
            $this->ensureEnrollment($lockedChild->id, $learningPath->id);

            $exam = FinalExam::query()
                ->where('learning_path_id', $learningPath->id)
                ->lockForUpdate()
                ->first();

            abort_unless($learningPath->certificate_enabled && $exam?->is_active, 404);

            if (! hash_equals($examService->version($exam), $data['exam_version'])) {
                throw ValidationException::withMessages([
                    'exam' => 'Konfigurasi ujian berubah setelah halaman dibuka. Muat ulang ujian agar soal dan aturan terbaru digunakan.',
                ]);
            }

            $learningPath->setRelation('finalExam', $exam);
            $evaluation = $certificateService->evaluate($lockedChild, $learningPath);

            if (! $evaluation['learning_complete']) {
                throw ValidationException::withMessages([
                    'exam' => 'Seluruh aktivitas harus selesai sebelum ujian akhir.',
                ]);
            }

            if ($evaluation['exam_passed']) {
                $certificateService->issue($lockedChild, $learningPath);

                return ['state' => 'already_passed'];
            }

            $attemptsUsed = ExamAttempt::query()
                ->where('final_exam_id', $exam->id)
                ->where('child_profile_id', $lockedChild->id)
                ->count();

            if ($attemptsUsed >= (int) $exam->max_attempts) {
                throw ValidationException::withMessages([
                    'exam' => 'Batas percobaan ujian akhir sudah habis.',
                ]);
            }

            $attemptNumber = ((int) ExamAttempt::query()
                ->where('final_exam_id', $exam->id)
                ->where('child_profile_id', $lockedChild->id)
                ->max('attempt_number')) + 1;

            $grade = $examService->grade($exam, $lockedChild, $attemptNumber, $data['answers']);

            ExamAttempt::create([
                'final_exam_id' => $exam->id,
                'child_profile_id' => $lockedChild->id,
                'attempt_number' => $attemptNumber,
                'score' => $grade['score'],
                'passing_score_snapshot' => $exam->passing_score,
                'question_count' => $grade['question_count'],
                'correct_answers' => $grade['correct_answers'],
                'passed' => $grade['passed'],
                'answers' => $grade['answers'],
                'completed_at' => now(),
            ]);

            if ($grade['passed']) {
                $certificateService->issue($lockedChild, $learningPath);
            }

            return [
                'state' => $grade['passed'] ? 'passed' : 'failed',
                'score' => $grade['score'],
                'passing_score' => (int) $exam->passing_score,
                'correct_answers' => $grade['correct_answers'],
                'question_count' => $grade['question_count'],
            ];
        });

        if ($result['state'] === 'already_passed') {
            return redirect()->route('certificates.show', $learningPath)
                ->with('success', 'Ujian akhir sudah lulus dan sertifikat tersedia.');
        }

        if ($result['state'] === 'passed') {
            return redirect()->route('certificates.show', $learningPath)
                ->with('success', 'Selamat! Ujian akhir lulus dengan nilai '.number_format($result['score'], 0).'. Sertifikat berhasil diterbitkan.');
        }

        return redirect()->route('exams.show', $learningPath)->withErrors([
            'exam' => 'Nilai ujian '.number_format($result['score'], 0)
                .' ('.$result['correct_answers'].'/'.$result['question_count'].' benar) belum mencapai batas lulus '
                .$result['passing_score'].'. Retake masih tersedia selama batas percobaan belum tercapai.',
        ]);
    }

    private function ensureEnrollment(int $childId, int $learningPathId): void
    {
        abort_unless(
            Enrollment::where('child_profile_id', $childId)
                ->where('learning_path_id', $learningPathId)
                ->where('status', 'active')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->exists(),
            403
        );
    }
}
