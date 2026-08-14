<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Category::CORE_CATEGORIES as $slug => $data) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'icon' => $data['icon'],
                    'description' => $data['description'],
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::table('categories')
            ->whereNotIn('slug', array_keys(Category::CORE_CATEGORIES))
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now(), 'updated_at' => now()]);

        DB::table('learning_paths')->whereIn('level', ['Pemula', 'pemula', 'Basic'])->update(['level' => 'Beginner']);
        DB::table('learning_paths')->whereIn('level', ['Menengah', 'menengah', 'Medium'])->update(['level' => 'Intermediate']);
        DB::table('learning_paths')->whereIn('level', ['Lanjutan', 'lanjutan', 'Advanced'])->update(['level' => 'Expert']);

        DB::table('learning_paths')
            ->whereNotIn('level', ['Beginner', 'Intermediate', 'Expert'])
            ->update(['level' => 'Beginner']);

        DB::table('learning_paths')->update(['course_type' => 'offline']);
    }

    public function down(): void
    {
        // Data taxonomy intentionally remains valid when rolling back.
    }
};
