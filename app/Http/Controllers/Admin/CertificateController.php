<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Certificate::with(['enrollment.child', 'enrollment.course.instructor', 'enrollment.examAttempt'])->latest();

        if ($search) {
            $query->where('certificate_no', 'like', "%{$search}%")
                  ->orWhereHas('enrollment.child', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
        }

        $certificates = $query->paginate(12)->withQueryString();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'search' => $search,
            'totalCount' => Certificate::count(),
        ]);
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['enrollment.child', 'enrollment.course.category', 'enrollment.course.instructor', 'examAttempt.exam']);
        return view('parent.certificate', compact('certificate'));
    }
}
