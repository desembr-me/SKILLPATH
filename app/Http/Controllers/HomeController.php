<?php
namespace App\Http\Controllers;
use App\Models\Category; use App\Models\Course;
class HomeController extends Controller { public function index(){return view('home',['categories'=>Category::all(),'courses'=>Course::with(['category','instructor','schedules'])->where('status','active')->orderByDesc('is_featured')->limit(9)->get()]);} }
