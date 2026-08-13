<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorRevenueController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'instructor', 403);
        $courseIds = $request->user()->coursesTaught()->pluck('id');

        $totalRevenue = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.learning_path_id', $courseIds)
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.final_price');

        $revenueByCourse = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('learning_paths', 'learning_paths.id', '=', 'order_items.learning_path_id')
            ->whereIn('order_items.learning_path_id', $courseIds)
            ->where('orders.payment_status', 'paid')
            ->select('learning_paths.title', DB::raw('SUM(order_items.final_price) as revenue'), DB::raw('COUNT(*) as sales'))
            ->groupBy('learning_paths.id', 'learning_paths.title')
            ->orderByDesc('revenue')
            ->get();

        $monthlyRevenue = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.learning_path_id', $courseIds)
            ->where('orders.payment_status', 'paid')
            ->selectRaw('MONTH(orders.paid_at) as month, YEAR(orders.paid_at) as year, SUM(order_items.final_price) as total')
            ->groupBy('year', 'month')
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(12)
            ->get();

        $recentSales = OrderItem::whereIn('learning_path_id', $courseIds)
            ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
            ->with(['order.user'])
            ->latest()
            ->take(20)
            ->get();

        return view('instructor.revenue.index', compact('totalRevenue', 'revenueByCourse', 'monthlyRevenue', 'recentSales'));
    }
}
