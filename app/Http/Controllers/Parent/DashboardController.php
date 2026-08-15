<?php
namespace App\Http\Controllers\Parent;
use App\Http\Controllers\Controller; use Illuminate\Http\Request;
class DashboardController extends Controller { public function __invoke(Request $r){$u=$r->user();return view('parent.dashboard',['children'=>$u->children()->with(['enrollments.course','credits'])->get(),'transactions'=>$u->transactions()->latest()->limit(5)->get()]);} }
