<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use App\Models\User; use Illuminate\Http\Request;
class DashboardController extends Controller { public function __invoke(Request $r){$u=$r->user();return view('parent.dashboard',['children'=>$u->children()->with(['enrollments.course','enrollments.certificate'])->get(),'transactions'=>$u->transactions()->with(['enrollment.course','enrollment.child'])->latest()->limit(5)->get(),'reviewable'=>$u->enrollments()->whereIn('status',['active','completed'])->whereDoesntHave('review')->with(['course','child'])->latest()->get(),'mentors'=>User::where('role','mentor')->with('category')->limit(3)->get()]);} }
