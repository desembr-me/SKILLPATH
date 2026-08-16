<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courses = $user->courses()->with('category')->get();
        $courseIds = $courses->pluck('id');

        $schedules = CourseSchedule::where(function ($q) use ($user, $courseIds) {
            $q->where('instructor_id', $user->id)
              ->orWhereIn('course_id', $courseIds);
        })
        ->with(['course.category', 'enrollments.child', 'sessions' => function ($q) {
            $q->orderBy('session_no');
        }])
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get();

        return view('mentor.schedules', [
            'courses' => $courses,
            'schedules' => $schedules,
            'days' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('id')->toArray();

        $data = $request->validate([
            'course_id' => ['required', 'in:' . implode(',', $courseIds)],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'room' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:open,full,closed'],
        ]);

        $course = Course::findOrFail($data['course_id']);

        $schedule = CourseSchedule::create([
            'course_id' => $course->id,
            'instructor_id' => $user->id,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'] . ':00',
            'end_time' => $data['end_time'] . ':00',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? Carbon::parse($data['start_date'])->addMonths(3)->toDateString(),
            'capacity' => $data['capacity'],
            'room' => $data['room'] ?? 'Studio Utama',
            'status' => $data['status'],
        ]);

        // Auto-generate course sessions
        $this->generateSessionsForSchedule($schedule, $course);

        return back()->with('success', 'Jadwal baru berhasil ditambahkan dan sesi pertemuan telah dibuat.');
    }

    public function update(Request $request, CourseSchedule $schedule)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('id')->toArray();

        abort_unless($schedule->instructor_id === $user->id || in_array($schedule->course_id, $courseIds, true), 403);

        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'room' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:open,full,closed'],
        ]);

        $schedule->update([
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'] . ':00',
            'end_time' => $data['end_time'] . ':00',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? $schedule->end_date,
            'capacity' => $data['capacity'],
            'room' => $data['room'] ?? $schedule->room,
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, CourseSchedule $schedule)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('id')->toArray();

        abort_unless($schedule->instructor_id === $user->id || in_array($schedule->course_id, $courseIds, true), 403);

        if ($schedule->enrollments()->where('status', 'active')->exists()) {
            return back()->withErrors(['error' => 'Jadwal tidak dapat dihapus karena memiliki siswa aktif. Ubah status menjadi closed jika ingin menutup jadwal.']);
        }

        $schedule->sessions()->delete();
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function generateSessions(Request $request, CourseSchedule $schedule)
    {
        $user = $request->user();
        $courseIds = $user->courses()->pluck('id')->toArray();

        abort_unless($schedule->instructor_id === $user->id || in_array($schedule->course_id, $courseIds, true), 403);

        $course = $schedule->course;
        $this->generateSessionsForSchedule($schedule, $course);

        return back()->with('success', 'Sesi pertemuan berhasil dibuat ulang sesuai jadwal.');
    }

    private function generateSessionsForSchedule(CourseSchedule $schedule, Course $course): void
    {
        $sessionsCount = $course->sessions_count ?: 8;
        $existingCount = $schedule->sessions()->count();

        if ($existingCount < $sessionsCount) {
            $startDate = Carbon::parse($schedule->start_date);
            // find first occurrence of day_of_week on or after start_date
            while ($startDate->dayOfWeek !== (int) $schedule->day_of_week) {
                $startDate->addDay();
            }

            for ($i = $existingCount + 1; $i <= $sessionsCount; $i++) {
                $sessionDate = (clone $startDate)->addWeeks($i - 1);
                CourseSession::create([
                    'course_id' => $course->id,
                    'schedule_id' => $schedule->id,
                    'session_no' => $i,
                    'session_date' => $sessionDate->toDateString(),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'topic' => 'Pertemuan ' . $i . ': Materi & Praktik ' . $course->title,
                    'status' => 'scheduled',
                ]);
            }
        }
    }
}
