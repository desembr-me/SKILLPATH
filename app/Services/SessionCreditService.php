<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LiveSession;
use App\Models\SessionBooking;
use App\Models\SessionCredit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionCreditService
{
    public function creditBooking(SessionBooking $booking, string $reason, ?string $note = null): SessionCredit
    {
        return DB::transaction(function () use ($booking, $reason, $note) {
            $lockedBooking = SessionBooking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedBooking->loadMissing('liveSession');

            if ($lockedBooking->status === 'credited') {
                $existingCredit = $this->creditForCreditedBooking($lockedBooking);
                if ($existingCredit->isAvailable()) {
                    return $existingCredit;
                }

                throw ValidationException::withMessages([
                    'credit' => 'Booking ini sudah pernah dikonversi dan kredit terkait sudah digunakan atau kedaluwarsa.',
                ]);
            }

            if ($lockedBooking->status !== 'booked') {
                throw ValidationException::withMessages([
                    'credit' => 'Hanya booking aktif yang dapat dikonversi menjadi kredit sesi.',
                ]);
            }

            // Jika booking ini sebelumnya dibuat memakai kredit, aktifkan kembali
            // kredit yang sama. Ini mencegah reschedule berulang mencetak kredit baru.
            if ($lockedBooking->session_credit_id) {
                $credit = SessionCredit::query()
                    ->whereKey($lockedBooking->session_credit_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $credit->update([
                    'reason' => $reason,
                    'reason_note' => $note,
                    'status' => 'available',
                    'used_live_session_id' => null,
                    'used_at' => null,
                    'expires_at' => $this->resolveExpiry($lockedBooking),
                    'reactivation_count' => $credit->reactivation_count + 1,
                    'last_reactivated_at' => now(),
                ]);
            } else {
                $credit = SessionCredit::firstOrCreate(
                    ['source_booking_id' => $lockedBooking->id],
                    [
                        'child_profile_id' => $lockedBooking->child_profile_id,
                        'learning_path_id' => $lockedBooking->liveSession->learning_path_id,
                        'source_live_session_id' => $lockedBooking->live_session_id,
                        'reason' => $reason,
                        'reason_note' => $note,
                        'status' => 'available',
                        'credited_at' => now(),
                        'expires_at' => $this->resolveExpiry($lockedBooking),
                    ]
                );
            }

            $lockedBooking->update([
                'status' => 'credited',
                'absence_reason' => $reason,
                'credited_at' => now(),
            ]);

            return $credit->fresh();
        });
    }

    public function useCredit(
        ChildProfile $child,
        LiveSession $target,
        SessionCredit $credit,
        ScheduleConflictService $conflicts
    ): SessionBooking {
        return DB::transaction(function () use ($child, $target, $credit, $conflicts) {
            // Serialisasi operasi booking per anak agar dua request bersamaan
            // tidak dapat membuat dua sesi yang saling bertabrakan.
            $lockedChild = ChildProfile::query()->whereKey($child->id)->lockForUpdate()->firstOrFail();
            $lockedSession = LiveSession::query()->whereKey($target->id)->lockForUpdate()->firstOrFail();
            $lockedCredit = SessionCredit::query()->whereKey($credit->id)->lockForUpdate()->firstOrFail();

            $this->assertTargetBookable($lockedSession);
            $this->assertEnrollmentActive($lockedChild, $lockedSession);

            if ($lockedCredit->child_profile_id !== $lockedChild->id || ! $lockedCredit->isAvailable()) {
                throw ValidationException::withMessages([
                    'credit' => $lockedCredit->isExpired()
                        ? 'Masa berlaku kredit sesi sudah berakhir.'
                        : 'Kredit sesi tidak tersedia atau bukan milik siswa ini.',
                ]);
            }

            if ($lockedCredit->learning_path_id !== $lockedSession->learning_path_id) {
                throw ValidationException::withMessages([
                    'credit' => 'Kredit hanya dapat digunakan untuk menjadwalkan ulang sesi pada course yang sama.',
                ]);
            }

            if ($conflicts->hasConflict($lockedChild, $lockedSession)) {
                throw ValidationException::withMessages([
                    'schedule' => 'Jadwal pengganti masih bertabrakan dengan sesi lain yang sudah dimiliki siswa.',
                ]);
            }

            $existing = SessionBooking::query()
                ->where('live_session_id', $lockedSession->id)
                ->where('child_profile_id', $lockedChild->id)
                ->lockForUpdate()
                ->first();

            if ($existing?->status === 'booked') {
                throw ValidationException::withMessages([
                    'schedule' => 'Siswa sudah memiliki booking aktif pada sesi ini.',
                ]);
            }

            $bookedCount = $lockedSession->bookings()->where('status', 'booked')->count();
            if ($bookedCount >= $lockedSession->capacity) {
                throw ValidationException::withMessages([
                    'schedule' => 'Kapasitas kelas pengganti sudah penuh.',
                ]);
            }

            $booking = SessionBooking::updateOrCreate(
                ['live_session_id' => $lockedSession->id, 'child_profile_id' => $lockedChild->id],
                [
                    'status' => 'booked',
                    'booked_at' => now(),
                    'booking_source' => 'credit',
                    'session_credit_id' => $lockedCredit->id,
                    'absence_reason' => null,
                    'credited_at' => null,
                ]
            );

            $lockedCredit->update([
                'status' => 'used',
                'used_live_session_id' => $lockedSession->id,
                'used_at' => now(),
            ]);

            return $booking;
        });
    }

    private function creditForCreditedBooking(SessionBooking $booking): SessionCredit
    {
        if ($booking->session_credit_id) {
            return SessionCredit::findOrFail($booking->session_credit_id);
        }

        return SessionCredit::where('source_booking_id', $booking->id)->firstOrFail();
    }

    private function resolveExpiry(SessionBooking $booking): mixed
    {
        return Enrollment::query()
            ->where('child_profile_id', $booking->child_profile_id)
            ->where('learning_path_id', $booking->liveSession->learning_path_id)
            ->value('expires_at');
    }

    private function assertTargetBookable(LiveSession $session): void
    {
        if ($session->status !== 'scheduled' || $session->starts_at->isPast()) {
            throw ValidationException::withMessages([
                'schedule' => 'Sesi pengganti tidak lagi tersedia untuk booking.',
            ]);
        }
    }

    private function assertEnrollmentActive(ChildProfile $child, LiveSession $session): void
    {
        $active = Enrollment::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $session->learning_path_id)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();

        if (! $active) {
            throw ValidationException::withMessages([
                'credit' => 'Kredit tidak dapat digunakan karena akses course sudah tidak aktif.',
            ]);
        }
    }
}
