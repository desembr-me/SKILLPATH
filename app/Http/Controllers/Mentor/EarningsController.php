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

        $transactions = Transaction::where(function ($query) use ($courseIds) {
            $query->whereHas('enrollments', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })->orWhereHas('enrollment', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            });
        })
        ->where('status', 'paid')
        ->with([
            'parent',
            'enrollments.child',
            'enrollments.course.category',
            'enrollments.schedule',
            'enrollment.child',
            'enrollment.course.category',
            'enrollment.schedule'
        ])
        ->latest('paid_at')
        ->get();

        $totalEarnings = $transactions->sum('subtotal');
        $thisMonthEarnings = $transactions->filter(fn ($t) => $t->paid_at && $t->paid_at->isCurrentMonth())->sum('subtotal');
        $paidStudentsCount = $transactions->flatMap(fn ($t) => $t->all_enrollments->pluck('child_id'))->unique()->count();
        $totalTransactionsCount = $transactions->count();
        $avgPerTransaction = $totalTransactionsCount > 0 ? round($totalEarnings / $totalTransactionsCount) : 0;

        // Breakdown per course
        $courseBreakdown = $courses->map(function ($course) use ($transactions) {
            $courseTx = $transactions->filter(function ($t) use ($course) {
                return $t->all_enrollments->contains('course_id', $course->id);
            });
            $totalIncome = $courseTx->sum(function ($t) use ($course) {
                $enrs = $t->all_enrollments->where('course_id', $course->id);
                $sum = $enrs->sum(fn ($e) => (float) ($e->package_info['price'] ?? 0));
                return $sum ?: (float) $t->subtotal;
            });
            return [
                'course' => $course,
                'total_income' => $totalIncome,
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
