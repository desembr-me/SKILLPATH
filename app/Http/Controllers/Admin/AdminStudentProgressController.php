<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use App\Models\LearningPath;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStudentProgressController extends Controller
{
    public function index(Request $request, StudentProgressService $progressService)
    {
        $this->validateFilters($request);

        [$rows, $stats] = $this->buildRows($request, $progressService);

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $rows->forPage($page, $perPage)->values();

        $students = new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $courses = LearningPath::query()
            ->whereHas('enrollments')
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.progress.index', compact(
            'students',
            'stats',
            'courses'
        ));
    }

    public function show(
        ChildProfile $childProfile,
        StudentProgressService $progressService
    ) {
        $progressService->ensureLoaded($childProfile);

        $summary = $progressService->summarize($childProfile);
        $courseProgress = $progressService->courseBreakdown($childProfile);

        $recentActivities = $childProfile->progress
            ->where('status', 'completed')
            ->filter(fn ($progress) => $progress->activity && $progress->activity->module)
            ->sortByDesc('completed_at')
            ->take(20)
            ->values();

        return view('admin.progress.show', compact(
            'childProfile',
            'summary',
            'courseProgress',
            'recentActivities'
        ));
    }

    public function export(
        Request $request,
        StudentProgressService $progressService
    ): StreamedResponse {
        $this->validateFilters($request);

        [$rows] = $this->buildRows($request, $progressService);

        $filename = 'monitoring-progres-siswa-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nama Siswa',
                'Usia',
                'Kelompok Usia',
                'Orang Tua',
                'Email Orang Tua',
                'Course Aktif',
                'Aktivitas Selesai',
                'Total Aktivitas',
                'Aktivitas Tersisa',
                'Progres (%)',
                'Poin',
                'Rata-rata Nilai',
                'Aktivitas Terakhir',
                'Hari Tidak Aktif',
                'Status',
                'Catatan Monitoring',
            ], ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['child']->name,
                    $row['child']->age,
                    $this->ageGroupLabel($row['child']->age),
                    $row['child']->user?->name,
                    $row['child']->user?->email,
                    $row['enrollment_count'],
                    $row['completed_activities'],
                    $row['total_activities'],
                    $row['remaining_activities'],
                    $row['progress_percent'],
                    $row['points'],
                    $row['average_score'] ?? '-',
                    $row['last_activity_at']?->format('Y-m-d H:i') ?? '-',
                    $row['days_inactive'] ?? '-',
                    $row['status_label'],
                    $row['attention_reason'] ?? '-',
                ], ';', '"', '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildRows(
        Request $request,
        StudentProgressService $progressService
    ): array {
        $courseId = $request->integer('course') ?: null;

        $query = ChildProfile::query()
            ->with([
                'user',
                'interests',
                'enrollments.learningPath.instructor',
                'enrollments.learningPath.modules.activities',
                'progress.activity.module.learningPath',
            ])
            ->whereHas('user')
            ->whereHas('enrollments', function ($enrollmentQuery) use ($courseId) {
                $enrollmentQuery->where('status', 'active');

                if ($courseId) {
                    $enrollmentQuery->where('learning_path_id', $courseId);
                }
            });

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($ageGroup = $request->input('age_group')) {
            [$min, $max] = match ($ageGroup) {
                '5_7' => [5, 7],
                '8_10' => [8, 10],
                '11_14' => [11, 14],
            };

            $query->whereBetween('age', [$min, $max]);
        }

        $rowsBeforeStatusFilter = $query
            ->get()
            ->map(fn ($child) => $progressService->summarize($child, $courseId));

        $stats = [
            'students' => $rowsBeforeStatusFilter->count(),
            'average_progress' => $rowsBeforeStatusFilter->isNotEmpty()
                ? (int) round($rowsBeforeStatusFilter->avg('progress_percent'))
                : 0,
            'active' => $rowsBeforeStatusFilter->where('status', 'active')->count(),
            'needs_attention' => $rowsBeforeStatusFilter
                ->where('status', 'needs_attention')
                ->count(),
            'completed' => $rowsBeforeStatusFilter
                ->where('status', 'completed')
                ->count(),
            'not_started' => $rowsBeforeStatusFilter
                ->where('status', 'not_started')
                ->count(),
        ];

        $rows = $rowsBeforeStatusFilter;

        if ($status = $request->input('status')) {
            $rows = $rows->where('status', $status)->values();
        }

        $rows = match ($request->input('sort', 'name')) {
            'progress_low' => $rows->sortBy('progress_percent'),
            'progress_high' => $rows->sortByDesc('progress_percent'),
            'recent' => $rows->sortByDesc(
                fn ($row) => $row['last_activity_at']?->timestamp ?? 0
            ),
            'inactive' => $rows->sortByDesc(fn ($row) => $row['days_inactive'] ?? 0),
            default => $rows->sortBy(
                fn ($row) => mb_strtolower($row['child']->name)
            ),
        };

        return [$rows->values(), $stats];
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'course' => ['nullable', 'integer', 'exists:learning_paths,id'],
            'age_group' => ['nullable', 'in:5_7,8_10,11_14'],
            'status' => ['nullable', 'in:active,completed,needs_attention,not_started'],
            'sort' => ['nullable', 'in:name,progress_low,progress_high,recent,inactive'],
        ]);
    }

    private function ageGroupLabel(int $age): string
    {
        return match (true) {
            $age <= 7 => '5–7 tahun',
            $age <= 10 => '8–10 tahun',
            default => '11–14 tahun',
        };
    }
}
