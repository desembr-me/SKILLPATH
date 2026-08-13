<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificateService
{
    public function evaluate(ChildProfile $child, LearningPath $course): array
    {
        $course->loadMissing('modules.activities', 'finalExam');

        $activityIds = $course->modules
            ->flatMap(fn ($module) => $module->activities->pluck('id'))
            ->values();

        $progress = Progress::query()
            ->where('child_profile_id', $child->id)
            ->whereIn('activity_id', $activityIds)
            ->where('status', 'completed')
            ->get();

        $totalActivities = $activityIds->count();
        $completedActivities = $progress->count();
        $learningComplete = $totalActivities > 0 && $completedActivities === $totalActivities;

        $exam = $course->finalExam;
        $attempts = $exam
            ? $exam->attempts()->where('child_profile_id', $child->id)->orderBy('attempt_number')->get()
            : collect();
        $passedAttempt = $attempts->where('passed', true)->sortByDesc('completed_at')->first();
        $attemptsUsed = $attempts->count();
        $maxAttempts = $exam?->max_attempts ?? 0;

        return [
            'eligible' => $course->certificate_enabled && $learningComplete && (bool) $passedAttempt,
            'learning_complete' => $learningComplete,
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'progress_percent' => $totalActivities > 0
                ? (int) round(($completedActivities / $totalActivities) * 100)
                : 0,
            'exam_configured' => (bool) ($exam?->is_active),
            'exam_passed' => (bool) $passedAttempt,
            'exam_score' => $passedAttempt ? (float) $passedAttempt->score : null,
            'attempts_used' => $attemptsUsed,
            'max_attempts' => $maxAttempts,
            'attempts_remaining' => max(0, $maxAttempts - $attemptsUsed),
            'final_score' => $passedAttempt ? (float) $passedAttempt->score : null,
        ];
    }

    public function issue(
        ChildProfile $child,
        LearningPath $course,
        ?User $issuer = null
    ): Certificate {
        $hasEnrollment = Enrollment::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $course->id)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();

        if (! $hasEnrollment) {
            throw ValidationException::withMessages([
                'certificate' => 'Siswa tidak memiliki enrollment aktif pada course ini.',
            ]);
        }

        if (! $course->certificate_enabled) {
            throw ValidationException::withMessages([
                'certificate' => 'Sertifikat tidak diaktifkan untuk course ini.',
            ]);
        }

        $evaluation = $this->evaluate($child, $course);

        if (! $evaluation['learning_complete']) {
            throw ValidationException::withMessages([
                'certificate' => 'Siswa belum menyelesaikan seluruh aktivitas course.',
            ]);
        }

        if (! $evaluation['exam_configured']) {
            throw ValidationException::withMessages([
                'certificate' => 'Ujian akhir untuk course ini belum dikonfigurasi.',
            ]);
        }

        if (! $evaluation['exam_passed']) {
            throw ValidationException::withMessages([
                'certificate' => 'Sertifikat hanya dapat diterbitkan setelah siswa lulus ujian akhir.',
            ]);
        }

        $existing = Certificate::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        return Certificate::create([
            'child_profile_id' => $child->id,
            'learning_path_id' => $course->id,
            'certificate_number' => $this->generateCertificateNumber(),
            'final_score' => $evaluation['final_score'],
            'issued_at' => now(),
            'status' => 'active',
            'issued_by' => $issuer?->id,
        ]);
    }

    private function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-SP-'
                .now()->format('Ym')
                .'-'
                .Str::upper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }
}
