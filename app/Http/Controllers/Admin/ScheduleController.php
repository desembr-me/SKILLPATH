<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $day = $request->query('day');
        $instructorId = $request->query('instructor_id');

        $query = CourseSchedule::with(['course', 'instructor', 'enrollments'])->latest();

        if ($day && in_array($day, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])) {
            $query->where('day_of_week', $day);
        }

        if ($instructorId) {
            $query->where('instructor_id', $instructorId);
        }

        $schedules = $query->paginate(12)->withQueryString();
        $instructors = User::where('role', 'mentor')->get();
        $courses = Course::where('status', 'active')->get();

        return view('admin.schedules.index', [
            'schedules' => $schedules,
            'instructors' => $instructors,
            'courses' => $courses,
            'currentDay' => $day ?: 'all',
            'currentInstructor' => $instructorId,
            'totalCount' => CourseSchedule::count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'day_of_week' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $course = Course::findOrFail($data['course_id']);
        $data['instructor_id'] = $course->instructor_id;
        $data['status'] = 'active';

        CourseSchedule::create($data);

        return back()->with('success', 'Jadwal kelas baru berhasil ditambahkan.');
    }

    public function destroy(CourseSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
