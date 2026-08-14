<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderedCore(
            Category::query()
                ->whereIn('slug', Category::coreSlugs())
                ->withCount('learningPaths')
                ->get()
        );

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        return back()->with('error', 'Kategori SKILLPATH dikunci menjadi 6 kategori utama. Tambahkan course ke kategori yang sudah tersedia.');
    }

    public function destroy(Category $category)
    {
        return back()->with('error', 'Enam kategori utama SKILLPATH tidak dapat dihapus.');
    }
}
