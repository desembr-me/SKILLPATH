<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminRevenueReportController extends Controller
{
    public function index(Request $request)
    {
        $this->validateFilters($request);
        [$from, $to] = $this->resolveRange($request);

        $itemsQuery = $this->baseItemQuery($request, $from, $to);

        $summary = $this->buildSummary($itemsQuery);

        [$previousFrom, $previousTo] = $this->previousRange($from, $to);
        $previousSummary = $this->buildSummary(
            $this->baseItemQuery($request, $previousFrom, $previousTo)
        );

        $summary['previous_revenue'] = $previousSummary['revenue'];
        $summary['revenue_change'] = $previousSummary['revenue'] > 0
            ? round(
                (($summary['revenue'] - $previousSummary['revenue'])
                    / $previousSummary['revenue']) * 100,
                1
            )
            : ($summary['revenue'] > 0 ? 100 : 0);

        $summary['discount_rate'] = $summary['gross'] > 0
            ? round(($summary['discount'] / $summary['gross']) * 100, 1)
            : 0;

        $transactions = (clone $itemsQuery)
            ->select([
                'order_items.*',
                'orders.order_number',
                'orders.payment_method',
                'orders.paid_at',
                'users.name as buyer_name',
                'learning_paths.title as course_title',
                'instructors.name as instructor_name',
            ])
            ->orderByDesc('orders.paid_at')
            ->paginate(15)
            ->withQueryString();

        $courseRevenue = (clone $itemsQuery)
            ->selectRaw(
                'order_items.learning_path_id, '
                .'COALESCE(MAX(learning_paths.title), MAX(order_items.title_snapshot)) as course_title, '
                .'COUNT(order_items.id) as items_sold, '
                .'SUM(order_items.final_price) as revenue, '
                .'SUM(order_items.discount) as discount'
            )
            ->groupBy('order_items.learning_path_id')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        $instructorRevenue = (clone $itemsQuery)
            ->selectRaw(
                'learning_paths.instructor_id, '
                .'COALESCE(instructors.name, "Tidak tersedia") as instructor_name, '
                .'COUNT(order_items.id) as items_sold, '
                .'SUM(order_items.final_price) as revenue'
            )
            ->groupBy('learning_paths.instructor_id', 'instructors.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        $paymentMethods = (clone $itemsQuery)
            ->selectRaw(
                'orders.payment_method, '
                .'COUNT(DISTINCT orders.id) as orders_count, '
                .'SUM(order_items.final_price) as revenue'
            )
            ->groupBy('orders.payment_method')
            ->orderByDesc('revenue')
            ->get();

        $trend = $this->buildTrend($request, $from, $to);
        $maxTrendRevenue = max(1, (float) $trend->max('revenue'));

        $courses = LearningPath::withTrashed()
            ->orderBy('title')
            ->get(['id', 'title']);

        $instructors = User::withTrashed()
            ->where('role', 'instructor')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.revenue.index', compact(
            'summary',
            'transactions',
            'courseRevenue',
            'instructorRevenue',
            'paymentMethods',
            'trend',
            'maxTrendRevenue',
            'courses',
            'instructors',
            'from',
            'to',
            'previousFrom',
            'previousTo',
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $this->validateFilters($request);
        [$from, $to] = $this->resolveRange($request);

        $rows = $this->baseItemQuery($request, $from, $to)
            ->select([
                'orders.order_number',
                'orders.paid_at',
                'orders.payment_method',
                'users.name as buyer_name',
                'users.email as buyer_email',
                'order_items.title_snapshot',
                'instructors.name as instructor_name',
                'order_items.price',
                'order_items.discount',
                'order_items.final_price',
            ])
            ->orderBy('orders.paid_at')
            ->get();

        $filename = 'laporan-pendapatan-'
            .$from->format('Ymd')
            .'-'
            .$to->format('Ymd')
            .'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nomor Pesanan',
                'Tanggal Bayar',
                'Pembeli',
                'Email',
                'Course',
                'Pengajar',
                'Metode Pembayaran',
                'Harga Normal',
                'Diskon',
                'Pendapatan',
            ], ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->order_number,
                    Carbon::parse($row->paid_at)->format('Y-m-d H:i'),
                    $row->buyer_name,
                    $row->buyer_email,
                    $row->title_snapshot,
                    $row->instructor_name ?? 'Tidak tersedia',
                    $row->payment_method,
                    $row->price,
                    $row->discount,
                    $row->final_price,
                ], ';', '"', '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildSummary($itemsQuery): array
    {
        $summary = [
            'revenue' => (float) (clone $itemsQuery)
                ->sum('order_items.final_price'),
            'gross' => (float) (clone $itemsQuery)
                ->sum('order_items.price'),
            'discount' => (float) (clone $itemsQuery)
                ->sum('order_items.discount'),
            'items_sold' => (int) (clone $itemsQuery)
                ->count('order_items.id'),
            'paid_orders' => (int) (clone $itemsQuery)
                ->distinct()
                ->count('orders.id'),
        ];

        $summary['average_order'] = $summary['paid_orders'] > 0
            ? $summary['revenue'] / $summary['paid_orders']
            : 0;

        return $summary;
    }

    private function baseItemQuery(Request $request, Carbon $from, Carbon $to)
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin(
                'learning_paths',
                'learning_paths.id',
                '=',
                'order_items.learning_path_id'
            )
            ->leftJoin(
                'users as instructors',
                'instructors.id',
                '=',
                'learning_paths.instructor_id'
            )
            ->where('orders.payment_status', 'paid')
            ->whereNotNull('orders.paid_at')
            ->whereBetween('orders.paid_at', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ]);

        if ($request->filled('course_id')) {
            $query->where(
                'order_items.learning_path_id',
                $request->integer('course_id')
            );
        }

        if ($request->filled('instructor_id')) {
            $query->where(
                'learning_paths.instructor_id',
                $request->integer('instructor_id')
            );
        }

        if ($request->filled('payment_method')) {
            $query->where(
                'orders.payment_method',
                $request->payment_method
            );
        }

        return $query;
    }

    private function buildTrend(Request $request, Carbon $from, Carbon $to)
    {
        $days = $from->diffInDays($to);
        $query = $this->baseItemQuery($request, $from, $to);

        if ($days <= 45) {
            return $query
                ->selectRaw(
                    'DATE(orders.paid_at) as period, '
                    .'SUM(order_items.final_price) as revenue'
                )
                ->groupByRaw('DATE(orders.paid_at)')
                ->orderBy('period')
                ->get()
                ->map(fn ($row) => [
                    'label' => Carbon::parse($row->period)->format('d M'),
                    'period' => $row->period,
                    'revenue' => (float) $row->revenue,
                ]);
        }

        return $query
            ->selectRaw(
                'DATE_FORMAT(orders.paid_at, "%Y-%m") as period, '
                .'SUM(order_items.final_price) as revenue'
            )
            ->groupByRaw('DATE_FORMAT(orders.paid_at, "%Y-%m")')
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => [
                'label' => Carbon::parse($row->period.'-01')
                    ->translatedFormat('M Y'),
                'period' => $row->period,
                'revenue' => (float) $row->revenue,
            ]);
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'course_id' => ['nullable', 'integer', 'exists:learning_paths,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'payment_method' => [
                'nullable',
                'in:qris,virtual_account,ewallet,bank_transfer'
            ],
        ]);
    }

    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('Y-m-d', $request->from)
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::createFromFormat('Y-m-d', $request->to)
            : now();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [
            $from->copy()->startOfDay(),
            $to->copy()->endOfDay(),
        ];
    }

    private function previousRange(Carbon $from, Carbon $to): array
    {
        $days = $from->diffInDays($to) + 1;

        $previousTo = $from->copy()->subDay()->endOfDay();
        $previousFrom = $previousTo->copy()
            ->subDays($days - 1)
            ->startOfDay();

        return [$previousFrom, $previousTo];
    }
}
