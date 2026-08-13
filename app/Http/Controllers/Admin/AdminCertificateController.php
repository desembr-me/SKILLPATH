<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\ChildProfile;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCertificateController extends Controller
{
    public function index(
        Request $request,
        CertificateService $certificateService
    ) {
        $this->validateFilters($request);

        $query = $this->filteredQuery($request);

        $certificates = $query
            ->latest('issued_at')
            ->paginate(15)
            ->withQueryString();

        $eligible = $this->eligibleEnrollments($certificateService);

        $base = Certificate::query();

        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)
                ->where('status', 'active')
                ->count(),
            'revoked' => (clone $base)
                ->where('status', 'revoked')
                ->count(),
            'issued_this_month' => (clone $base)
                ->whereBetween('issued_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->count(),
            'eligible' => $eligible->count(),
        ];

        $courses = LearningPath::withTrashed()
            ->where('certificate_enabled', true)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.certificates.index', compact(
            'certificates',
            'stats',
            'courses'
        ));
    }

    public function create(CertificateService $certificateService)
    {
        $eligible = $this->eligibleEnrollments($certificateService);

        return view('admin.certificates.create', compact('eligible'));
    }

    public function store(
        Request $request,
        CertificateService $certificateService
    ) {
        $data = $request->validate([
            'child_profile_id' => [
                'required',
                'integer',
                'exists:child_profiles,id',
            ],
            'learning_path_id' => [
                'required',
                'integer',
                'exists:learning_paths,id',
            ],
        ]);

        $child = ChildProfile::findOrFail($data['child_profile_id']);
        $course = LearningPath::findOrFail($data['learning_path_id']);

        $existing = Certificate::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $course->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.certificates.show', $existing)
                ->withErrors([
                    'certificate' => 'Sertifikat untuk siswa dan course tersebut sudah ada.',
                ]);
        }

        $certificate = $certificateService->issue(
            $child,
            $course,
            $request->user()
        );

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load([
            'childProfile.user',
            'learningPath.instructor',
            'learningPath.categories',
            'issuedBy',
        ]);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function revoke(Request $request, Certificate $certificate)
    {
        $data = $request->validate([
            'revoked_reason' => [
                'required',
                'string',
                'min:5',
                'max:500',
            ],
        ]);

        if ($certificate->isRevoked()) {
            return back()->withErrors([
                'certificate' => 'Sertifikat sudah berstatus dicabut.',
            ]);
        }

        $certificate->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_reason' => $data['revoked_reason'],
        ]);

        return back()->with('success', 'Sertifikat berhasil dicabut.');
    }

    public function reactivate(Certificate $certificate)
    {
        if ($certificate->isActive()) {
            return back()->withErrors([
                'certificate' => 'Sertifikat sudah aktif.',
            ]);
        }

        $certificate->update([
            'status' => 'active',
            'revoked_at' => null,
            'revoked_reason' => null,
        ]);

        return back()->with('success', 'Sertifikat berhasil diaktifkan kembali.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->validateFilters($request);

        $rows = $this->filteredQuery($request)
            ->latest('issued_at')
            ->get();

        $filename = 'sertifikat-skillpath-'
            .now()->format('Y-m-d-His')
            .'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nomor Sertifikat',
                'Nama Siswa',
                'Usia',
                'Orang Tua',
                'Course',
                'Nilai Akhir',
                'Tanggal Terbit',
                'Status',
                'Diterbitkan Oleh',
                'Tanggal Dicabut',
                'Alasan Pencabutan',
            ], ';', '"', '');

            foreach ($rows as $certificate) {
                fputcsv($handle, [
                    $certificate->certificate_number,
                    $certificate->childProfile?->name,
                    $certificate->childProfile?->age,
                    $certificate->childProfile?->user?->name,
                    $certificate->learningPath?->title,
                    $certificate->final_score ?? '-',
                    $certificate->issued_at?->format('Y-m-d H:i'),
                    $certificate->status,
                    $certificate->issuedBy?->name ?? 'Otomatis oleh sistem',
                    $certificate->revoked_at?->format('Y-m-d H:i') ?? '-',
                    $certificate->revoked_reason ?? '-',
                ], ';', '"', '');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $query = Certificate::query()
            ->with([
                'childProfile.user',
                'learningPath.instructor',
                'issuedBy',
            ]);

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($certificateQuery) use ($search) {
                $certificateQuery
                    ->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'childProfile',
                        function ($childQuery) use ($search) {
                            $childQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery
                                            ->where('name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%");
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'learningPath',
                        fn ($courseQuery) => $courseQuery
                            ->where('title', 'like', "%{$search}%")
                    );
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($courseId = $request->integer('course_id')) {
            $query->where('learning_path_id', $courseId);
        }

        if ($request->filled('from')) {
            $query->where(
                'issued_at',
                '>=',
                $request->date('from')->startOfDay()
            );
        }

        if ($request->filled('to')) {
            $query->where(
                'issued_at',
                '<=',
                $request->date('to')->endOfDay()
            );
        }

        return $query;
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,revoked'],
            'course_id' => [
                'nullable',
                'integer',
                'exists:learning_paths,id',
            ],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
    }

    private function eligibleEnrollments(
        CertificateService $certificateService
    ) {
        $existingKeys = Certificate::query()
            ->get(['child_profile_id', 'learning_path_id'])
            ->mapWithKeys(fn ($certificate) => [
                $certificate->child_profile_id
                    .'-'
                    .$certificate->learning_path_id => true,
            ]);

        return Enrollment::query()
            ->with([
                'childProfile.user',
                'learningPath.instructor',
                'learningPath.modules.activities',
            ])
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas(
                'learningPath',
                fn ($query) => $query->where(
                    'certificate_enabled',
                    true
                )
            )
            ->get()
            ->filter(function ($enrollment) use (
                $certificateService,
                $existingKeys
            ) {
                if (! $enrollment->childProfile || ! $enrollment->learningPath) {
                    return false;
                }

                $key = $enrollment->child_profile_id
                    .'-'
                    .$enrollment->learning_path_id;

                if ($existingKeys->has($key)) {
                    return false;
                }

                return $certificateService
                    ->evaluate(
                        $enrollment->childProfile,
                        $enrollment->learningPath
                    )['eligible'];
            })
            ->map(function ($enrollment) use ($certificateService) {
                return [
                    'enrollment' => $enrollment,
                    'evaluation' => $certificateService->evaluate(
                        $enrollment->childProfile,
                        $enrollment->learningPath
                    ),
                ];
            })
            ->sortBy(
                fn ($item) => mb_strtolower(
                    $item['enrollment']->childProfile->name
                )
            )
            ->values();
    }
}
