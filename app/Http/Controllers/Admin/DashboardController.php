<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Course; use App\Models\Review; use App\Models\Transaction; use App\Models\User;
class DashboardController extends Controller {public function __invoke(){return view('admin.dashboard',['parents'=>User::where('role','parent')->count(),'mentors'=>User::where('role','mentor')->count(),'courses'=>Course::where('status','active')->count(),'transactions'=>Transaction::count(),'mentorRating'=>round((float)Review::avg('mentor_rating'),2),'platformRating'=>round((float)Review::avg('platform_rating'),2),'latest'=>Transaction::with(['parent','enrollment.course'])->latest()->limit(8)->get()]);}}
