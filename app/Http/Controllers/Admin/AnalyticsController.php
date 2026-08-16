<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\User;

class AnalyticsController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['courses', 'mentors'])->get();
        $totalCourses = Course::count() ?: 1;

        $categoryStats = $categories->map(function ($cat) use ($totalCourses) {
            $enrollmentsCount = Enrollment::whereHas('course', function ($q) use ($cat) {
                $q->where('category_id', $cat->id);
            })->count();

            return [
                'name' => $cat->name,
                'slug' => $cat->slug,
                'courses_count' => $cat->courses_count,
                'mentors_count' => $cat->mentors_count,
                'enrollments_count' => $enrollmentsCount,
                'course_percent' => round(($cat->courses_count / $totalCourses) * 100),
            ];
        });

        $totalUsers = User::count();
        $totalTransactions = Transaction::count() ?: 1;
        $paidTransactions = Transaction::where('status', 'paid')->count();
        $conversionRate = round(($paidTransactions / $totalTransactions) * 100);

        return view('admin.analytics.index', [
            'categoryStats' => $categoryStats,
            'totalUsers' => $totalUsers,
            'conversionRate' => $conversionRate,
            'totalTransactions' => $totalTransactions,
            'paidTransactions' => $paidTransactions,
        ]);
    }
}
