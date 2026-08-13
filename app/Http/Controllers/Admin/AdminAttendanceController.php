<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use App\Models\LearningPath;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAttendanceController extends Controller
{
    public function index(Request $request, AttendanceService $attendanceService)
    {
        $this->validateFilters($request);
        [$rows, $stats] = $this->buildRows($request, $attendanceService);

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $students = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $courses = LearningPath::query()->whereHas('enrollments')->orderBy('title')->get(['id','title']);
        return view('admin.attendance.index', compact('students','stats','courses'));
    }

    public function show(ChildProfile $childProfile, AttendanceService $attendanceService)
    {
        $attendanceService->ensureLoaded($childProfile);
        $summary = $attendanceService->summarize($childProfile);
        $courseAttendance = $attendanceService->courseBreakdown($childProfile);
        $bookings = $childProfile->classBookings
            ->filter(fn ($booking) => $booking->classSession)
            ->sortByDesc(fn ($booking) => $booking->classSession->starts_at)
            ->values();

        return view('admin.attendance.show', compact('childProfile','summary','courseAttendance','bookings'));
    }

    public function export(Request $request, AttendanceService $attendanceService): StreamedResponse
    {
        $this->validateFilters($request);
        [$rows] = $this->buildRows($request, $attendanceService);
        $filename = 'monitoring-kehadiran-peserta-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Nama Anak','Usia','Orang Tua','Email','Kelas Terdaftar','Jadwal','Booking','Hadir','Tidak Hadir',
                'Jadwal Mendatang Dipesan','Jadwal Belum Dipesan','Tingkat Kehadiran (%)','Status','Catatan'
            ], ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['child']->name,
                    $row['child']->age,
                    $row['child']->user?->name,
                    $row['child']->user?->email,
                    $row['enrollment_count'],
                    $row['session_count'],
                    $row['booking_count'],
                    $row['attended_count'],
                    $row['absent_count'],
                    $row['upcoming_booked'],
                    $row['unbooked_upcoming'],
                    $row['attendance_rate'] ?? '-',
                    $row['status_label'],
                    $row['attention_reason'] ?? '-',
                ], ';', '"', '');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function buildRows(Request $request, AttendanceService $attendanceService): array
    {
        $courseId = $request->integer('course') ?: null;
        $query = ChildProfile::query()
            ->with([
                'user','interests','enrollments.learningPath.instructor',
                'enrollments.learningPath.classSessions','classBookings.classSession.learningPath',
            ])
            ->whereHas('user')
            ->whereHas('enrollments', function ($enrollmentQuery) use ($courseId) {
                $enrollmentQuery->where('status', 'active');
                if ($courseId) $enrollmentQuery->where('learning_path_id', $courseId);
            });

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('name', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($ageGroup = $request->input('age_group')) {
            [$min,$max] = match ($ageGroup) {
                '5_7' => [5,7], '8_10' => [8,10], default => [11,14],
            };
            $query->whereBetween('age', [$min,$max]);
        }

        $allRows = $query->get()->map(fn ($child) => $attendanceService->summarize($child, $courseId));
        $rated = $allRows->whereNotNull('attendance_rate');
        $stats = [
            'students' => $allRows->count(),
            'average_attendance' => $rated->isNotEmpty() ? (int) round($rated->avg('attendance_rate')) : 0,
            'active' => $allRows->where('status', 'active')->count(),
            'needs_attention' => $allRows->where('status', 'needs_attention')->count(),
            'completed' => $allRows->where('status', 'completed')->count(),
        ];

        $rows = $request->filled('status') ? $allRows->where('status', $request->status)->values() : $allRows;
        $rows = match ($request->input('sort', 'name')) {
            'attendance_low' => $rows->sortBy(fn ($row) => $row['attendance_rate'] ?? -1),
            'attendance_high' => $rows->sortByDesc(fn ($row) => $row['attendance_rate'] ?? -1),
            'recent' => $rows->sortByDesc(fn ($row) => $row['last_session_at']?->timestamp ?? 0),
            default => $rows->sortBy(fn ($row) => mb_strtolower($row['child']->name)),
        };

        return [$rows->values(), $stats];
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'q'=>['nullable','string','max:100'],
            'course'=>['nullable','integer','exists:learning_paths,id'],
            'age_group'=>['nullable','in:5_7,8_10,11_14'],
            'status'=>['nullable','in:active,completed,needs_attention,not_scheduled'],
            'sort'=>['nullable','in:name,attendance_low,attendance_high,recent'],
        ]);
    }
}
