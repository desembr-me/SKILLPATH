<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(
        Request $request,
        LearningPath $learningPath,
        CertificateService $certificateService
    ) {
        $child = $request->user()->childProfile;

        abort_unless($child, 403);
        abort_unless($learningPath->certificate_enabled, 404);

        abort_unless(
            Enrollment::query()
                ->where('child_profile_id', $child->id)
                ->where('learning_path_id', $learningPath->id)
                ->where('status', 'active')
                ->exists(),
            403
        );

        $existing = Certificate::query()
            ->where('child_profile_id', $child->id)
            ->where('learning_path_id', $learningPath->id)
            ->first();

        abort_if(
            $existing?->isRevoked(),
            403,
            'Sertifikat ini telah dicabut oleh administrator.'
        );

        $certificate = $existing
            ?? $certificateService->issue($child, $learningPath);

        $learningPath->load(
            'classSessions',
            'instructor.instructorProfile',
            'categories'
        );

        return view('certificates.show', compact(
            'certificate',
            'child',
            'learningPath'
        ));
    }
}
