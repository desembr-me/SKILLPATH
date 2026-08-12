<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPaths = LearningPath::query()
            ->where('is_published', true)
            ->with('skill')
            ->orderBy('id')
            ->take(6)
            ->get();

        return view('home', compact('featuredPaths'));
    }
}
