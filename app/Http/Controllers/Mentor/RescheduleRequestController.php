<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\RescheduleRequest;
use Illuminate\Http\Request;

class RescheduleRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Mark unread as read
        $user->mentorRescheduleRequests()->where('is_read', false)->update(['is_read' => true]);

        $requests = RescheduleRequest::where('mentor_id', $user->id)
            ->with([
                'enrollment.child',
                'enrollment.course.category',
                'parent',
                'currentSchedule',
                'requestedSchedule',
            ])
            ->latest()
            ->get();

        $pendingRequests = $requests->where('status', 'pending');
        $resolvedRequests = $requests->whereIn('status', ['approved', 'rejected']);

        return view('mentor.reschedules', [
            'pendingRequests' => $pendingRequests,
            'resolvedRequests' => $resolvedRequests,
            'days' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ]);
    }

    public function approve(Request $request, RescheduleRequest $rescheduleRequest)
    {
        $user = $request->user();
        abort_unless($rescheduleRequest->mentor_id === $user->id, 403);
        abort_unless($rescheduleRequest->status === 'pending', 422);

        $note = $request->input('mentor_note');

        // Update enrollment schedule
        $enrollment = $rescheduleRequest->enrollment;
        if ($enrollment) {
            $enrollment->update([
                'schedule_id' => $rescheduleRequest->requested_schedule_id,
            ]);
        }

        $rescheduleRequest->update([
            'status' => 'approved',
            'mentor_note' => $note ?: 'Disetujui oleh mentor.',
            'is_read' => true,
        ]);

        return back()->with('success', 'Permintaan perubahan jadwal telah disetujui. Jadwal siswa berhasil dipindahkan.');
    }

    public function reject(Request $request, RescheduleRequest $rescheduleRequest)
    {
        $user = $request->user();
        abort_unless($rescheduleRequest->mentor_id === $user->id, 403);
        abort_unless($rescheduleRequest->status === 'pending', 422);

        $request->validate([
            'mentor_note' => ['required', 'string', 'max:500'],
        ]);

        $rescheduleRequest->update([
            'status' => 'rejected',
            'mentor_note' => $request->input('mentor_note'),
            'is_read' => true,
        ]);

        return back()->with('success', 'Permintaan perubahan jadwal telah ditolak.');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->mentorRescheduleRequests()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
