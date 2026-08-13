<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Progress;
use App\Models\SessionBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlatformStatisticsService
{
    public function build(string $period = '30d'): array
    {
        [$from, $to, $periodLabel] = $this->resolveRange($period);

        $paidOrders = Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to]);

        $activeStudentIds = Progress::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->distinct()
            ->pluck('child_profile_id');

        $enrollmentCount = Enrollment::count();
        $activeCertificates = Certificate::where('status', 'active')->count();

        $metrics = [
            'users' => User::count(),
            'parents' => User::where('role', 'parent')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'children' => ChildProfile::count(),
            'published_courses' => LearningPath::where('is_published', true)->count(),
            'enrollments' => $enrollmentCount,
            'active_students_period' => $activeStudentIds->count(),
            'completed_activities_period' => Progress::query()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$from, $to])
                ->count(),
            'revenue_period' => (float) (clone $paidOrders)->sum('total'),
            'paid_orders_period' => (clone $paidOrders)->count(),
            'certificates_active' => $activeCertificates,
            'certificates_period' => Certificate::query()
                ->whereBetween('issued_at', [$from, $to])
                ->count(),
            'average_rating' => round((float) CourseReview::query()
                ->where('is_approved', true)
                ->avg('rating'), 2),
            'completion_rate' => $enrollmentCount > 0
                ? round(($activeCertificates / $enrollmentCount) * 100, 1)
                : 0,
        ];

        $trend = $this->buildTrend($from, $to);

        $ageDistribution = collect([
            [
                'label' => '5–7 tahun',
                'count' => ChildProfile::whereBetween('age', [5, 7])->count(),
            ],
            [
                'label' => '8–10 tahun',
                'count' => ChildProfile::whereBetween('age', [8, 10])->count(),
            ],
            [
                'label' => '11–14 tahun',
                'count' => ChildProfile::whereBetween('age', [11, 14])->count(),
            ],
        ]);

        $maxAgeCount = max(1, (int) $ageDistribution->max('count'));

        $categoryPopularity = DB::table('categories')
            ->leftJoin(
                'category_learning_path',
                'categories.id',
                '=',
                'category_learning_path.category_id'
            )
            ->leftJoin(
                'learning_paths',
                'category_learning_path.learning_path_id',
                '=',
                'learning_paths.id'
            )
            ->leftJoin(
                'enrollments',
                'learning_paths.id',
                '=',
                'enrollments.learning_path_id'
            )
            ->whereNull('categories.deleted_at')
            ->where(function ($query) {
                $query->whereNull('learning_paths.deleted_at')
                    ->orWhereNull('learning_paths.id');
            })
            ->selectRaw(
                'categories.id, categories.name, categories.icon, '
                .'COUNT(enrollments.id) as enrollment_count'
            )
            ->groupBy('categories.id', 'categories.name', 'categories.icon')
            ->orderByDesc('enrollment_count')
            ->get();

        $maxCategoryEnrollments = max(
            1,
            (int) $categoryPopularity->max('enrollment_count')
        );

        $topCourses = LearningPath::query()
            ->with('instructor')
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('enrollments_count')
            ->take(8)
            ->get();

        $topInstructors = User::query()
            ->where('role', 'instructor')
            ->withCount([
                'coursesTaught as course_count',
                'coursesTaught as enrollment_count' => function ($courseQuery) {
                    $courseQuery->join(
                        'enrollments',
                        'learning_paths.id',
                        '=',
                        'enrollments.learning_path_id'
                    );
                },
            ])
            ->with('instructorProfile')
            ->orderByDesc('enrollment_count')
            ->take(8)
            ->get();

        $funnel = collect([
            [
                'label' => 'Profil anak',
                'value' => ChildProfile::count(),
            ],
            [
                'label' => 'Pernah enrollment',
                'value' => Enrollment::query()
                    ->distinct()
                    ->count('child_profile_id'),
            ],
            [
                'label' => 'Mulai aktivitas',
                'value' => Progress::query()
                    ->where('status', 'completed')
                    ->distinct()
                    ->count('child_profile_id'),
            ],
            [
                'label' => 'Memiliki sertifikat',
                'value' => Certificate::query()
                    ->where('status', 'active')
                    ->distinct()
                    ->count('child_profile_id'),
            ],
        ]);

        $maxFunnel = max(1, (int) $funnel->max('value'));

        $liveCapacity = LiveSession::query()
            ->whereBetween('starts_at', [$from, $to])
            ->sum('capacity');

        $liveBookings = SessionBooking::query()
            ->whereHas('liveSession', function ($query) use ($from, $to) {
                $query->whereBetween('starts_at', [$from, $to]);
            })
            ->where('status', 'booked')
            ->count();

        $engagement = [
            'live_sessions' => LiveSession::query()
                ->whereBetween('starts_at', [$from, $to])
                ->count(),
            'live_bookings' => $liveBookings,
            'live_fill_rate' => $liveCapacity > 0
                ? round(($liveBookings / $liveCapacity) * 100, 1)
                : 0,
            'approved_reviews' => CourseReview::query()
                ->where('is_approved', true)
                ->whereBetween('created_at', [$from, $to])
                ->count(),
            'new_users' => User::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),
            'new_enrollments' => Enrollment::query()
                ->whereBetween('enrolled_at', [$from, $to])
                ->count(),
        ];

        return compact(
            'period',
            'periodLabel',
            'from',
            'to',
            'metrics',
            'trend',
            'ageDistribution',
            'maxAgeCount',
            'categoryPopularity',
            'maxCategoryEnrollments',
            'topCourses',
            'topInstructors',
            'funnel',
            'maxFunnel',
            'engagement'
        );
    }

    private function resolveRange(string $period): array
    {
        $to = now()->endOfDay();

        return match ($period) {
            '7d' => [
                now()->subDays(6)->startOfDay(),
                $to,
                '7 hari terakhir',
            ],
            '90d' => [
                now()->subDays(89)->startOfDay(),
                $to,
                '90 hari terakhir',
            ],
            '12m' => [
                now()->subMonths(11)->startOfMonth(),
                $to,
                '12 bulan terakhir',
            ],
            'all' => [
                $this->platformStartDate(),
                $to,
                'Sejak platform digunakan',
            ],
            default => [
                now()->subDays(29)->startOfDay(),
                $to,
                '30 hari terakhir',
            ],
        };
    }

    private function platformStartDate(): Carbon
    {
        $dates = collect([
            User::min('created_at'),
            Enrollment::min('enrolled_at'),
            Order::whereNotNull('paid_at')->min('paid_at'),
            Progress::whereNotNull('completed_at')->min('completed_at'),
        ])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));

        return $dates->isNotEmpty()
            ? $dates->min()->copy()->startOfDay()
            : now()->subDays(29)->startOfDay();
    }

    private function buildTrend(Carbon $from, Carbon $to): array
    {
        $monthly = $from->diffInDays($to) > 120;

        $buckets = collect();
        $cursor = $monthly
            ? $from->copy()->startOfMonth()
            : $from->copy()->startOfDay();

        while ($cursor->lte($to)) {
            $key = $monthly
                ? $cursor->format('Y-m')
                : $cursor->format('Y-m-d');

            $buckets->put($key, [
                'key' => $key,
                'label' => $monthly
                    ? $cursor->translatedFormat('M Y')
                    : $cursor->format('d M'),
                'users' => 0,
                'enrollments' => 0,
                'activities' => 0,
                'revenue' => 0.0,
            ]);

            $cursor = $monthly
                ? $cursor->addMonth()
                : $cursor->addDay();
        }

        $keyFor = fn ($date) => $monthly
            ? Carbon::parse($date)->format('Y-m')
            : Carbon::parse($date)->format('Y-m-d');

        User::query()
            ->whereBetween('created_at', [$from, $to])
            ->get(['created_at'])
            ->each(function ($user) use (&$buckets, $keyFor) {
                $key = $keyFor($user->created_at);
                if ($buckets->has($key)) {
                    $item = $buckets->get($key);
                    $item['users']++;
                    $buckets->put($key, $item);
                }
            });

        Enrollment::query()
            ->whereBetween('enrolled_at', [$from, $to])
            ->get(['enrolled_at'])
            ->each(function ($enrollment) use (&$buckets, $keyFor) {
                $key = $keyFor($enrollment->enrolled_at);
                if ($buckets->has($key)) {
                    $item = $buckets->get($key);
                    $item['enrollments']++;
                    $buckets->put($key, $item);
                }
            });

        Progress::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->get(['completed_at'])
            ->each(function ($progress) use (&$buckets, $keyFor) {
                $key = $keyFor($progress->completed_at);
                if ($buckets->has($key)) {
                    $item = $buckets->get($key);
                    $item['activities']++;
                    $buckets->put($key, $item);
                }
            });

        Order::query()
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to])
            ->get(['paid_at', 'total'])
            ->each(function ($order) use (&$buckets, $keyFor) {
                $key = $keyFor($order->paid_at);
                if ($buckets->has($key)) {
                    $item = $buckets->get($key);
                    $item['revenue'] += (float) $order->total;
                    $buckets->put($key, $item);
                }
            });

        $points = $buckets->values();

        return [
            'points' => $points,
            'monthly' => $monthly,
            'max_users' => max(1, (int) $points->max('users')),
            'max_enrollments' => max(1, (int) $points->max('enrollments')),
            'max_activities' => max(1, (int) $points->max('activities')),
            'max_revenue' => max(1, (float) $points->max('revenue')),
        ];
    }
}
