<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\LiveSession;
use App\Models\SessionBooking;
use Illuminate\Support\Collection;

class ScheduleConflictService
{
    public function conflicts(ChildProfile $child, LiveSession $target): Collection
    {
        return $this->conflictsForWindow(
            $child,
            $target->starts_at,
            $target->ends_at,
            $target->id
        );
    }

    public function conflictsForWindow(
        ChildProfile $child,
        mixed $startsAt,
        mixed $endsAt,
        ?int $ignoreSessionId = null
    ): Collection {
        return SessionBooking::query()
            ->where('child_profile_id', $child->id)
            ->where('status', 'booked')
            ->when($ignoreSessionId, fn ($query) => $query->where('live_session_id', '!=', $ignoreSessionId))
            ->whereHas('liveSession', function ($query) use ($startsAt, $endsAt) {
                $query->whereIn('status', ['scheduled', 'live'])
                    ->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->with(['liveSession.learningPath'])
            ->get();
    }

    public function hasConflict(ChildProfile $child, LiveSession $target): bool
    {
        return $this->conflicts($child, $target)->isNotEmpty();
    }

    public function alternatives(ChildProfile $child, LiveSession $target, int $limit = 3): Collection
    {
        return LiveSession::query()
            ->where('learning_path_id', $target->learning_path_id)
            ->where('id', '!=', $target->id)
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->whereDoesntHave('bookings', fn ($query) => $query
                ->where('child_profile_id', $child->id)
                ->where('status', 'booked'))
            ->with(['learningPath', 'instructor'])
            ->withCount([
                'bookings as booked_count' => fn ($query) => $query->where('status', 'booked'),
            ])
            ->orderBy('starts_at')
            ->get()
            ->filter(fn (LiveSession $session) => $session->booked_count < $session->capacity)
            ->filter(fn (LiveSession $session) => ! $this->hasConflict($child, $session))
            ->take($limit)
            ->values();
    }

    public function conflictingBookedChildrenForReschedule(
        LiveSession $session,
        mixed $startsAt,
        mixed $endsAt
    ): Collection {
        $childIds = $session->bookings()
            ->where('status', 'booked')
            ->pluck('child_profile_id');

        if ($childIds->isEmpty()) {
            return collect();
        }

        return SessionBooking::query()
            ->whereIn('child_profile_id', $childIds)
            ->where('status', 'booked')
            ->where('live_session_id', '!=', $session->id)
            ->whereHas('liveSession', function ($query) use ($startsAt, $endsAt) {
                $query->whereIn('status', ['scheduled', 'live'])
                    ->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->pluck('child_profile_id')
            ->unique()
            ->values();
    }
}
