<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentProgressController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Enrollment::with([
            'child.parent',
            'course.instructor',
            'course.modules.activities',
            'activityCompletions',
            'certificate',
            'examAttempt'
        ])->where('status', 'active')->latest();

        if ($search) {
            $query->whereHas('child', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(12)->withQueryString();

        $studentsData = $enrollments->through(function ($enr) {
            $totalActs = 0;
            foreach ($enr->course->modules as $mod) {
                $totalActs += $mod->activities->count();
            }
            $doneActs = $enr->activityCompletions->count();
            $percent = $totalActs > 0 ? min(100, round(($doneActs / $totalActs) * 100)) : 0;

            return [
                'enrollment' => $enr,
                'child' => $enr->child,
                'course' => $enr->course,
                'totalActivities' => $totalActs,
                'completedActivities' => $doneActs,
                'progressPercent' => $percent,
                'examScore' => $enr->examAttempt ? $enr->examAttempt->score : null,
                'certificate' => $enr->certificate,
            ];
        });

        return view('admin.students.index', [
            'students' => $studentsData,
            'paginator' => $enrollments,
            'search' => $search,
            'totalActiveStudents' => Enrollment::where('status', 'active')->count(),
        ]);
    }
}
