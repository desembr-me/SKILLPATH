<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request, ScheduleConflictService $svc)
    {
        $enrollments = $request->user()->enrollments()
            ->where('status', 'active')
            ->with(['course', 'schedule', 'child'])
            ->get();

        $alternatives = $enrollments->mapWithKeys(fn ($e) => [$e->id => $svc->alternatives($e->schedule)]);

        return view('parent.schedule', compact('enrollments', 'alternatives'));
    }

    public function update(Request $request, Enrollment $enrollment, ScheduleConflictService $svc)
    {
        abort_unless($enrollment->parent_id === $request->user()->id, 403);
        abort_unless($enrollment->status === 'active', 422);

        $data = $request->validate([
            'schedule_id' => ['required', 'exists:course_schedules,id'],
        ]);

        $target = CourseSchedule::findOrFail($data['schedule_id']);
        abort_unless($target->course_id === $enrollment->course_id, 422);

        $conflicts = $svc->conflicts($enrollment->child, $target, $enrollment->id);
        if ($conflicts) {
            return back()->with('error', 'Jadwal baru bentrok dengan course aktif anak lainnya.');
        }

        $enrollment->update(['schedule_id' => $target->id]);

        return back()->with('success', 'Jadwal berhasil diubah.');
    }
}
