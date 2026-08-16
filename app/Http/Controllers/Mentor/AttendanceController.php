<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Services\SessionCreditService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, SessionCreditService $credits)
    {
        $data = $request->validate([
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'course_session_id' => ['required', 'exists:course_sessions,id'],
            'status' => ['required', 'in:present,absent,excused,rescheduled'],
            'absence_reason' => ['nullable', 'string', 'max:255'],
            'mentor_note' => ['nullable', 'string', 'max:1500'],
            'credit_eligible' => ['nullable', 'boolean'],
        ]);

        $enrollment = Enrollment::with('course')->findOrFail($data['enrollment_id']);
        abort_unless($enrollment->course->instructor_id === $request->user()->id, 403);

        $session = CourseSession::findOrFail($data['course_session_id']);
        abort_unless($session->course_id === $enrollment->course_id, 422);

        $attendance = Attendance::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_session_id' => $session->id,
            ],
            [
                'status' => $data['status'],
                'absence_reason' => $data['absence_reason'] ?? null,
                'mentor_note' => $data['mentor_note'] ?? null,
                'credit_eligible' => (bool) ($data['credit_eligible'] ?? false),
            ]
        );

        $creditCreated = false;
        if ($attendance->credit_eligible && in_array($attendance->status, ['absent', 'excused'], true)) {
            $attendance->load('enrollment');
            $credit = $credits->createFromAttendance($attendance);
            $creditCreated = (bool) $credit;
        }

        $statusText = match ($data['status']) {
            'present' => 'Hadir',
            'excused' => 'Izin',
            'absent' => 'Tidak Hadir',
            'rescheduled' => 'Jadwal Ulang',
            default => $data['status'],
        };

        $msg = "Presensi Sesi {$session->session_no} berhasil disimpan ({$statusText}).";
        if ($creditCreated) {
            $msg .= " Kredit sesi pengganti otomatis telah diterbitkan untuk siswa.";
        }

        return back()->with('success', $msg);
    }
}
