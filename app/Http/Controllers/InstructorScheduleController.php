<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\LearningPath;
use App\Models\SessionBooking;
use Illuminate\Http\Request;

class InstructorScheduleController extends Controller
{
    public function store(Request $request, LearningPath $learningPath)
    {
        abort_unless($request->user()->role === 'instructor' && $learningPath->instructor_id === $request->user()->id, 403);

        $data = $request->validate([
            'title'=>['required','string','max:150'],
            'description'=>['nullable','string','max:2000'],
            'starts_at'=>['required','date','after:now'],
            'ends_at'=>['required','date','after:starts_at'],
            'venue_name'=>['required','string','max:180'],
            'address'=>['required','string','max:1000'],
            'room'=>['nullable','string','max:100'],
            'map_url'=>['nullable','url','max:255'],
            'capacity'=>['required','integer','between:1,500'],
            'preparation_notes'=>['nullable','string','max:2000'],
        ]);

        $learningPath->classSessions()->create($data + [
            'instructor_id'=>$request->user()->id,
            'status'=>'scheduled',
        ]);

        return back()->with('success', 'Jadwal kelas offline berhasil dibuat.');
    }

    public function show(Request $request, ClassSession $classSession)
    {
        abort_unless($request->user()->role === 'instructor' && $classSession->instructor_id === $request->user()->id, 403);

        $classSession->load([
            'learningPath',
            'bookings.childProfile.user',
        ]);

        return view('instructor.schedules.show', compact('classSession'));
    }

    public function updateAttendance(Request $request, ClassSession $classSession, SessionBooking $sessionBooking)
    {
        abort_unless($request->user()->role === 'instructor' && $classSession->instructor_id === $request->user()->id, 403);
        abort_unless($sessionBooking->class_session_id === $classSession->id, 404);

        $data = $request->validate([
            'status' => ['required','in:booked,attended,absent,cancelled'],
            'notes' => ['nullable','string','max:500'],
        ]);

        $sessionBooking->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'checked_in_at' => $data['status'] === 'attended' ? now() : null,
        ]);

        return back()->with('success', 'Status kehadiran diperbarui.');
    }

    public function complete(Request $request, ClassSession $classSession)
    {
        abort_unless($request->user()->role === 'instructor' && $classSession->instructor_id === $request->user()->id, 403);
        abort_if($classSession->status === 'cancelled', 422, 'Jadwal yang dibatalkan tidak dapat diselesaikan.');

        $classSession->update(['status' => 'completed']);
        return back()->with('success', 'Sesi kelas ditandai selesai.');
    }

    public function destroy(Request $request, ClassSession $classSession)
    {
        abort_unless($request->user()->role === 'instructor' && $classSession->instructor_id === $request->user()->id, 403);
        abort_if($classSession->bookings()->whereIn('status', ['booked','attended'])->exists(), 422, 'Jadwal yang sudah memiliki peserta tidak dapat dihapus. Batalkan jadwal bila diperlukan.');

        $classSession->delete();
        return back()->with('success', 'Jadwal kelas dihapus.');
    }
}
