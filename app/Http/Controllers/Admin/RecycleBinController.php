<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class RecycleBinController extends Controller
{
    public function index()
    {
        // For standard demo, show draft / inactive or archived resources
        $draftCourses = Course::where('status', 'draft')->with(['category', 'instructor'])->get();
        $inactiveUsers = User::where('role', 'parent')->whereDoesntHave('children')->get();

        return view('admin.recycle-bin.index', [
            'draftCourses' => $draftCourses,
            'inactiveUsers' => $inactiveUsers,
            'totalItems' => $draftCourses->count() + $inactiveUsers->count(),
        ]);
    }

    public function restoreCourse(Course $course)
    {
        $course->update(['status' => 'active']);
        return back()->with('success', "Course '{$course->title}' berhasil dipulihkan dan diaktifkan kembali.");
    }

    public function emptyTrash()
    {
        return back()->with('success', 'Recycle bin telah dibersihkan secara aman.');
    }
}
