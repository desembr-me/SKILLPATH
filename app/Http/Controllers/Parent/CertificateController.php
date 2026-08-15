<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Certificate; use Illuminate\Http\Request;
class CertificateController extends Controller { public function show(Request $r,Certificate $certificate){$certificate->load(['enrollment.child','enrollment.course.category','enrollment.course.instructor','examAttempt.exam']);abort_unless($certificate->enrollment->parent_id===$r->user()->id,403);return view('parent.certificate',compact('certificate'));} }
