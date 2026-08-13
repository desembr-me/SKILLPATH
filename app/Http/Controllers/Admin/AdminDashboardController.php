<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Order;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'courses' => LearningPath::count(),
            'published_courses' => LearningPath::where('is_published', true)->count(),
            'students' => User::where('role', 'parent')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'orders' => Order::count(),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'enrollments' => Enrollment::where('status', 'active')->count(),
            'categories' => Category::count(),
            'revenue' => (float) Order::where('payment_status', 'paid')->sum('total'),
            'pending_reviews' => CourseReview::where('is_approved', false)->count(),
            'recycle_bin' => LearningPath::onlyTrashed()->count() + Category::onlyTrashed()->count() + User::onlyTrashed()->count() + CourseReview::onlyTrashed()->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(6)
            ->get();

        $popularCourses = LearningPath::withCount('enrollments')
            ->with('instructor')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'popularCourses'));
    }
}
