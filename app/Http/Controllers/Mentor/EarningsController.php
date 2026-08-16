<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courses = $user->courses()->with('category')->get();
        $courseIds = $courses->pluck('id');

        $transactions = Transaction::whereHas('enrollment', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })
        ->where('status', 'paid')
        ->with(['parent', 'enrollment.child', 'enrollment.course.category', 'enrollment.schedule'])
        ->latest('paid_at')
        ->get();

        $totalEarnings = $transactions->sum('subtotal');
        $thisMonthEarnings = $transactions->filter(fn ($t) => $t->paid_at && $t->paid_at->isCurrentMonth())->sum('subtotal');
        $paidStudentsCount = $transactions->pluck('enrollment.child_id')->unique()->count();
        $totalTransactionsCount = $transactions->count();
        $avgPerTransaction = $totalTransactionsCount > 0 ? round($totalEarnings / $totalTransactionsCount) : 0;

        // Breakdown per course
        $courseBreakdown = $courses->map(function ($course) use ($transactions) {
            $courseTx = $transactions->filter(fn ($t) => $t->enrollment && $t->enrollment->course_id === $course->id);
            return [
                'course' => $course,
                'total_income' => $courseTx->sum('subtotal'),
                'transactions_count' => $courseTx->count(),
            ];
        });

        return view('mentor.earnings', [
            'transactions' => $transactions,
            'courses' => $courses,
            'totalEarnings' => $totalEarnings,
            'thisMonthEarnings' => $thisMonthEarnings,
            'paidStudentsCount' => $paidStudentsCount,
            'totalTransactionsCount' => $totalTransactionsCount,
            'avgPerTransaction' => $avgPerTransaction,
            'courseBreakdown' => $courseBreakdown,
        ]);
    }
}
