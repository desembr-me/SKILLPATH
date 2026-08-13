<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\SessionBooking;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificateService
{
    public function evaluate(ChildProfile $child, LearningPath $course): array
    {
        $course->loadMissing('classSessions');

        $requiredSessions = $course->classSessions
            ->where('status', '!=', 'cancelled')
            ->values();
        $requiredSessionIds = $requiredSessions->pluck('id');

        $attendedCount = SessionBooking::query()
            ->where('child_profile_id', $child->id)
            ->whereIn('class_session_id', $requiredSessionIds)
            ->where('status', 'attended')
            ->count();

        $pendingSessions = $requiredSessions
            ->filter(fn ($session) => $session->status !== 'completed')
            ->count();

        $totalSessions = $requiredSessions->count();
        $attendanceRate = $totalSessions > 0
            ? round(($attendedCount / $totalSessions) * 100, 1)
            : 0;

        return [
            'eligible' => $course->certificate_enabled
                && $totalSessions > 0
                && $pendingSessions === 0
                && $attendedCount === $totalSessions,
            'total_sessions' => $totalSessions,
            'attended_sessions' => $attendedCount,
            'pending_sessions' => $pendingSessions,
            'attendance_rate' => $attendanceRate,
            'final_score' => $attendanceRate,
        ];
    }

    public function issue(ChildProfile $child, LearningPath $course, ?User $issuer = null): Certificate
    {
        $hasEnrollment = Enrollment::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (! $hasEnrollment) {
            throw ValidationException::withMessages([
                'certificate' => 'Peserta tidak memiliki pendaftaran aktif pada kelas ini.',
            ]);
        }

        if (! $course->certificate_enabled) {
            throw ValidationException::withMessages([
                'certificate' => 'Sertifikat tidak diaktifkan untuk kelas ini.',
            ]);
        }

        $evaluation = $this->evaluate($child, $course);

        if (! $evaluation['eligible']) {
            throw ValidationException::withMessages([
                'certificate' => 'Sertifikat tersedia setelah seluruh sesi kelas selesai dan peserta tercatat hadir pada semua sesi wajib.',
            ]);
        }

        $existing = Certificate::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $course->id)
            ->first();

        if ($existing) return $existing;

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
            $number = 'CERT-SP-'.now()->format('Ym').'-'.Str::upper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());
        return $number;
    }
}
