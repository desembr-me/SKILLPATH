<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTeachingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $this->validateFilters($request);

        $schedules = $this->filteredQuery($request)
            ->with([
                'learningPath' => fn ($query) => $query->withTrashed(),
                'instructor.instructorProfile',
            ])
            ->withCount([
                'bookings as booked_count' => fn ($query) => $query->whereIn('status', ['booked','attended']),
                'bookings as attended_count' => fn ($query) => $query->where('status', 'attended'),
            ])
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        $occupancyRows = ClassSession::query()
            ->withCount(['bookings as booked_count' => fn ($query) => $query->whereIn('status', ['booked','attended'])])
            ->where('status', 'scheduled')
            ->where('starts_at', '>=', now()->startOfDay())
            ->get();

        $avgOccupancy = $occupancyRows->isNotEmpty()
            ? round((float) $occupancyRows->avg(fn ($session) => $session->capacity > 0
                ? min(100, ($session->booked_count / $session->capacity) * 100)
                : 0), 1)
            : 0;

        $stats = [
            'today' => ClassSession::whereDate('starts_at', today())->where('status', '!=', 'cancelled')->count(),
            'upcoming' => ClassSession::where('starts_at', '>=', now())->where('status', 'scheduled')->count(),
            'ongoing' => ClassSession::where('status', 'scheduled')->where('starts_at', '<=', now())->where('ends_at', '>=', now())->count(),
            'completed_month' => ClassSession::where('status', 'completed')
                ->whereBetween('ends_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'avg_occupancy' => $avgOccupancy,
        ];

        $instructors = User::where('role', 'instructor')->orderBy('name')->get(['id','name']);
        $courses = LearningPath::with('instructor')->orderBy('title')->get(['id','title','instructor_id']);

        return view('admin.schedules.index', compact('schedules','stats','instructors','courses'));
    }

    public function create()
    {
        $courses = LearningPath::where('is_published', true)
            ->whereNotNull('instructor_id')
            ->with('instructor')
            ->orderBy('title')
            ->get();
        return view('admin.schedules.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);
        $course = LearningPath::findOrFail($data['learning_path_id']);
        abort_unless($course->instructor_id, 422, 'Kelas belum memiliki pengajar.');

        $this->ensureNoInstructorConflict($course->instructor_id, $data['starts_at'], $data['ends_at']);

        ClassSession::create($data + ['instructor_id' => $course->instructor_id]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kelas offline berhasil dibuat.');
    }

    public function edit(ClassSession $classSession)
    {
        $classSession->load([
            'learningPath' => fn ($query) => $query->withTrashed(),
            'instructor',
        ]);
        $courses = LearningPath::withTrashed()->whereNotNull('instructor_id')->with('instructor')->orderBy('title')->get();
        return view('admin.schedules.edit', compact('classSession','courses'));
    }

    public function update(Request $request, ClassSession $classSession)
    {
        $data = $this->validateSchedule($request);
        $course = LearningPath::withTrashed()->findOrFail($data['learning_path_id']);
        abort_unless($course->instructor_id, 422, 'Kelas belum memiliki pengajar.');

        $this->ensureNoInstructorConflict($course->instructor_id, $data['starts_at'], $data['ends_at'], $classSession->id);
        $classSession->update($data + ['instructor_id' => $course->instructor_id]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kelas offline berhasil diperbarui.');
    }

    public function cancel(ClassSession $classSession)
    {
        if ($classSession->status === 'completed') {
            return back()->withErrors(['schedule' => 'Jadwal yang sudah selesai tidak dapat dibatalkan.']);
        }
        $classSession->update(['status' => 'cancelled']);
        $classSession->bookings()->where('status', 'booked')->update(['status' => 'cancelled']);
        return back()->with('success', 'Jadwal kelas dibatalkan.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->validateFilters($request);
        $rows = $this->filteredQuery($request)
            ->with(['learningPath' => fn ($query) => $query->withTrashed(), 'instructor'])
            ->withCount([
                'bookings as booked_count' => fn ($query) => $query->whereIn('status', ['booked','attended']),
                'bookings as attended_count' => fn ($query) => $query->where('status', 'attended'),
            ])
            ->orderBy('starts_at')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Tanggal','Jam Mulai','Jam Selesai','Kelas','Pengajar','Judul Sesi','Lokasi','Alamat','Ruangan',
                'Status','Kursi Terisi','Hadir','Kapasitas','Keterisian (%)'
            ], ';', '"', '');

            foreach ($rows as $session) {
                $occupancy = $session->capacity > 0 ? round(($session->booked_count / $session->capacity) * 100, 1) : 0;
                fputcsv($handle, [
                    $session->starts_at?->format('Y-m-d'),
                    $session->starts_at?->format('H:i'),
                    $session->ends_at?->format('H:i'),
                    $session->learningPath?->title ?? 'Kelas tidak tersedia',
                    $session->instructor?->name ?? 'Pengajar tidak tersedia',
                    $session->title,
                    $session->venue_name,
                    $session->address,
                    $session->room ?? '-',
                    $session->status,
                    $session->booked_count,
                    $session->attended_count,
                    $session->capacity,
                    $occupancy,
                ], ';', '"', '');
            }
            fclose($handle);
        }, 'jadwal-kelas-offline-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filteredQuery(Request $request)
    {
        $query = ClassSession::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('venue_name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhereHas('learningPath', fn ($courseQuery) => $courseQuery->where('title', 'like', "%{$q}%"))
                    ->orWhereHas('instructor', fn ($instructorQuery) => $instructorQuery->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('course_id')) $query->where('learning_path_id', $request->integer('course_id'));
        if ($request->filled('instructor_id')) $query->where('instructor_id', $request->integer('instructor_id'));
        if ($request->status === 'live') {
            $query->where('status', 'scheduled')->where('starts_at', '<=', now())->where('ends_at', '>=', now());
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) $query->whereDate('starts_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('starts_at', '<=', $request->date_to);

        if ($request->period === 'today') $query->whereDate('starts_at', today());
        elseif ($request->period === 'upcoming') $query->where('starts_at', '>=', now());
        elseif ($request->period === 'past') $query->where('ends_at', '<', now());

        return $query;
    }

    private function validateSchedule(Request $request): array
    {
        $data = $request->validate([
            'learning_path_id' => ['required','integer','exists:learning_paths,id'],
            'title' => ['required','string','max:150'],
            'description' => ['nullable','string','max:2000'],
            'starts_at' => ['required','date'],
            'ends_at' => ['required','date','after:starts_at'],
            'venue_name' => ['required','string','max:180'],
            'address' => ['required','string','max:1000'],
            'room' => ['nullable','string','max:100'],
            'map_url' => ['nullable','url','max:255'],
            'capacity' => ['required','integer','min:1','max:500'],
            'status' => ['required','in:scheduled,live,completed,cancelled'],
            'preparation_notes' => ['nullable','string','max:2000'],
        ]);

        if (($data['status'] ?? null) === 'live') {
            $data['status'] = 'scheduled';
        }

        return $data;
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'q'=>['nullable','string','max:120'],
            'course_id'=>['nullable','integer','exists:learning_paths,id'],
            'instructor_id'=>['nullable','integer','exists:users,id'],
            'status'=>['nullable','in:scheduled,live,completed,cancelled'],
            'period'=>['nullable','in:today,upcoming,past'],
            'date_from'=>['nullable','date_format:Y-m-d'],
            'date_to'=>['nullable','date_format:Y-m-d'],
        ]);
    }

    private function ensureNoInstructorConflict(int $instructorId, string $startsAt, string $endsAt, ?int $ignoreSessionId = null): void
    {
        $conflict = ClassSession::query()
            ->where('instructor_id', $instructorId)
            ->whereNotIn('status', ['cancelled'])
            ->when($ignoreSessionId, fn ($query) => $query->whereKeyNot($ignoreSessionId))
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'Pengajar sudah memiliki jadwal yang bertabrakan: '.$conflict->title.' ('.$conflict->starts_at->format('d M Y H:i').'–'.$conflict->ends_at->format('H:i').').',
            ]);
        }
    }
}
