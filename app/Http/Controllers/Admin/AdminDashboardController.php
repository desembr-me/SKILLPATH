<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Progress;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'courses' => LearningPath::count(),

            'published_courses' => LearningPath::where(
                'is_published',
                true
            )->count(),

            'students' => User::where(
                'role',
                'parent'
            )->count(),

            'student_profiles' => ChildProfile::whereHas(
                'enrollments',
                function ($query) {
                    $query->where('status', 'active');
                }
            )->count(),

            'instructors' => User::where(
                'role',
                'instructor'
            )->count(),

            'orders' => Order::count(),

            'paid_orders' => Order::where(
                'payment_status',
                'paid'
            )->count(),

            'enrollments' => Enrollment::where(
                'status',
                'active'
            )->count(),

            'categories' => Category::count(),

            'revenue' => (float) Order::where(
                'payment_status',
                'paid'
            )->sum('total'),

            'revenue_this_month' => (float) Order::where(
                'payment_status',
                'paid'
            )
                ->whereBetween('paid_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->sum('total'),

            'teaching_today' => LiveSession::whereDate(
                'starts_at',
                today()
            )->count(),

            'upcoming_teaching' => LiveSession::where(
                'starts_at',
                '>=',
                now()
            )
                ->whereIn('status', [
                    'scheduled',
                    'live',
                ])
                ->count(),

            'pending_reviews' => CourseReview::where(
                'is_approved',
                false
            )->count(),

            // Statistik sertifikat
            'certificates' => Certificate::where(
                'status',
                'active'
            )->count(),

            // Siswa yang aktif belajar dalam 30 hari terakhir
            'active_students_30d' => Progress::where(
                'status',
                'completed'
            )
                ->where(
                    'completed_at',
                    '>=',
                    now()->subDays(30)
                )
                ->distinct()
                ->count('child_profile_id'),

            // Recycle Bin
            'recycle_bin' =>
                LearningPath::onlyTrashed()->count()
                + Category::onlyTrashed()->count()
                + User::onlyTrashed()->count()
                + CourseReview::onlyTrashed()->count(),
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

        $nextSchedules = LiveSession::query()
            ->with([
                'learningPath',
                'instructor',
            ])
            ->where(
                'starts_at',
                '>=',
                now()
            )
            ->whereIn('status', [
                'scheduled',
                'live',
            ])
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'stats',
                'recentOrders',
                'popularCourses',
                'nextSchedules'
            )
        );
    }
}