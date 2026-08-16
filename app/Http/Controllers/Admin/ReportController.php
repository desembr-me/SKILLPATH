<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = (int) Carbon::now()->year;
        $yearInput = $request->query('year');
        $selectedYear = ($yearInput !== null && $yearInput !== '') ? (int) $yearInput : $currentYear;

        // Fetch available years from transactions in DB + last 4 calendar years
        $availableYears = $this->getAvailableYears($currentYear);

        // Ensure selected year is in available list
        if (!in_array($selectedYear, $availableYears)) {
            $availableYears[] = $selectedYear;
            rsort($availableYears);
        }

        // Query transactions for the selected year strictly
        $paidTransactions = Transaction::with(['parent', 'enrollments.child', 'enrollments.course.instructor', 'enrollment.child', 'enrollment.course.instructor'])
            ->where('status', 'paid')
            ->where(function ($q) use ($selectedYear) {
                $q->whereYear('paid_at', $selectedYear)
                  ->orWhere(function ($sub) use ($selectedYear) {
                      $sub->whereNull('paid_at')
                          ->whereYear('created_at', $selectedYear);
                  });
            })
            ->latest('paid_at')
            ->get();

        $grossRevenue = (float) $paidTransactions->sum('total');
        $platformShare = round($grossRevenue * 0.20); // 20% platform profit
        $mentorShare = round($grossRevenue * 0.80);   // 80% mentor revenue

        // Group by month
        $monthlyReport = $this->buildMonthlyReport($paidTransactions);

        return view('admin.reports.index', [
            'grossRevenue' => $grossRevenue,
            'platformShare' => $platformShare,
            'mentorShare' => $mentorShare,
            'totalOrders' => $paidTransactions->count(),
            'monthlyReport' => $monthlyReport,
            'transactions' => $paidTransactions,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
        ]);
    }

    public function export(Request $request)
    {
        $currentYear = (int) Carbon::now()->year;
        $yearInput = $request->query('year');
        $selectedYear = ($yearInput !== null && $yearInput !== '') ? (int) $yearInput : $currentYear;

        $paidTransactions = Transaction::with(['parent', 'enrollments.child', 'enrollments.course.instructor', 'enrollment.child', 'enrollment.course.instructor'])
            ->where('status', 'paid')
            ->where(function ($q) use ($selectedYear) {
                $q->whereYear('paid_at', $selectedYear)
                  ->orWhere(function ($sub) use ($selectedYear) {
                      $sub->whereNull('paid_at')
                          ->whereYear('created_at', $selectedYear);
                  });
            })
            ->latest('paid_at')
            ->get();

        $grossRevenue = (float) $paidTransactions->sum('total');
        $platformShare = round($grossRevenue * 0.20);
        $mentorShare = round($grossRevenue * 0.80);

        $monthlyReport = $this->buildMonthlyReport($paidTransactions);

        $filename = 'Laporan_Pendapatan_SkillPath_' . $selectedYear . '_' . date('Ymd_His') . '.xls';

        $content = view('admin.reports.excel', [
            'year' => $selectedYear,
            'grossRevenue' => $grossRevenue,
            'platformShare' => $platformShare,
            'mentorShare' => $mentorShare,
            'totalOrders' => $paidTransactions->count(),
            'monthlyReport' => $monthlyReport,
            'transactions' => $paidTransactions,
            'generatedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    private function getAvailableYears(int $currentYear): array
    {
        $dbYearsPaid = Transaction::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->selectRaw('DISTINCT YEAR(paid_at) as yr')
            ->pluck('yr')
            ->map(fn($y) => (int)$y)
            ->toArray();

        $dbYearsCreated = Transaction::where('status', 'paid')
            ->selectRaw('DISTINCT YEAR(created_at) as yr')
            ->pluck('yr')
            ->map(fn($y) => (int)$y)
            ->toArray();

        $defaults = [$currentYear, $currentYear - 1, $currentYear - 2, $currentYear - 3];
        $all = array_unique(array_merge($defaults, $dbYearsPaid, $dbYearsCreated));
        rsort($all);

        return array_values($all);
    }

    private function buildMonthlyReport($paidTransactions): array
    {
        $indonesianMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $monthlyReport = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTrx = $paidTransactions->filter(function ($trx) use ($m) {
                $date = $trx->paid_at ?? $trx->created_at;
                return (int) $date->format('n') === $m;
            });

            $mGross = (float) $monthTrx->sum('total');
            $monthlyReport[] = [
                'month_num' => $m,
                'month_name' => $indonesianMonths[$m],
                'orders_count' => $monthTrx->count(),
                'gross' => $mGross,
                'platform_profit' => round($mGross * 0.20),
                'mentor_payout' => round($mGross * 0.80),
            ];
        }

        return $monthlyReport;
    }
}
