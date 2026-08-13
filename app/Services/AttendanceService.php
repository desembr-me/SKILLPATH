<?php

namespace App\Services;

use App\Models\ChildProfile;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function ensureLoaded(ChildProfile $child): ChildProfile
    {
        return $child->loadMissing([
            'user',
            'interests',
            'enrollments.learningPath.instructor',
            'enrollments.learningPath.classSessions',
            'classBookings.classSession.learningPath',
        ]);
    }

    public function summarize(ChildProfile $child, ?int $courseId = null): array
    {
        $this->ensureLoaded($child);

        $enrollments = $child->enrollments
            ->filter(fn ($enrollment) => $enrollment->status === 'active' && $enrollment->learningPath)
            ->when($courseId, fn (Collection $items) => $items->where('learning_path_id', $courseId))
            ->values();

        $courseIds = $enrollments->pluck('learning_path_id');
        $sessions = $enrollments
            ->flatMap(fn ($enrollment) => $enrollment->learningPath->classSessions)
            ->unique('id')
            ->values();

        $bookings = $child->classBookings
            ->filter(fn ($booking) => $booking->classSession && $courseIds->contains($booking->classSession->learning_path_id))
            ->values();

        $attendanceRecords = $bookings->whereIn('status', ['attended', 'absent']);
        $attended = $bookings->where('status', 'attended')->count();
        $absent = $bookings->where('status', 'absent')->count();
        $upcomingSessions = $sessions
            ->where('status', 'scheduled')
            ->filter(fn ($session) => $session->starts_at && $session->starts_at->isFuture());

        $activeBookingSessionIds = $bookings
            ->whereIn('status', ['booked', 'attended'])
            ->pluck('class_session_id');

        $unbookedUpcoming = $upcomingSessions
            ->reject(fn ($session) => $activeBookingSessionIds->contains($session->id))
            ->count();

        $upcomingBooked = $bookings
            ->where('status', 'booked')
            ->filter(fn ($booking) => $booking->classSession?->starts_at?->isFuture())
            ->count();

        $lastSessionAt = $bookings
            ->filter(fn ($booking) => in_array($booking->status, ['attended','absent'], true))
            ->map(fn ($booking) => $booking->classSession?->ends_at)
            ->filter()
            ->sortDesc()
            ->first();

        $attendanceRate = $attendanceRecords->count() > 0
            ? (int) round(($attended / $attendanceRecords->count()) * 100)
            : null;

        $status = match (true) {
            $sessions->isEmpty() => 'not_scheduled',
            $absent > 0 || $unbookedUpcoming > 0 => 'needs_attention',
            $upcomingSessions->isNotEmpty() => 'active',
            $attendanceRecords->isNotEmpty() => 'completed',
            default => 'not_scheduled',
        };

        return [
            'child' => $child,
            'enrollment_count' => $enrollments->count(),
            'session_count' => $sessions->where('status', '!=', 'cancelled')->count(),
            'booking_count' => $bookings->where('status', '!=', 'cancelled')->count(),
            'attended_count' => $attended,
            'absent_count' => $absent,
            'upcoming_booked' => $upcomingBooked,
            'unbooked_upcoming' => $unbookedUpcoming,
            'attendance_rate' => $attendanceRate,
            'last_session_at' => $lastSessionAt,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'attention_reason' => $this->attentionReason($absent, $unbookedUpcoming),
        ];
    }

    public function courseBreakdown(ChildProfile $child): Collection
    {
        $this->ensureLoaded($child);

        return $child->enrollments
            ->filter(fn ($enrollment) => $enrollment->status === 'active' && $enrollment->learningPath)
            ->map(function ($enrollment) use ($child) {
                $path = $enrollment->learningPath;
                $sessionIds = $path->classSessions->pluck('id');
                $bookings = $child->classBookings->whereIn('class_session_id', $sessionIds);
                $records = $bookings->whereIn('status', ['attended','absent']);
                $attended = $bookings->where('status', 'attended')->count();
                $upcoming = $path->classSessions
                    ->where('status', 'scheduled')
                    ->filter(fn ($session) => $session->starts_at?->isFuture())
                    ->count();

                $absent = $bookings->where('status', 'absent')->count();
                $status = match (true) {
                    $absent > 0 => 'needs_attention',
                    $upcoming > 0 => 'active',
                    $records->isNotEmpty() => 'completed',
                    default => 'not_scheduled',
                };

                return [
                    'enrollment' => $enrollment,
                    'path' => $path,
                    'session_count' => $path->classSessions->where('status', '!=', 'cancelled')->count(),
                    'attended_count' => $attended,
                    'absent_count' => $absent,
                    'upcoming_count' => $upcoming,
                    'attendance_rate' => $records->count() > 0 ? (int) round(($attended / $records->count()) * 100) : null,
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                ];
            })
            ->sortByDesc(fn ($item) => $item['enrollment']->enrolled_at)
            ->values();
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Jadwal aktif',
            'completed' => 'Program selesai',
            'needs_attention' => 'Perlu perhatian',
            default => 'Belum ada jadwal',
        };
    }

    private function attentionReason(int $absent, int $unbookedUpcoming): ?string
    {
        $reasons = [];
        if ($absent > 0) $reasons[] = $absent.' sesi tercatat tidak hadir';
        if ($unbookedUpcoming > 0) $reasons[] = $unbookedUpcoming.' jadwal mendatang belum dipesan';
        return $reasons ? implode(' · ', $reasons) : null;
    }
}
