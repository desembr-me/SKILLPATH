<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\Child; use App\Services\LearningPathService; use Illuminate\Http\Request;
class LearningPathController extends Controller { public function show(Request $r, Child $child, LearningPathService $svc){abort_unless($child->parent_id===$r->user()->id,403);$path=$child->learningPaths()->with('items.course.category')->where('status','active')->latest()->first() ?: $svc->regenerate($child);return view('parent.learning-path',compact('child','path'));} }
