<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\SessionBooking;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $child = $request->user()->childProfile;
        if (! $child) return redirect()->route('onboarding.edit');

        $courseIds = Enrollment::where('child_profile_id', $child->id)
            ->where('status', 'active')
            ->pluck('learning_path_id');

        $sessions = ClassSession::whereIn('learning_path_id', $courseIds)
            ->whereHas('learningPath', fn ($query) => $query->where('is_published', true))
            ->where('status', 'scheduled')
            ->where('starts_at', '>=', now())
            ->when($request->integer('course'), fn ($query, $courseId) => $query->where('learning_path_id', $courseId))
            ->with(['learningPath','instructor.instructorProfile','bookings'])
            ->orderBy('starts_at')
            ->get();

        $bookings = SessionBooking::where('child_profile_id', $child->id)
            ->whereIn('class_session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('class_session_id');

        return view('classes.index', compact('sessions','bookings'));
    }

    public function show(Request $request, ClassSession $classSession)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        abort_unless($classSession->learningPath, 404);
        abort_unless(
            Enrollment::where('child_profile_id', $child->id)
                ->where('learning_path_id', $classSession->learning_path_id)
                ->where('status', 'active')
                ->exists(),
            403
        );

        $classSession->load(['learningPath','instructor.instructorProfile','bookings']);
        $booking = SessionBooking::where('child_profile_id', $child->id)
            ->where('class_session_id', $classSession->id)
            ->first();

        return view('classes.show', compact('classSession','booking'));
    }

    public function book(Request $request, ClassSession $classSession)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        abort_unless($classSession->status === 'scheduled' && $classSession->starts_at->isFuture(), 422, 'Jadwal ini sudah tidak dapat dipesan.');
        abort_unless(
            Enrollment::where('child_profile_id', $child->id)
                ->where('learning_path_id', $classSession->learning_path_id)
                ->where('status', 'active')
                ->exists(),
            403
        );

        $count = $classSession->bookings()->whereIn('status', ['booked','attended'])->count();
        abort_if($count >= $classSession->capacity, 422, 'Kapasitas kelas sudah penuh.');

        SessionBooking::updateOrCreate(
            ['class_session_id'=>$classSession->id,'child_profile_id'=>$child->id],
            ['status'=>'booked','booked_at'=>now(),'checked_in_at'=>null]
        );

        return back()->with('success', 'Kursi kelas tatap muka berhasil dipesan.');
    }

    public function cancel(Request $request, ClassSession $classSession)
    {
        $child = $request->user()->childProfile;
        abort_unless($child, 403);
        abort_unless($classSession->starts_at->isFuture(), 422, 'Jadwal yang sudah dimulai tidak dapat dibatalkan.');

        $booking = SessionBooking::where('class_session_id', $classSession->id)
            ->where('child_profile_id', $child->id)
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Pemesanan kursi dibatalkan.');
    }
}
