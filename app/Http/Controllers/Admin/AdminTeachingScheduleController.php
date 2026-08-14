<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\LiveSession;
use App\Models\User;
use App\Services\ScheduleConflictService;
use App\Services\SessionCreditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTeachingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->validateFilters($request);

        $query = $this->filteredQuery($request)
            ->with([
                'learningPath' => fn ($courseQuery) => $courseQuery->withTrashed(),
                'instructor.instructorProfile',
            ])
            ->withCount([
                'bookings as booked_count' => fn ($bookingQuery) => $bookingQuery
                    ->where('status', 'booked'),
            ]);

        $schedules = $query
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        $occupancyRows = LiveSession::query()
            ->withCount([
                'bookings as booked_count' => fn ($bookingQuery) => $bookingQuery
                    ->where('status', 'booked'),
            ])
            ->whereIn('status', ['scheduled', 'live'])
            ->where('starts_at', '>=', now()->startOfDay())
            ->get();

        $avgOccupancy = $occupancyRows->isNotEmpty()
            ? round((float) $occupancyRows->avg(
                fn ($session) => $session->capacity > 0
                    ? min(100, ($session->booked_count / $session->capacity) * 100)
                    : 0
            ), 1)
            : 0;

        $stats = [
            'today' => LiveSession::whereDate('starts_at', today())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'live_now' => LiveSession::where('status', 'live')->count(),
            'upcoming' => LiveSession::where('starts_at', '>=', now())
                ->whereIn('status', ['scheduled', 'live'])
                ->count(),
            'avg_occupancy' => $avgOccupancy,
        ];

        $instructors = User::query()
            ->where('role', 'instructor')
            ->orderBy('name')
            ->get(['id', 'name']);

        $courses = LearningPath::query()
            ->with('instructor')
            ->orderBy('title')
            ->get(['id', 'title', 'instructor_id']);

        return view('admin.schedules.index', compact(
            'schedules',
            'stats',
            'instructors',
            'courses'
        ));
    }

    public function create()
    {
        $courses = LearningPath::query()
            ->where('is_published', true)
            ->whereNotNull('instructor_id')
            ->with('instructor')
            ->orderBy('title')
            ->get();

        return view('admin.schedules.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);
        $course = LearningPath::query()->findOrFail($data['learning_path_id']);

        abort_unless($course->instructor_id, 422, 'Kelas belum memiliki pengajar.');

        $this->ensureNoInstructorConflict(
            instructorId: $course->instructor_id,
            startsAt: $data['starts_at'],
            endsAt: $data['ends_at'],
        );

        LiveSession::create([
            'learning_path_id' => $course->id,
            'instructor_id' => $course->instructor_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'location' => $data['location'] ?? null,
            'meeting_url' => $data['meeting_url'] ?? null,
            'capacity' => $data['capacity'],
            'status' => $data['status'],
            'recording_url' => $data['recording_url'] ?? null,
        ]);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal pengajaran berhasil dibuat.');
    }

    public function edit(LiveSession $liveSession)
    {
        $liveSession->load([
            'learningPath' => fn ($query) => $query->withTrashed(),
            'instructor',
        ]);

        $courses = LearningPath::query()
            ->withTrashed()
            ->whereNotNull('instructor_id')
            ->with('instructor')
            ->orderBy('title')
            ->get();

        return view('admin.schedules.edit', compact(
            'liveSession',
            'courses'
        ));
    }

    public function update(
        Request $request,
        LiveSession $liveSession,
        ScheduleConflictService $conflictService,
        SessionCreditService $creditService
    ) {
        $data = $this->validateSchedule($request);
        $course = LearningPath::withTrashed()->findOrFail($data['learning_path_id']);

        abort_unless($course->instructor_id, 422, 'Kelas belum memiliki pengajar.');

        if ($data['status'] !== 'cancelled') {
            $this->ensureNoInstructorConflict(
                instructorId: $course->instructor_id,
                startsAt: $data['starts_at'],
                endsAt: $data['ends_at'],
                ignoreSessionId: $liveSession->id,
            );
        }

        DB::transaction(function () use ($liveSession, $course, $data, $creditService, $conflictService) {
            // Lock sesi sebelum memeriksa booking. Request booking siswa juga
            // mengunci baris sesi yang sama, sehingga perubahan jadwal tidak
            // dapat menyelinap di antara pemeriksaan bentrok dan penyimpanan.
            $lockedSession = LiveSession::query()
                ->whereKey($liveSession->id)
                ->lockForUpdate()
                ->firstOrFail();

            $activeBookings = $lockedSession->bookings()->where('status', 'booked')->count();

            if ($activeBookings > 0 && (int) $course->id !== (int) $lockedSession->learning_path_id) {
                throw ValidationException::withMessages([
                    'learning_path_id' => 'Kelas tidak dapat diganti karena sesi sudah memiliki booking aktif. Batalkan sesi terlebih dahulu agar peserta memperoleh kredit.',
                ]);
            }

            if ($data['status'] !== 'cancelled' && $activeBookings > (int) $data['capacity']) {
                throw ValidationException::withMessages([
                    'capacity' => 'Kapasitas tidak boleh lebih kecil dari jumlah peserta yang sudah booking ('.$activeBookings.').',
                ]);
            }

            if ($data['status'] !== 'cancelled') {
                $conflictedChildren = $conflictService->conflictingBookedChildrenForReschedule(
                    $lockedSession,
                    $data['starts_at'],
                    $data['ends_at']
                );

                if ($conflictedChildren->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'starts_at' => 'Perubahan jadwal akan membuat '.$conflictedChildren->count().' siswa memiliki jadwal bentrok. Pilih waktu lain atau batalkan sesi agar kredit pengganti dapat diberikan.',
                    ]);
                }
            }

            $lockedSession->update([
                'learning_path_id' => $course->id,
                'instructor_id' => $course->instructor_id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'location' => $data['location'] ?? null,
                'meeting_url' => $data['meeting_url'] ?? null,
                'capacity' => $data['capacity'],
                'status' => $data['status'],
                'recording_url' => $data['recording_url'] ?? null,
            ]);

            if ($data['status'] === 'cancelled') {
                $bookings = $lockedSession->bookings()
                    ->where('status', 'booked')
                    ->lockForUpdate()
                    ->get();

                foreach ($bookings as $booking) {
                    $creditService->creditBooking(
                        $booking,
                        'sesi_dibatalkan',
                        'Kredit otomatis karena jadwal dibatalkan oleh administrator.'
                    );
                }
            }
        });

        $message = $data['status'] === 'cancelled'
            ? 'Jadwal dibatalkan. Booking aktif otomatis dikonversi menjadi kredit sesi.'
            : 'Jadwal pengajaran berhasil diperbarui tanpa menimbulkan bentrok pada peserta.';

        return redirect()->route('admin.schedules.index')->with('success', $message);
    }

    public function cancel(LiveSession $liveSession, SessionCreditService $creditService)
    {
        if ($liveSession->status === 'completed') {
            return back()->withErrors([
                'schedule' => 'Jadwal yang sudah selesai tidak dapat dibatalkan.',
            ]);
        }

        if ($liveSession->status === 'cancelled') {
            return back()->with('success', 'Jadwal ini sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($liveSession, $creditService) {
            $lockedSession = LiveSession::query()->whereKey($liveSession->id)->lockForUpdate()->firstOrFail();
            $bookings = $lockedSession->bookings()->where('status', 'booked')->lockForUpdate()->get();

            foreach ($bookings as $booking) {
                $creditService->creditBooking(
                    $booking,
                    'sesi_dibatalkan',
                    'Kredit otomatis karena jadwal dibatalkan oleh administrator.'
                );
            }

            $lockedSession->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Jadwal dibatalkan. Booking aktif otomatis dikonversi menjadi kredit sesi.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->validateFilters($request);

        $filename = 'jadwal-pengajaran-'.now()->format('Ymd-His').'.csv';

        $rows = $this->filteredQuery($request)
            ->with([
                'learningPath' => fn ($query) => $query->withTrashed(),
                'instructor',
            ])
            ->withCount([
                'bookings as booked_count' => fn ($bookingQuery) => $bookingQuery
                    ->where('status', 'booked'),
            ])
            ->orderBy('starts_at')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Tanggal',
                'Jam Mulai',
                'Jam Selesai',
                'Kelas',
                'Pengajar',
                'Judul Sesi',
                'Status',
                'Peserta Booking',
                'Kapasitas',
                'Keterisian (%)',
                'Lokasi',
                'Tautan Lokasi',
            ], ';', '"', '');

            foreach ($rows as $session) {
                $occupancy = $session->capacity > 0
                    ? round(($session->booked_count / $session->capacity) * 100, 1)
                    : 0;

                fputcsv($handle, [
                    $session->starts_at?->format('Y-m-d'),
                    $session->starts_at?->format('H:i'),
                    $session->ends_at?->format('H:i'),
                    $session->learningPath?->title ?? 'Kelas tidak tersedia',
                    $session->instructor?->name ?? 'Pengajar tidak tersedia',
                    $session->title,
                    $session->status,
                    $session->booked_count,
                    $session->capacity,
                    $occupancy,
                    $session->location,
                    $session->meeting_url,
                ], ';', '"', '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $query = LiveSession::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->q);

            $query->where(function ($builder) use ($q) {
                $builder
                    ->where('title', 'like', "%{$q}%")
                    ->orWhereHas(
                        'learningPath',
                        fn ($courseQuery) => $courseQuery
                            ->where('title', 'like', "%{$q}%")
                    )
                    ->orWhereHas(
                        'instructor',
                        fn ($instructorQuery) => $instructorQuery
                            ->where('name', 'like', "%{$q}%")
                    );
            });
        }

        if ($request->filled('course_id')) {
            $query->where('learning_path_id', $request->integer('course_id'));
        }

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->integer('instructor_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('starts_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('starts_at', '<=', $request->date_to);
        }

        if ($request->period === 'today') {
            $query->whereDate('starts_at', today());
        } elseif ($request->period === 'upcoming') {
            $query->where('starts_at', '>=', now());
        } elseif ($request->period === 'past') {
            $query->where('ends_at', '<', now());
        }

        return $query;
    }

    private function validateSchedule(Request $request): array
    {
        return $request->validate([
            'learning_path_id' => ['required', 'integer', 'exists:learning_paths,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_url' => ['nullable', 'url', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:scheduled,live,completed,cancelled'],
            'recording_url' => ['nullable', 'url', 'max:255'],
        ]);
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'course_id' => ['nullable', 'integer', 'exists:learning_paths,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:scheduled,live,completed,cancelled'],
            'period' => ['nullable', 'in:today,upcoming,past'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);
    }

    private function ensureNoInstructorConflict(
        int $instructorId,
        string $startsAt,
        string $endsAt,
        ?int $ignoreSessionId = null
    ): void {
        $conflict = LiveSession::query()
            ->where('instructor_id', $instructorId)
            ->whereNotIn('status', ['cancelled'])
            ->when(
                $ignoreSessionId,
                fn ($query) => $query->whereKeyNot($ignoreSessionId)
            )
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'Pengajar sudah memiliki jadwal yang bertabrakan: '
                    .$conflict->title
                    .' ('
                    .$conflict->starts_at->format('d M Y H:i')
                    .'–'
                    .$conflict->ends_at->format('H:i')
                    .').',
            ]);
        }
    }
}
