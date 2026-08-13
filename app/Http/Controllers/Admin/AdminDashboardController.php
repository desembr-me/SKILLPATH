<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\ClassSession;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Order;
use App\Models\SessionBooking;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'courses' => LearningPath::count(),
            'published_courses' => LearningPath::where('is_published', true)->count(),
            'students' => User::where('role', 'parent')->count(),
            'student_profiles' => ChildProfile::whereHas('enrollments', fn ($query) => $query->where('status','active'))->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'categories' => Category::count(),
            'enrollments' => Enrollment::where('status','active')->count(),
            'revenue' => (float) Order::where('payment_status','paid')->sum('total'),
            'revenue_this_month' => (float) Order::where('payment_status','paid')
                ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'teaching_today' => ClassSession::whereDate('starts_at', today())->where('status','!=','cancelled')->count(),
            'upcoming_teaching' => ClassSession::where('starts_at','>=',now())->where('status','scheduled')->count(),
            'pending_reviews' => CourseReview::where('is_approved', false)->count(),
            'certificates' => Certificate::where('status','active')->count(),
            'active_students_30d' => SessionBooking::where('status','attended')
                ->where(function ($query) {
                    $query->where('checked_in_at','>=',now()->subDays(30))
                        ->orWhereHas('classSession', fn ($session) => $session->where('starts_at','>=',now()->subDays(30)));
                })
                ->distinct()->count('child_profile_id'),
            'recycle_bin' => LearningPath::onlyTrashed()->count()
                + Category::onlyTrashed()->count()
                + User::onlyTrashed()->count()
                + CourseReview::onlyTrashed()->count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $popularCourses = LearningPath::withCount('enrollments')->with('instructor')->orderByDesc('enrollments_count')->take(5)->get();
        $nextSchedules = ClassSession::with(['learningPath','instructor'])
            ->where('starts_at','>=',now())->where('status','scheduled')->orderBy('starts_at')->take(5)->get();

        return view('admin.dashboard', compact('stats','recentOrders','popularCourses','nextSchedules'));
    }
}
