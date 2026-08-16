<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Models\RescheduleRequest;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request, ScheduleConflictService $svc)
    {
        $user = $request->user();
        $enrollments = $user->enrollments()
            ->where('status', 'active')
            ->with(['course', 'schedule', 'child', 'rescheduleRequests.requestedSchedule'])
            ->get();

        $alternatives = $enrollments->mapWithKeys(fn ($e) => [$e->id => $svc->alternatives($e->schedule)]);

        $recentRequests = $user->parentRescheduleRequests()
            ->with(['enrollment.course', 'enrollment.child', 'currentSchedule', 'requestedSchedule', 'mentor'])
            ->latest()
            ->take(5)
            ->get();

        return view('parent.schedule', compact('enrollments', 'alternatives', 'recentRequests'));
    }

    public function update(Request $request, Enrollment $enrollment, ScheduleConflictService $svc)
    {
        abort_unless($enrollment->parent_id === $request->user()->id, 403);
        abort_unless($enrollment->status === 'active', 422);

        $data = $request->validate([
            'schedule_id' => ['required', 'exists:course_schedules,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $target = CourseSchedule::findOrFail($data['schedule_id']);
        abort_unless($target->course_id === $enrollment->course_id, 422);

        // Check if there is already a pending request
        $hasPending = RescheduleRequest::where('enrollment_id', $enrollment->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->withErrors(['error' => 'Masih ada permohonan perubahan jadwal yang menunggu konfirmasi mentor.']);
        }

        $conflicts = $svc->conflicts($enrollment->child, $target, $enrollment->id);
        if ($conflicts) {
            return back()->withErrors(['error' => 'Jadwal baru bentrok dengan course aktif anak lainnya.']);
        }

        // Create reschedule request for mentor
        RescheduleRequest::create([
            'enrollment_id' => $enrollment->id,
            'parent_id' => $request->user()->id,
            'mentor_id' => $enrollment->course->instructor_id,
            'current_schedule_id' => $enrollment->schedule_id,
            'requested_schedule_id' => $target->id,
            'reason' => $data['reason'] ?: 'Permintaan perubahan jadwal oleh orang tua.',
            'status' => 'pending',
            'is_read' => false,
        ]);

        return back()->with('success', 'Permintaan perubahan jadwal berhasil dikirimkan ke pengajar. Pengajar akan memeriksa dan mengonfirmasi.');
    }
}
