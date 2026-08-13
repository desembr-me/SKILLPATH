<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CertificateService
{
    public function evaluate(ChildProfile $child, LearningPath $course): array
    {
        $course->loadMissing('modules.activities');

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

        return [
            'eligible' => $course->certificate_enabled
                && $totalActivities > 0
                && $completedActivities === $totalActivities,
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'progress_percent' => $totalActivities > 0
                ? (int) round(($completedActivities / $totalActivities) * 100)
                : 0,
            'final_score' => $progress->whereNotNull('score')->avg('score'),
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

        if (! $evaluation['eligible']) {
            throw ValidationException::withMessages([
                'certificate' => 'Siswa belum menyelesaikan seluruh aktivitas course.',
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
