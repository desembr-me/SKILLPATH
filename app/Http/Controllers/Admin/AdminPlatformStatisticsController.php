<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlatformStatisticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPlatformStatisticsController extends Controller
{
    public function index(
        Request $request,
        PlatformStatisticsService $statisticsService
    ) {
        $data = $this->validated($request);
        $report = $statisticsService->build($data['period'] ?? '30d');

        return view('admin.statistics.index', $report);
    }

    public function export(
        Request $request,
        PlatformStatisticsService $statisticsService
    ): StreamedResponse {
        $data = $this->validated($request);
        $report = $statisticsService->build($data['period'] ?? '30d');

        $filename = 'statistik-platform-skillpath-'
            .now()->format('Y-m-d-His')
            .'.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['STATISTIK PLATFORM SKILLPATH'], ';', '"', '');
            fputcsv($handle, ['Periode', $report['periodLabel']], ';', '"', '');
            fputcsv($handle, [
                'Rentang',
                $report['from']->format('Y-m-d').' s.d. '.$report['to']->format('Y-m-d'),
            ], ';', '"', '');
            fputcsv($handle, [], ';', '"', '');

            fputcsv($handle, ['METRIK UTAMA', 'NILAI'], ';', '"', '');
            foreach ($report['metrics'] as $key => $value) {
                fputcsv($handle, [$key, $value], ';', '"', '');
            }

            fputcsv($handle, [], ';', '"', '');
            fputcsv($handle, ['FUNNEL SISWA', 'JUMLAH'], ';', '"', '');
            foreach ($report['funnel'] as $item) {
                fputcsv($handle, [$item['label'], $item['value']], ';', '"', '');
            }

            fputcsv($handle, [], ';', '"', '');
            fputcsv($handle, ['DISTRIBUSI USIA', 'JUMLAH'], ';', '"', '');
            foreach ($report['ageDistribution'] as $item) {
                fputcsv($handle, [$item['label'], $item['count']], ';', '"', '');
            }

            fputcsv($handle, [], ';', '"', '');
            fputcsv($handle, [
                'KATEGORI',
                'ENROLLMENT',
            ], ';', '"', '');
            foreach ($report['categoryPopularity'] as $category) {
                fputcsv($handle, [
                    $category->name,
                    $category->enrollment_count,
                ], ';', '"', '');
            }

            fputcsv($handle, [], ';', '"', '');
            fputcsv($handle, [
                'KELAS TERPOPULER',
                'ENROLLMENT',
                'RATING',
            ], ';', '"', '');
            foreach ($report['topCourses'] as $course) {
                fputcsv($handle, [
                    $course->title,
                    $course->enrollments_count,
                    round((float) ($course->reviews_avg_rating ?? 0), 2),
                ], ';', '"', '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'period' => ['nullable', 'in:7d,30d,90d,12m,all'],
        ]);
    }
}
