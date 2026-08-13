<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LiveSession;
use App\Models\SessionBooking;
use App\Models\SessionCredit;
use App\Services\ScheduleConflictService;
use App\Services\SessionCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LiveClassController extends Controller
{
    public function index(Request $request, ScheduleConflictService $conflictService)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        $courseIds = Enrollment::where('child_profile_id', $child->id)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->pluck('learning_path_id');

        $sessions = LiveSession::whereIn('learning_path_id', $courseIds)
            ->whereHas('learningPath', fn ($query) => $query->where('is_published', true))
            ->whereIn('status', ['scheduled', 'live'])
            ->where('starts_at', '>=', now()->subHours(2))
            ->with(['learningPath', 'instructor.instructorProfile', 'bookings'])
            ->orderBy('starts_at')
            ->get();

        $bookedIds = SessionBooking::where('child_profile_id', $child->id)
            ->where('status', 'booked')
            ->pluck('live_session_id');

        $availableCredits = SessionCredit::where('child_profile_id', $child->id)
            ->available()
            ->with(['learningPath', 'sourceLiveSession'])
            ->latest('credited_at')
            ->get();

        $recentCredits = SessionCredit::where('child_profile_id', $child->id)
            ->with(['learningPath', 'sourceLiveSession', 'usedLiveSession'])
            ->latest('credited_at')
            ->take(10)
            ->get();

        $conflictIds = $sessions
            ->reject(fn ($session) => $bookedIds->contains($session->id))
            ->filter(fn ($session) => $conflictService->hasConflict($child, $session))
            ->pluck('id');

        return view('live.index', compact('sessions', 'bookedIds', 'availableCredits', 'recentCredits', 'conflictIds'));
    }

    public function show(Request $request, LiveSession $liveSession, ScheduleConflictService $conflictService)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        $this->ensureAccess($child->id, $liveSession);

        $liveSession->load(['learningPath', 'instructor.instructorProfile', 'bookings']);
        $booking = SessionBooking::where('child_profile_id', $child->id)
            ->where('live_session_id', $liveSession->id)
            ->where('status', 'booked')
            ->first();
        $conflicts = $booking ? collect() : $conflictService->conflicts($child, $liveSession);
        $alternatives = $conflicts->isNotEmpty()
            ? $conflictService->alternatives($child, $liveSession)
            : collect();
        $availableCredits = SessionCredit::where('child_profile_id', $child->id)
            ->where('learning_path_id', $liveSession->learning_path_id)
            ->available()
            ->get();

        return view('live.show', compact('liveSession', 'booking', 'conflicts', 'alternatives', 'availableCredits'));
    }

    public function confirm(Request $request, LiveSession $liveSession, ScheduleConflictService $conflictService)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        $this->ensureAccess($child->id, $liveSession);
        abort_unless($liveSession->status === 'scheduled', 422, 'Sesi tidak tersedia untuk booking.');
        abort_if($liveSession->starts_at->isPast(), 422, 'Sesi ini sudah dimulai.');

        $liveSession->load(['learningPath', 'instructor.instructorProfile', 'bookings']);
        $existingBooking = SessionBooking::where('child_profile_id', $child->id)
            ->where('live_session_id', $liveSession->id)
            ->where('status', 'booked')
            ->first();
        if ($existingBooking) {
            return redirect()->route('live.show', $liveSession);
        }

        $conflicts = $conflictService->conflicts($child, $liveSession);
        $alternatives = $conflicts->isNotEmpty()
            ? $conflictService->alternatives($child, $liveSession)
            : collect();
        $availableCredits = SessionCredit::where('child_profile_id', $child->id)
            ->where('learning_path_id', $liveSession->learning_path_id)
            ->available()
            ->get();

        return view('live.confirm', compact('liveSession', 'conflicts', 'alternatives', 'availableCredits'));
    }

    public function book(
        Request $request,
        LiveSession $liveSession,
        ScheduleConflictService $conflictService,
        SessionCreditService $creditService
    ) {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        $this->ensureAccess($child->id, $liveSession);
        abort_unless($liveSession->status === 'scheduled', 422, 'Sesi tidak tersedia untuk booking.');
        abort_if($liveSession->starts_at->isPast(), 422, 'Sesi ini sudah dimulai.');

        $data = $request->validate([
            'session_credit_id' => ['nullable', 'integer', 'exists:session_credits,id'],
        ]);

        if (! empty($data['session_credit_id'])) {
            $credit = SessionCredit::findOrFail($data['session_credit_id']);
            $creditService->useCredit($child, $liveSession, $credit, $conflictService);

            return redirect()->route('live.show', $liveSession)
                ->with('success', 'Sesi berhasil dijadwalkan menggunakan 1 kredit sesi.');
        }

        // Jika sesi ini sebelumnya dilepas menjadi kredit, booking ulang harus
        // memakai kembali kredit yang sama agar tidak tercipta hak sesi ganda.
        $previousBooking = SessionBooking::query()
            ->where('child_profile_id', $child->id)
            ->where('live_session_id', $liveSession->id)
            ->where('status', 'credited')
            ->first();

        if ($previousBooking) {
            $restorableCredit = $previousBooking->session_credit_id
                ? SessionCredit::query()->available()->find($previousBooking->session_credit_id)
                : SessionCredit::query()->available()->where('source_booking_id', $previousBooking->id)->first();

            if ($restorableCredit) {
                $creditService->useCredit($child, $liveSession, $restorableCredit, $conflictService);

                return redirect()->route('live.show', $liveSession)
                    ->with('success', 'Booking sesi dipulihkan dan kredit sesi digunakan kembali, tanpa membuat hak sesi tambahan.');
            }
        }

        DB::transaction(function () use ($liveSession, $child, $conflictService) {
            $lockedChild = ChildProfile::query()->whereKey($child->id)->lockForUpdate()->firstOrFail();
            $lockedSession = LiveSession::query()->whereKey($liveSession->id)->lockForUpdate()->firstOrFail();

            if ($lockedSession->status !== 'scheduled' || $lockedSession->starts_at->isPast()) {
                throw ValidationException::withMessages(['schedule' => 'Sesi tidak lagi tersedia untuk booking.']);
            }

            if ($conflictService->hasConflict($lockedChild, $lockedSession)) {
                throw ValidationException::withMessages([
                    'schedule' => 'Jadwal ini bertabrakan dengan sesi lain. Pilih salah satu jadwal alternatif yang tersedia.',
                ]);
            }

            $existing = SessionBooking::query()
                ->where('live_session_id', $lockedSession->id)
                ->where('child_profile_id', $lockedChild->id)
                ->lockForUpdate()
                ->first();

            if ($existing?->status === 'booked') {
                return;
            }

            $count = $lockedSession->bookings()->where('status', 'booked')->count();
            if ($count >= $lockedSession->capacity) {
                throw ValidationException::withMessages(['schedule' => 'Kapasitas kelas sudah penuh.']);
            }

            SessionBooking::updateOrCreate(
                ['live_session_id' => $lockedSession->id, 'child_profile_id' => $lockedChild->id],
                [
                    'status' => 'booked',
                    'booked_at' => now(),
                    'booking_source' => 'direct',
                    'session_credit_id' => null,
                    'absence_reason' => null,
                    'credited_at' => null,
                ]
            );
        });

        return redirect()->route('live.show', $liveSession)->with('success', 'Kelas live berhasil dipesan.');
    }

    public function convertToCredit(
        Request $request,
        LiveSession $liveSession,
        SessionCreditService $creditService
    ) {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        $this->ensureAccess($child->id, $liveSession);

        if ($liveSession->ends_at->isPast()) {
            throw ValidationException::withMessages(['credit' => 'Sesi yang sudah selesai tidak dapat dikonversi menjadi kredit.']);
        }

        $data = $request->validate([
            'reason' => ['required', 'in:sakit,bentrok,keluarga,lainnya'],
            'reason_note' => ['nullable', 'string', 'max:500'],
        ]);

        $booking = SessionBooking::where('child_profile_id', $child->id)
            ->where('live_session_id', $liveSession->id)
            ->whereIn('status', ['booked', 'credited'])
            ->firstOrFail();

        $creditService->creditBooking($booking, $data['reason'], $data['reason_note'] ?? null);

        return redirect()->route('live.index')
            ->with('success', 'Booking dikonversi menjadi 1 kredit sesi. Kredit dapat dipakai untuk jadwal pengganti pada course yang sama.');
    }

    private function ensureAccess(int $childId, LiveSession $liveSession): void
    {
        abort_unless($liveSession->learningPath, 404);
        abort_unless(
            Enrollment::where('child_profile_id', $childId)
                ->where('learning_path_id', $liveSession->learning_path_id)
                ->where('status', 'active')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->exists(),
            403
        );
    }
}
